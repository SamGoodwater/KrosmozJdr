<?php

namespace App\Http\Controllers\Api;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

/**
 * Aperçu HTML contrôlé d'une section CMS (popover mentions).
 */
class CmsSectionPreviewController extends Controller
{
    private const MAX_HTML_LENGTH = 4000;

    /** Nombre max. de blocs (paragraphes, items de liste, titres…) dans l’aperçu popover (Phase D, ~10 lignes). */
    private const EXCERPT_MAX_BLOCKS = 10;

    /** Filet de sécurité si le HTML ne se découpe pas proprement. */
    private const EXCERPT_MAX_LEN = 2400;

    /** Tags comptés comme une « ligne » d’aperçu. */
    private const BLOCK_TAGS = ['p', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre'];

    public function show(Section $section): JsonResponse
    {
        $this->authorize('view', $section);

        return response()->json($this->buildPreviewBody($section, false));
    }

    /**
     * Aperçu par couple page / section (références {@code pageSlug@sectionSlug}).
     * Corps HTML réservé aux sections de type {@link SectionType::TEXT} (extrait léger).
     */
    public function showByQuery(Request $request): JsonResponse
    {
        $pageSlug = trim((string) $request->query('page_slug', ''));
        $sectionSlug = trim((string) $request->query('section_slug', ''));
        if ($pageSlug === '' || $sectionSlug === '') {
            return response()->json(['message' => 'page_slug et section_slug requis.'], 422);
        }

        $page = Page::query()->where('slug', $pageSlug)->first();
        if ($page === null) {
            abort(404);
        }
        $this->authorize('view', $page);

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('slug', $sectionSlug)
            ->first();
        if ($section === null) {
            abort(404);
        }
        $this->authorize('view', $section);

        return response()->json($this->buildPreviewBody($section, true));
    }

    /**
     * @return array{canView: true, title: mixed, html: string, textPreviewOnly?: bool}
     */
    private function buildPreviewBody(Section $section, bool $textTemplateOnly): array
    {
        $template = $this->sectionTypeValue($section->template ?? $section->type ?? null);
        if ($textTemplateOnly && $template !== SectionType::TEXT->value) {
            return [
                'canView' => true,
                'title' => $section->title,
                'html' => '',
                'textPreviewOnly' => true,
            ];
        }

        $raw = (string) ($section->data['content'] ?? '');
        if ($raw === '') {
            return [
                'canView' => true,
                'title' => $section->title,
                'html' => '',
            ];
        }

        $clean = Purifier::clean($raw, 'section_text');
        if (strlen($clean) > self::MAX_HTML_LENGTH) {
            $clean = Str::limit($clean, self::MAX_HTML_LENGTH, '…');
        }

        $excerpt = $this->excerptForPopover($clean, (string) ($section->title ?? ''));

        return [
            'canView' => true,
            'title' => $section->title,
            'html' => $excerpt,
        ];
    }

    /**
     * Retire un titre dupliqué en tête du HTML puis tronque pour un aperçu léger.
     */
    private function excerptForPopover(string $clean, string $sectionTitle): string
    {
        $titleNorm = mb_strtolower(trim(html_entity_decode(strip_tags($sectionTitle), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if ($titleNorm !== '') {
            $clean = (string) preg_replace_callback(
                '/^\s*<h[1-4][^>]*>(.*?)<\/h[1-4]>\s*/isu',
                static function (array $m) use ($titleNorm): string {
                    $inner = mb_strtolower(trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($m[1] ?? '')))));

                    return $inner === $titleNorm ? '' : (string) $m[0];
                },
                $clean,
                1
            );
        }

        return $this->truncateHtmlAfterBlocks($clean, self::EXCERPT_MAX_BLOCKS);
    }

    /**
     * Tronque le HTML après N blocs en ordre document, sans couper au milieu d’un bloc.
     */
    private function truncateHtmlAfterBlocks(string $html, int $maxBlocks): string
    {
        $html = trim($html);
        if ($html === '' || $maxBlocks < 1) {
            return '';
        }

        $wrapped = '<div id="_kref_excerpt_root">'.$html.'</div>';

        $previous = libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $loaded = $doc->loadHTML(
            '<?xml encoding="UTF-8"?>'.$wrapped,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return Str::limit($html, self::EXCERPT_MAX_LEN, '…');
        }

        $root = $doc->getElementById('_kref_excerpt_root');
        if ($root === null) {
            return Str::limit($html, self::EXCERPT_MAX_LEN, '…');
        }

        $blocks = [];
        $this->collectBlockNodesInOrder($root, $blocks);

        if (count($blocks) <= $maxBlocks) {
            $out = $this->innerHtmlFromNode($root, $doc);

            return $this->finalizeExcerpt($out, $html, false);
        }

        for ($i = $maxBlocks; $i < count($blocks); $i++) {
            $node = $blocks[$i];
            $node->parentNode?->removeChild($node);
        }

        $this->removeEmptyListContainers($root);

        $out = $this->innerHtmlFromNode($root, $doc);

        return $this->finalizeExcerpt($out, $html, true);
    }

    /**
     * @param  list<DOMElement>  $blocks
     */
    private function collectBlockNodesInOrder(DOMNode $node, array &$blocks): void
    {
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }

        /** @var DOMElement $node */
        $tag = strtolower($node->nodeName);
        if (in_array($tag, self::BLOCK_TAGS, true)) {
            $blocks[] = $node;

            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->collectBlockNodesInOrder($child, $blocks);
        }
    }

    private function removeEmptyListContainers(DOMElement $root): void
    {
        foreach (['ul', 'ol'] as $listTag) {
            $lists = $root->getElementsByTagName($listTag);
            for ($i = $lists->length - 1; $i >= 0; $i--) {
                $list = $lists->item($i);
                if (! ($list instanceof DOMElement)) {
                    continue;
                }
                $hasLi = false;
                foreach ($list->childNodes as $child) {
                    if ($child->nodeType === XML_ELEMENT_NODE && strtolower($child->nodeName) === 'li') {
                        $hasLi = true;
                        break;
                    }
                }
                if (! $hasLi) {
                    $list->parentNode?->removeChild($list);
                }
            }
        }
    }

    private function innerHtmlFromNode(DOMElement $root, DOMDocument $doc): string
    {
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private function finalizeExcerpt(string $out, string $fallbackHtml, bool $truncated): string
    {
        if ($truncated && $out !== '') {
            $out = rtrim($out).'…';
        }

        if ($out === '' || strlen($out) > self::EXCERPT_MAX_LEN) {
            return Str::limit($out !== '' ? $out : strip_tags($fallbackHtml), self::EXCERPT_MAX_LEN, '…');
        }

        return $out;
    }

    private function sectionTypeValue(mixed $type): string
    {
        if ($type instanceof SectionType) {
            return $type->value;
        }

        return is_string($type) ? $type : '';
    }
}

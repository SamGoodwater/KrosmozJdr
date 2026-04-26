<?php

namespace App\Http\Controllers\Api;

use App\Enums\SectionType;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
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

    /** Longueur max. de l’extrait affiché dans le popover (style aperçu Wikipédia). */
    private const EXCERPT_MAX_LEN = 1200;

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
        $template = (string) ($section->template ?? $section->type ?? '');
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

        if (strlen($clean) > self::EXCERPT_MAX_LEN) {
            return Str::limit($clean, self::EXCERPT_MAX_LEN, '…');
        }

        return $clean;
    }
}

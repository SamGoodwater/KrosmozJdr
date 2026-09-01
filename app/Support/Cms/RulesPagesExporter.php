<?php

declare(strict_types=1);

namespace App\Support\Cms;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Support\Str;

/**
 * Exporte les pages/sections règles CMS vers les fichiers Markdown du dépôt.
 *
 * La base reste la source de vérité du contenu. La TOC et les chemins fichiers
 * existants conservent la structure du livre.
 *
 * @example
 * $result = (new RulesPagesExporter)->export(base_path('private/game/rules/TABLE_DES_MATIERES.md'), dryRun: true);
 */
final class RulesPagesExporter
{
    /**
     * @return array{
     *     written: int,
     *     unchanged: int,
     *     skipped: int,
     *     missing_page: int,
     *     missing_file: int,
     *     files: list<string>,
     *     warnings: list<string>
     * }
     */
    public function export(string $tocPath, bool $dryRun = false, bool $createMissing = false): array
    {
        $result = [
            'written' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'missing_page' => 0,
            'missing_file' => 0,
            'files' => [],
            'warnings' => [],
        ];

        $tree = RulesTocParser::parse($tocPath);
        if ($tree === []) {
            $result['warnings'][] = 'Aucune hiérarchie détectée dans la table des matières.';

            return $result;
        }

        $rulesRoot = dirname($tocPath);
        $filesByNumber = RulesMarkdownFileIndex::numberedFiles($rulesRoot);

        foreach ($tree as $level1) {
            foreach ($level1['children'] as $level2) {
                $pageSlug = RulesImportSlugHelper::buildPageSlug($level2['number'], $level2['title']);
                $page = Page::query()
                    ->where('slug', $pageSlug)
                    ->with(['sections' => static fn ($query) => $query->orderBy('order')])
                    ->first();

                foreach ($level2['sections'] as $level3) {
                    $number = trim((string) $level3['number']);
                    $title = trim((string) $level3['title']);
                    if ($number === '') {
                        continue;
                    }

                    if ($page === null) {
                        $result['missing_page']++;
                        $result['warnings'][] = "Page CMS introuvable pour {$number} (slug {$pageSlug}).";
                        continue;
                    }

                    $chunks = $this->chunksForLevel3($page, $number, $title);
                    if ($chunks === []) {
                        $result['skipped']++;
                        $result['warnings'][] = "Aucune section CMS pour {$number} — {$title}.";
                        continue;
                    }

                    $path = $filesByNumber[$number] ?? null;
                    if ($path === null) {
                        if (! $createMissing) {
                            $result['missing_file']++;
                            $result['warnings'][] = "Fichier Markdown introuvable pour {$number} — {$title}.";
                            continue;
                        }
                        $path = $this->buildMissingPath($rulesRoot, $level1, $level2, $number, $title);
                    }

                    $existing = is_file($path) ? (string) file_get_contents($path) : '';
                    $markdown = RulesMarkdownFileAssembler::assemble($number, $title, $chunks, $existing);

                    if ($this->sameMarkdown($existing, $markdown)) {
                        $result['unchanged']++;
                        continue;
                    }

                    $result['written']++;
                    $result['files'][] = $path;

                    if (! $dryRun) {
                        $directory = dirname($path);
                        if (! is_dir($directory)) {
                            mkdir($directory, 0775, true);
                        }
                        file_put_contents($path, $markdown);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return list<array{title: string, html: string}>
     */
    private function chunksForLevel3(Page $page, string $number, string $title): array
    {
        $baseSlug = RulesImportSlugHelper::buildSectionSlug($number, $title);
        $chunks = [];

        foreach ($page->sections as $section) {
            if (! $section instanceof Section) {
                continue;
            }
            if ($section->template !== SectionType::TEXT) {
                continue;
            }
            $slug = trim((string) $section->slug);
            if ($slug !== $baseSlug && ! str_starts_with($slug, $baseSlug.'-')) {
                continue;
            }

            $chunks[] = [
                'title' => trim((string) ($section->title ?: $title)),
                'html' => $this->sectionHtml($section),
            ];
        }

        return $chunks;
    }

    private function sectionHtml(Section $section): string
    {
        $data = is_array($section->data) ? $section->data : [];
        $params = is_array($section->params) ? $section->params : [];
        $content = $data['content'] ?? $params['content'] ?? '';

        return is_string($content) ? $content : '';
    }

    /**
     * @param  array{number: string, title: string}  $level1
     * @param  array{number: string, title: string}  $level2
     */
    private function buildMissingPath(
        string $rulesRoot,
        array $level1,
        array $level2,
        string $number,
        string $title,
    ): string {
        $l1Dir = trim($level1['number']).'-'.Str::slug($level1['title']);
        $l2Dir = trim($level2['number']).'-'.Str::slug($level2['title']);
        $basename = $number.'-'.Str::slug($title).'.md';

        return $rulesRoot.DIRECTORY_SEPARATOR.$l1Dir.DIRECTORY_SEPARATOR.$l2Dir.DIRECTORY_SEPARATOR.$basename;
    }

    private function sameMarkdown(string $left, string $right): bool
    {
        return $this->normalizeForCompare($left) === $this->normalizeForCompare($right);
    }

    private function normalizeForCompare(string $markdown): string
    {
        $markdown = str_replace("\r\n", "\n", $markdown);

        return trim($markdown);
    }
}

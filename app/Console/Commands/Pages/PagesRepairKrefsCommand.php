<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Models\Section;
use App\Support\Cms\KrefShortcodeReplacer;
use App\Support\Cms\RulesHtmlSectionSplitter;
use App\Support\Cms\RulesImportSlugHelper;
use App\Support\Cms\RulesMarkdownSectionCompiler;
use App\Support\Cms\RulesTocParser;
use App\Support\Cms\RulesTocSlugIndex;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Répare les krefs CMS : shortcodes laissés en clair, et sections règles cassées par un shortcode imbriqué.
 *
 * @example php artisan pages:repair-krefs --dry-run
 */
class PagesRepairKrefsCommand extends Command
{
    protected $signature = 'pages:repair-krefs
        {--dry-run : Affiche les sections concernées sans écrire}';

    protected $description = 'Convertit les shortcodes [[kref:]] restés en clair et reconstruit les sections règles au HTML cassé.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $replacer = KrefShortcodeReplacer::forEssentialPages();
        $rebuilt = 0;
        $converted = 0;
        $stillBroken = 0;

        $rulesRoot = base_path('private/game/rules');
        $tocPath = $rulesRoot.'/TABLE_DES_MATIERES.md';
        $tree = is_file($tocPath) ? RulesTocParser::parse($tocPath) : [];
        $index = $tree !== [] ? RulesTocSlugIndex::fromTree($tree) : null;
        $titleByNumber = $this->tocTitlesByNumber($tree);
        $fileByNumber = $this->markdownFilesByNumber($rulesRoot);
        $rulesRootReal = realpath($rulesRoot) ?: $rulesRoot;

        $candidates = Section::query()
            ->where(function ($query): void {
                $query->where('data', 'like', '%[[kref:%')
                    ->orWhere('data', 'like', '%</span>]]%');
            })
            ->get();

        $rebuiltNumbers = [];

        foreach ($candidates as $section) {
            $html = $this->contentOf($section);
            if (! $this->needsRepair($html)) {
                continue;
            }

            $number = $this->rulesNumberForSlug((string) $section->slug, $titleByNumber);
            if ($number !== null && isset($fileByNumber[$number], $titleByNumber[$number]) && $this->isCorruptedRulesHtml($html)) {
                if (! isset($rebuiltNumbers[$number])) {
                    $raw = file_get_contents($fileByNumber[$number]);
                    $compiled = is_string($raw)
                        ? RulesMarkdownSectionCompiler::markdownFileToHtml($raw, $fileByNumber[$number], $rulesRootReal, $index)
                        : '';
                    $chunks = $compiled !== '' ? RulesHtmlSectionSplitter::split($compiled) : [];
                    if ($chunks === [] && $compiled !== '') {
                        $chunks = [['title' => '', 'html' => $compiled]];
                    }
                    $rebuiltNumbers[$number] = RulesMarkdownSectionCompiler::assignSlugs(
                        $number,
                        $titleByNumber[$number],
                        $chunks,
                    );
                }

                $match = null;
                foreach ($rebuiltNumbers[$number] as $chunk) {
                    if ($chunk['slug'] === $section->slug) {
                        $match = $chunk;
                        break;
                    }
                }

                if ($match === null || trim($match['html']) === '') {
                    $this->warn("Section {$section->slug} : HTML règles cassé, aucun chunk recompilé.");
                    $stillBroken++;

                    continue;
                }

                $this->line(($dryRun ? '[dry-run] ' : '')."Reconstruit {$section->slug}");
                if (! $dryRun) {
                    $this->writeContent($section, $match['html']);
                }
                $rebuilt++;

                continue;
            }

            $next = $replacer->replace($html);
            if ($next === $html || str_contains($next, '[[kref:')) {
                if (str_contains($html, '[[kref:')) {
                    $this->warn("Section {$section->slug} : shortcode non résolu.");
                    $stillBroken++;
                }

                continue;
            }

            $this->line(($dryRun ? '[dry-run] ' : '')."Shortcodes convertis {$section->slug}");
            if (! $dryRun) {
                $this->writeContent($section, $next);
            }
            $converted++;
        }

        $this->info("Sections reconstruites : {$rebuilt}. Shortcodes convertis : {$converted}. Encore cassées : {$stillBroken}.");

        return $stillBroken > 0 ? ArtisanExitCode::FAILURE : ArtisanExitCode::SUCCESS;
    }

    /**
     * @param  array<int, mixed>  $tree
     * @return array<string, string>
     */
    private function tocTitlesByNumber(array $tree): array
    {
        $titles = [];
        foreach ($tree as $level1) {
            if (! is_array($level1)) {
                continue;
            }
            foreach ($level1['children'] ?? [] as $level2) {
                if (! is_array($level2)) {
                    continue;
                }
                foreach ($level2['sections'] ?? [] as $level3) {
                    if (! is_array($level3)) {
                        continue;
                    }
                    $number = trim((string) ($level3['number'] ?? ''));
                    $title = trim((string) ($level3['title'] ?? ''));
                    if ($number !== '' && $title !== '') {
                        $titles[$number] = $title;
                    }
                }
            }
        }

        return $titles;
    }

    /**
     * @return array<string, string> numéro TOC => chemin absolu
     */
    private function markdownFilesByNumber(string $rulesRoot): array
    {
        if (! is_dir($rulesRoot)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rulesRoot, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }
            $basename = $fileInfo->getBasename();
            if (! preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }
            $files[(string) $matches[1]] = (string) $fileInfo->getPathname();
        }

        return $files;
    }

    /**
     * @param  array<string, string>  $titleByNumber
     */
    private function rulesNumberForSlug(string $slug, array $titleByNumber): ?string
    {
        if (! str_starts_with($slug, 'regle-')) {
            return null;
        }

        $found = null;
        $foundLength = 0;
        foreach ($titleByNumber as $number => $title) {
            $base = RulesImportSlugHelper::buildSectionSlug($number, $title);
            $matches = $slug === $base || str_starts_with($slug, $base.'-');
            if ($matches && strlen($base) > $foundLength) {
                $found = $number;
                $foundLength = strlen($base);
            }
        }

        return $found;
    }

    private function contentOf(Section $section): string
    {
        $data = is_array($section->data) ? $section->data : [];

        return (string) ($data['content'] ?? '');
    }

    private function needsRepair(string $html): bool
    {
        return str_contains($html, '[[kref:') || str_contains($html, '</span>]]');
    }

    private function isCorruptedRulesHtml(string $html): bool
    {
        return str_contains($html, '</span>]]')
            || str_contains($html, 'Esquive [[kref')
            || str_contains($html, '&lt;/td&gt;');
    }

    private function writeContent(Section $section, string $html): void
    {
        $data = is_array($section->data) ? $section->data : [];
        $data['content'] = $html;
        $section->data = $data;

        if (is_array($section->params) && array_key_exists('content', $section->params)) {
            $params = $section->params;
            $params['content'] = $html;
            $section->params = $params;
        }

        $settings = is_array($section->settings) ? $section->settings : [];
        $settings['enableRichReferences'] = true;
        $section->settings = $settings;
        $section->save();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Concatène les chapitres Markdown du livre de règles, dans l’ordre numérique.
 *
 * Source : `private/game/rules/`. Les fichiers meta (table des matières, index,
 * guides de rédaction) sont exclus. Les shortcodes kref deviennent le libellé.
 *
 * @example
 * $markdown = (new RulesBookAssembler())->assemble();
 */
class RulesBookAssembler
{
    public function __construct(
        private readonly string $rulesRoot = '',
    ) {}

    public function root(): string
    {
        return $this->rulesRoot !== '' ? $this->rulesRoot : base_path('private/game/rules');
    }

    /**
     * Livre Markdown prêt à convertir (titre, version, chapitres).
     */
    public function assemble(): string
    {
        $version = (string) env('APP_VERSION', 'dev');
        $date = now()->timezone(config('app.timezone'))->format('d/m/Y');
        $parts = [
            '# Krosmoz JDR — Livre de règles',
            '',
            'Version '.$version.' · compilé le '.$date.'.',
            '',
            'Ce document reprend les chapitres du livre. La version à jour se lit aussi en ligne.',
            '',
        ];

        foreach ($this->chapterFiles() as $file) {
            $raw = file_get_contents($file['path']);
            if (! is_string($raw) || trim($raw) === '') {
                continue;
            }

            $parts[] = $this->normalizeChapter($raw);
            $parts[] = '';
        }

        return trim(implode(PHP_EOL, $parts)).PHP_EOL;
    }

    /**
     * HTML du livre (CommonMark), sans shortcodes kref.
     */
    public function toHtml(): string
    {
        return trim((string) Str::markdown($this->assemble(), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }

    /**
     * @return list<array{number: string, path: string}>
     */
    public function chapterFiles(): array
    {
        $root = $this->root();
        if (! is_dir($root)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $basename = pathinfo((string) $fileInfo->getPathname(), PATHINFO_BASENAME);
            if (! preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }

            $files[] = [
                'number' => (string) $matches[1],
                'path' => (string) $fileInfo->getPathname(),
            ];
        }

        usort($files, static function (array $a, array $b): int {
            return version_compare($a['number'], $b['number']);
        });

        return $files;
    }

    private function normalizeChapter(string $markdown): string
    {
        $markdown = $this->replaceKrefShortcodes($markdown);
        $markdown = $this->replaceInternalMarkdownLinks($markdown);

        return trim($markdown);
    }

    /**
     * `[[kref:type:cible|Libellé]]` → Libellé.
     */
    private function replaceKrefShortcodes(string $markdown): string
    {
        $withLabel = (string) preg_replace(
            '/\[\[kref:[^\]|]+\|([^\]]+)\]\]/u',
            '$1',
            $markdown
        );

        return (string) preg_replace('/\[\[kref:[^\]]+\]\]/u', '', $withLabel);
    }

    /**
     * Liens relatifs vers d’autres .md → texte seul. Les URL http(s) restent des liens.
     */
    private function replaceInternalMarkdownLinks(string $markdown): string
    {
        return (string) preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/u',
            static function (array $matches): string {
                $label = (string) $matches[1];
                $url = trim((string) $matches[2]);
                if (preg_match('#^https?://#i', $url) === 1) {
                    return '['.$label.']('.$url.')';
                }

                return $label;
            },
            $markdown
        );
    }
}

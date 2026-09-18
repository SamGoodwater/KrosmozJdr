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
 * Pour le PDF : un saut de page par grande partie (pas par fiche), sans blocs
 * Sources / Contenu / liens internes.
 *
 * @example
 * $markdown = (new RulesBookAssembler())->assemble();
 */
class RulesBookAssembler
{
    /** @var array<string, string> */
    private const PART_TITLES = [
        '1' => 'Introduction',
        '2' => 'Créer un personnage',
        '3' => 'Jouer',
        '4' => 'Le Monde des Douze',
        '5' => 'Ressources et équilibrage',
        '6' => 'Annexes',
    ];

    /** Historique de design / archives : utiles en repo, pas dans le livre imprimé. */
    private const SKIP_PRINT_NUMBERS = [
        '6.1.3' => true,
        '6.1.4' => true,
    ];

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

        $currentPart = '';
        foreach ($this->chapterFiles() as $file) {
            $raw = file_get_contents($file['path']);
            if (! is_string($raw) || trim($raw) === '') {
                continue;
            }

            $major = explode('.', $file['number'])[0];
            if ($major !== $currentPart) {
                $currentPart = $major;
                $title = self::PART_TITLES[$major] ?? 'Partie '.$major;
                $parts[] = '# '.$major.'. '.$title;
                $parts[] = '';
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

            $number = (string) $matches[1];
            if (isset(self::SKIP_PRINT_NUMBERS[$number])) {
                continue;
            }

            $files[] = [
                'number' => $number,
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
        $markdown = $this->stripPrintOnlyNoise($markdown);
        $markdown = $this->demoteHeadings($markdown);

        return trim($markdown);
    }

    /**
     * Un `#` de moins : les fiches passent en h2 sous le titre de partie (h1).
     */
    private function demoteHeadings(string $markdown): string
    {
        return (string) preg_replace('/^(#{1,5})(\s)/m', '#$1$2', $markdown);
    }

    /**
     * Blocs utiles en CMS / repo, redondants à l’impression.
     */
    private function stripPrintOnlyNoise(string $markdown): string
    {
        $markdown = (string) preg_replace('/\n## Sources?\b.*\z/us', "\n", $markdown);
        $markdown = (string) preg_replace(
            '/^## Contenu\s*\n(?:[-*].*\n|\s*\n)*---\s*\n/mu',
            '',
            $markdown
        );
        $markdown = (string) preg_replace(
            '/\n\*\*Pour plus de détails\*\*[^\n]*\n(?:[ \t]*-[ \t].*\n)+/u',
            "\n",
            $markdown
        );
        $markdown = (string) preg_replace('/^\*\*Description\*\*\s*:\s*/mu', '', $markdown);

        return $markdown;
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

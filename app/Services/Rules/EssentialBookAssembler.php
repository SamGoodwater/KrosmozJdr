<?php

declare(strict_types=1);

namespace App\Services\Rules;

/**
 * Assemble le PDF/ODT « L’Essentiel » depuis le seed HTML des pages aide-mémoire.
 *
 * Source : {@see database/seeders/data/essential-pages.php} (même contenu que le CMS).
 * Les shortcodes {@code [[kref:…]]} sont aplatis en libellés pour l’impression.
 *
 * @example
 * $html = (new EssentialBookAssembler())->toHtml();
 */
class EssentialBookAssembler
{
    public function __construct(
        private readonly string $pagesPath = '',
    ) {}

    public function pagesPath(): string
    {
        return $this->pagesPath !== ''
            ? $this->pagesPath
            : database_path('seeders/data/essential-pages.php');
    }

    /**
     * @return list<array{
     *   title: string,
     *   slug: string,
     *   menu_order: int,
     *   intro_title: string,
     *   intro_html: string,
     *   sections: list<array{slug: string, title: string, html: string}>
     * }>
     */
    public function pages(): array
    {
        $path = $this->pagesPath();
        if (! is_file($path)) {
            return [];
        }

        $pages = require $path;
        if (! is_array($pages)) {
            return [];
        }

        $list = [];
        foreach ($pages as $page) {
            if (! is_array($page) || ! isset($page['title'], $page['slug'])) {
                continue;
            }
            $list[] = $page;
        }

        usort($list, static fn (array $a, array $b): int => ((int) ($a['menu_order'] ?? 0)) <=> ((int) ($b['menu_order'] ?? 0)));

        return $list;
    }

    /**
     * HTML prêt pour {@see RulesPdfWriter} / {@see RulesOdtWriter}.
     */
    public function toHtml(): string
    {
        $pages = $this->pages();
        if ($pages === []) {
            return '';
        }

        $version = (string) env('APP_VERSION', 'dev');
        $date = now()->timezone(config('app.timezone'))->format('d/m/Y');
        $parts = [
            '<h1>Krosmoz JDR — L’Essentiel</h1>',
            '<p class="meta">Version '.$version.' · compilé le '.$date.'. Aide-mémoire pour la table ; le détail est dans le livre de règles.</p>',
        ];

        foreach ($pages as $page) {
            $parts[] = '<h1>'.e((string) $page['title']).'</h1>';

            $introTitle = trim((string) ($page['intro_title'] ?? ''));
            $introHtml = $this->normalizeHtml((string) ($page['intro_html'] ?? ''));
            if ($introTitle !== '') {
                $parts[] = '<h2>'.e($introTitle).'</h2>';
            }
            if ($introHtml !== '') {
                $parts[] = $introHtml;
            }

            foreach ($page['sections'] ?? [] as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $title = trim((string) ($section['title'] ?? ''));
                $html = $this->normalizeHtml((string) ($section['html'] ?? ''));
                if ($title !== '') {
                    $parts[] = '<h2>'.e($title).'</h2>';
                }
                if ($html !== '') {
                    $parts[] = $html;
                }
            }
        }

        return trim(implode("\n", $parts));
    }

    private function normalizeHtml(string $html): string
    {
        $html = $this->replaceKrefShortcodes($html);

        return trim($html);
    }

    /**
     * {@code [[kref:type:cible|Libellé]]} → Libellé.
     */
    private function replaceKrefShortcodes(string $html): string
    {
        $withLabel = (string) preg_replace(
            '/\[\[kref:[^\]|]+\|([^\]]+)\]\]/u',
            '$1',
            $html
        );

        return (string) preg_replace('/\[\[kref:[^\]]+\]\]/u', '', $withLabel);
    }
}

<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Découpe le HTML d'une section règles (import Markdown) en blocs CMS par grand titre.
 *
 * - Préambule avant le premier {@code h2} → section « Résumé »
 * - Chaque {@code h2} → section (sauf « Contenu » et « Sources »)
 * - {@code h3} « À retenir / À faire / À éviter » en fin de bloc → sections dédiées
 *
 * @example
 * RulesHtmlSectionSplitter::split('<p>Intro</p><h2>1.2.1.1. Mécanique</h2><p>…</p>');
 */
final class RulesHtmlSectionSplitter
{
    /** @var list<string> */
    private const SKIP_H2_TITLES = ['contenu', 'sources'];

    /** @var array<string, string> */
    private const META_H3_PATTERNS = [
        'À retenir' => '/<h3[^>]*>\s*À retenir\s*<\/h3>(.*?)(?=<h3[\s>]|<h2[\s>]|$)/isu',
        'À faire' => '/<h3[^>]*>\s*À faire\s*<\/h3>(.*?)(?=<h3[\s>]|<h2[\s>]|$)/isu',
        'À éviter' => '/<h3[^>]*>\s*À éviter\s*<\/h3>(.*?)(?=<h3[\s>]|<h2[\s>]|$)/isu',
    ];

    /**
     * @return list<array{title: string, html: string}>
     */
    public static function split(string $html): array
    {
        $html = trim($html);
        if ($html === '') {
            return [];
        }

        $html = self::stripSourcesSection($html);

        $parts = preg_split('/(?=<h2[\s>])/iu', $html, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $chunks = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (! preg_match('/<h2[^>]*>(.*?)<\/h2>/is', $part, $headingMatch)) {
                if (self::hasVisibleText($part)) {
                    $chunks[] = ['title' => 'Résumé', 'html' => $part];
                }

                continue;
            }

            $title = self::normalizeHeadingTitle(
                html_entity_decode(strip_tags((string) $headingMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            );

            if (self::shouldSkipH2Title($title)) {
                continue;
            }

            $body = trim((string) preg_replace('/<h2[^>]*>.*?<\/h2>/is', '', $part, 1));
            $chunks[] = ['title' => $title, 'html' => $body];
        }

        return self::peelMetaH3Sections($chunks);
    }

    private static function stripSourcesSection(string $html): string
    {
        return trim((string) preg_replace('/<h2[^>]*>\s*Sources\s*<\/h2>.*$/isu', '', $html));
    }

    /**
     * @param  list<array{title: string, html: string}>  $chunks
     * @return list<array{title: string, html: string}>
     */
    private static function peelMetaH3Sections(array $chunks): array
    {
        $result = [];

        foreach ($chunks as $chunk) {
            $peeled = self::peelMetaFromHtml($chunk['html']);
            $mainHtml = trim($peeled['main']);

            if ($mainHtml !== '' || $chunk['title'] === 'Résumé') {
                $result[] = ['title' => $chunk['title'], 'html' => $mainHtml];
            }

            foreach ($peeled['meta'] as $meta) {
                $result[] = $meta;
            }
        }

        return $result;
    }

    /**
     * @return array{main: string, meta: list<array{title: string, html: string}>}
     */
    private static function peelMetaFromHtml(string $html): array
    {
        $meta = [];
        $main = $html;

        foreach (self::META_H3_PATTERNS as $displayTitle => $pattern) {
            if (! preg_match($pattern, $main, $match, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $meta[] = [
                'title' => $displayTitle,
                'html' => trim((string) ($match[1][0] ?? '')),
            ];

            $main = substr($main, 0, (int) $match[0][1])
                .substr($main, (int) $match[0][1] + strlen((string) $match[0][0]));
        }

        return ['main' => trim($main), 'meta' => $meta];
    }

    private static function normalizeHeadingTitle(string $title): string
    {
        $title = trim($title);
        $title = (string) preg_replace('/^\d+(?:\.\d+)*\.?\s*[-–—]?\s*/u', '', $title);

        return trim($title);
    }

    private static function shouldSkipH2Title(string $title): bool
    {
        $normalized = mb_strtolower(trim($title));

        return in_array($normalized, self::SKIP_H2_TITLES, true);
    }

    private static function hasVisibleText(string $html): bool
    {
        return trim(strip_tags($html)) !== '';
    }
}

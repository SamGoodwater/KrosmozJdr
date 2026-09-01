<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Reconstitue un fichier Markdown de règles à partir des chunks CMS (titre + HTML).
 *
 * @example
 * RulesMarkdownFileAssembler::assemble('1.2.1', 'Jets de dés', [
 *     ['title' => 'Résumé', 'html' => '<p><strong>Description</strong> : …</p>'],
 *     ['title' => 'Mécanique d20', 'html' => '<p>…</p>'],
 * ], $existingMarkdown);
 */
final class RulesMarkdownFileAssembler
{
    /** @var list<string> */
    private const META_TITLES = ['À retenir', 'À faire', 'À éviter'];

    /**
     * @param  list<array{title: string, html: string}>  $chunks
     */
    public static function assemble(string $number, string $title, array $chunks, string $existingMarkdown = ''): string
    {
        $number = trim($number);
        $title = trim($title);
        $parts = [];
        $parts[] = '# '.$number.'. '.$title;
        $parts[] = '';

        $resume = null;
        $bodyChunks = [];
        foreach ($chunks as $chunk) {
            $chunkTitle = trim($chunk['title']);
            if ($resume === null && self::isResumeTitle($chunkTitle)) {
                $resume = $chunk;
                continue;
            }
            $bodyChunks[] = $chunk;
        }

        if ($resume !== null) {
            $resumeMd = trim(RulesHtmlToMarkdown::convert($resume['html']));
            if ($resumeMd !== '') {
                $parts[] = $resumeMd;
                $parts[] = '';
            }
        }

        $contenuItems = [];
        foreach ($bodyChunks as $chunk) {
            $chunkTitle = trim($chunk['title']);
            if ($chunkTitle === '' || self::isMetaTitle($chunkTitle)) {
                continue;
            }
            $contenuItems[] = '- **'.$chunkTitle.'**';
        }
        if ($contenuItems !== []) {
            $parts[] = '## Contenu';
            $parts[] = '';
            $parts[] = implode("\n", $contenuItems);
            $parts[] = '';
        }

        foreach ($bodyChunks as $chunk) {
            $chunkTitle = trim($chunk['title']);
            $bodyMd = trim(RulesHtmlToMarkdown::convert($chunk['html']));
            $heading = self::isMetaTitle($chunkTitle) ? '### '.$chunkTitle : '## '.$chunkTitle;
            $parts[] = '---';
            $parts[] = '';
            $parts[] = $heading;
            $parts[] = '';
            if ($bodyMd !== '') {
                $parts[] = $bodyMd;
                $parts[] = '';
            }
        }

        $sources = self::extractSourcesBlock($existingMarkdown);
        if ($sources !== '') {
            $parts[] = '---';
            $parts[] = '';
            $parts[] = $sources;
            $parts[] = '';
        }

        $markdown = implode("\n", $parts);
        $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown) ?? $markdown;

        return trim($markdown)."\n";
    }

    public static function extractSourcesBlock(string $markdown): string
    {
        if (! preg_match('/^## Sources\s*$/mu', $markdown, $match, PREG_OFFSET_CAPTURE)) {
            return '';
        }

        $from = (int) $match[0][1];

        return trim(substr($markdown, $from));
    }

    private static function isResumeTitle(string $title): bool
    {
        return mb_strtolower($title) === 'résumé' || mb_strtolower($title) === 'resume';
    }

    private static function isMetaTitle(string $title): bool
    {
        return in_array($title, self::META_TITLES, true);
    }
}

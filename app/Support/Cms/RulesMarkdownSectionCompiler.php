<?php

declare(strict_types=1);

namespace App\Support\Cms;

use Illuminate\Support\Str;

/**
 * Convertit un fichier Markdown de règles en HTML de section CMS (krefs aplatis puis spans).
 *
 * @example
 * $html = RulesMarkdownSectionCompiler::markdownFileToHtml($raw, $path, $root, $index);
 */
final class RulesMarkdownSectionCompiler
{
    public static function markdownFileToHtml(
        string $rawMarkdown,
        string $absolutePath,
        string $rulesRootReal,
        ?RulesTocSlugIndex $index,
    ): string {
        $markdown = self::stripFirstMarkdownHeading($rawMarkdown);
        $markdown = RulesMarkdownInternalRulesLinkToPageKref::apply(
            $markdown,
            $absolutePath,
            $rulesRootReal,
            $index,
        );
        $markdown = RulesMarkdownPlainReferenceToKref::apply($markdown, $index);
        $markdown = RulesMarkdownCharacteristicKrefAutowrap::apply($markdown);
        $markdown = (new KrefShortcodeReplacer)->replace($markdown);

        return trim((string) Str::markdown($markdown));
    }

    /**
     * Slugs identiques à {@see PagesImportRulesTocCommand} (préambule = slug TOC, puis suffixe de titre).
     *
     * @param  list<array{title: string, html: string}>  $chunks
     * @return list<array{slug: string, title: string, html: string}>
     */
    public static function assignSlugs(string $number, string $tocTitle, array $chunks): array
    {
        $baseSlug = RulesImportSlugHelper::buildSectionSlug($number, $tocTitle);
        $assigned = [];
        foreach ($chunks as $index => $chunk) {
            $chunkTitle = trim((string) ($chunk['title'] ?? ''));
            if ($chunkTitle === '') {
                $chunkTitle = $tocTitle;
            }
            $slug = count($chunks) === 1
                ? $baseSlug
                : ($index === 0 ? $baseSlug : $baseSlug.'-'.Str::slug($chunkTitle));
            $assigned[] = [
                'slug' => $slug,
                'title' => $chunkTitle,
                'html' => (string) ($chunk['html'] ?? ''),
            ];
        }

        return $assigned;
    }

    private static function stripFirstMarkdownHeading(string $markdown): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $markdown);
        if (! is_array($lines) || count($lines) === 0) {
            return $markdown;
        }

        $firstNonEmptyIndex = null;
        foreach ($lines as $index => $line) {
            if (trim((string) $line) !== '') {
                $firstNonEmptyIndex = $index;
                break;
            }
        }

        if ($firstNonEmptyIndex !== null) {
            $firstLine = trim((string) $lines[$firstNonEmptyIndex]);
            if (preg_match('/^#\s+/u', $firstLine)) {
                unset($lines[$firstNonEmptyIndex]);
                if (isset($lines[$firstNonEmptyIndex + 1]) && trim((string) $lines[$firstNonEmptyIndex + 1]) === '') {
                    unset($lines[$firstNonEmptyIndex + 1]);
                }
            }
        }

        return implode(PHP_EOL, array_values($lines));
    }
}

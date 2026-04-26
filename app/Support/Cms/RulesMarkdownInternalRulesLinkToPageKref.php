<?php

namespace App\Support\Cms;

/**
 * Remplace les liens Markdown relatifs vers des fichiers de règles {@code N[.N]+-titre.md}
 * par des shortcodes {@code [[kref:pageSection:pageSlug@sectionSlug|libellé]]} lorsque le
 * numéro de section est connu dans la TOC ; sinon {@code [[kref:page:slug|libellé]]}.
 */
final class RulesMarkdownInternalRulesLinkToPageKref
{
    private const LINK_PATTERN = '/\[([^\]]*)\]\(([^)]+)\)/u';

    public static function apply(
        string $markdown,
        string $currentMdAbsolutePath,
        string $rulesRootAbsolutePath,
        ?RulesTocSlugIndex $index,
    ): string {
        if ($index === null || trim($markdown) === '') {
            return $markdown;
        }

        $rulesRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rulesRootAbsolutePath), DIRECTORY_SEPARATOR);
        $currentDir = dirname(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $currentMdAbsolutePath));

        $out = preg_replace_callback(self::LINK_PATTERN, function (array $m) use ($currentDir, $rulesRoot, $index): string {
            $label = (string) ($m[1] ?? '');
            $rawTarget = trim((string) ($m[2] ?? ''));
            if ($rawTarget === '' || preg_match('#^(https?:|mailto:)#i', $rawTarget)) {
                return $m[0];
            }

            $targetPath = preg_replace('/#.*$/', '', $rawTarget) ?? $rawTarget;
            $joined = $currentDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $targetPath);
            $resolved = realpath($joined);
            if ($resolved === false || ! str_starts_with($resolved, $rulesRoot)) {
                return $m[0];
            }

            $basename = basename($resolved);
            if (! preg_match('/^(\d+(?:\.\d+){1,2})-[^\/\\\\]+\.md$/u', $basename, $bm)) {
                return $m[0];
            }

            $num = (string) $bm[1];
            $parentL2 = self::parentLevel2NumberFromSectionNumber($num);
            $slug = $index->slugForLevel2Number($parentL2);
            if ($slug === null) {
                return $m[0];
            }

            $display = $label !== '' ? $label : $basename;

            $sectionSlug = $index->sectionSlugForL3Number($num);
            if ($sectionSlug !== null) {
                return '[[kref:pageSection:'.$slug.'@'.$sectionSlug.'|'.$display.']]';
            }

            return '[[kref:page:'.$slug.'|'.$display.']]';
        }, $markdown);

        return is_string($out) ? $out : $markdown;
    }

    /**
     * {@code 3.2.4} → page parente {@code 3.2} ; {@code 2.5} → {@code 2.5}.
     */
    public static function parentLevel2NumberFromSectionNumber(string $number): string
    {
        $parts = explode('.', trim($number));
        if (count($parts) >= 3) {
            return $parts[0].'.'.$parts[1];
        }

        return $number;
    }
}

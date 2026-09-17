<?php

declare(strict_types=1);

namespace App\Support\Cms;

/**
 * Remplace les liens Markdown relatifs vers des fichiers ou dossiers de règles
 * par des shortcodes {@code [[kref:page:slug|libellé]]}.
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

        $markdown = self::replaceSectionLinksWithEmbeddedKref($markdown, $currentMdAbsolutePath, $rulesRootAbsolutePath, $index);

        $rulesRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rulesRootAbsolutePath), DIRECTORY_SEPARATOR);
        $currentDir = dirname(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $currentMdAbsolutePath));

        $out = preg_replace_callback(self::LINK_PATTERN, function (array $m) use ($currentDir, $rulesRoot, $index): string {
            $label = (string) ($m[1] ?? '');
            $rawTarget = trim((string) ($m[2] ?? ''));
            if ($rawTarget === '' || preg_match('#^(https?:|mailto:)#i', $rawTarget)) {
                return $m[0];
            }

            if (str_contains($rawTarget, 'REFERENCE_CLES_CARACTERISTIQUES.md')) {
                $display = $label !== '' ? $label : 'Caractéristiques';

                return '[[kref:page:caracteristiques|'.$display.']]';
            }

            if (str_starts_with($rawTarget, '#')) {
                return $m[0];
            }

            $targetPath = preg_replace('/#.*$/', '', $rawTarget) ?? $rawTarget;
            $joined = $currentDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $targetPath);
            $resolved = realpath($joined);

            $sectionNumber = null;
            if ($resolved !== false) {
                if (is_file($resolved)) {
                    $sectionNumber = self::sectionNumberFromBasename(basename($resolved));
                } elseif (is_dir($resolved)) {
                    $sectionNumber = self::sectionNumberFromBasename(basename($resolved));
                }
            }

            if ($sectionNumber === null) {
                $sectionNumber = self::sectionNumberFromBasename(basename(rtrim($targetPath, '/')));
            }

            if ($sectionNumber === null) {
                return $m[0];
            }

            if ($resolved !== false && ! str_starts_with($resolved, $rulesRoot)) {
                return $m[0];
            }

            $display = $label !== '' ? $label : $sectionNumber;
            $kref = $index->krefForSectionNumber($sectionNumber, $display);

            return $kref ?? $m[0];
        }, $markdown);

        return is_string($out) ? $out : $markdown;
    }

    /**
     * Liens du type {@code [Section 3.3.4 : [[kref:…|Libellé]]](fichier.md)} (labels imbriqués).
     */
    private static function replaceSectionLinksWithEmbeddedKref(
        string $markdown,
        string $currentMdAbsolutePath,
        string $rulesRootAbsolutePath,
        RulesTocSlugIndex $index,
    ): string {
        $pattern = '/\[Section\s+(?<num>\d+(?:\.\d+)*)\s*[:—–-]\s*\[\[kref:[^|]+\|(?<kreflabel>[^\]]+)\]\]\]\((?<target>[^)]+)\)/u';

        $rulesRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rulesRootAbsolutePath), DIRECTORY_SEPARATOR);
        $currentDir = dirname(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $currentMdAbsolutePath));

        $out = preg_replace_callback($pattern, function (array $m) use ($currentDir, $rulesRoot, $index): string {
            $sectionNumber = trim((string) ($m['num'] ?? ''));
            $label = trim((string) ($m['kreflabel'] ?? ''));
            $rawTarget = trim((string) ($m['target'] ?? ''));
            $resolvedNumber = self::resolveSectionNumberFromTarget($rawTarget, $currentDir, $rulesRoot) ?? $sectionNumber;
            $kref = $index->krefForSectionNumber($resolvedNumber, $label);

            return $kref ?? $m[0];
        }, $markdown);

        return is_string($out) ? $out : $markdown;
    }

    private static function resolveSectionNumberFromTarget(string $rawTarget, string $currentDir, string $rulesRoot): ?string
    {
        $targetPath = preg_replace('/#.*$/', '', $rawTarget) ?? $rawTarget;
        $joined = $currentDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $targetPath);
        $resolved = realpath($joined);
        if ($resolved !== false && ! str_starts_with($resolved, $rulesRoot)) {
            return null;
        }
        $basename = $resolved !== false ? basename($resolved) : basename(rtrim($targetPath, '/'));

        return self::sectionNumberFromBasename($basename);
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

    private static function sectionNumberFromBasename(string $basename): ?string
    {
        if (preg_match('/^(\d+(?:\.\d+){1,2})(?:-[^\/\\\\]+)?(?:\.md)?$/u', $basename, $m)) {
            return (string) $m[1];
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace App\Support\Cms;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Indexe les fichiers {@code N.N.N-*.md} (et {@code N.N-*.md}) d’un arbre de règles.
 *
 * @example
 * $map = RulesMarkdownFileIndex::numberedFiles(base_path('private/game/rules'));
 * $map['1.2.1']; // .../1.2.1-jets-de-des-et-difficultes.md
 */
final class RulesMarkdownFileIndex
{
    /**
     * @return array<string, string> Numéro TOC => chemin absolu
     */
    public static function numberedFiles(string $rulesRootDirectory): array
    {
        if (! is_dir($rulesRootDirectory)) {
            return [];
        }

        $contentByNumber = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rulesRootDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo || ! $fileInfo->isFile()) {
                continue;
            }
            if (strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $basename = pathinfo((string) $fileInfo->getPathname(), PATHINFO_BASENAME);
            if (! preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }

            $contentByNumber[(string) $matches[1]] = (string) $fileInfo->getPathname();
        }

        return $contentByNumber;
    }
}

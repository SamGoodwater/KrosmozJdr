<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @description Assemble les exports Markdown changelog (intro, navigation semver, fichier version).
 *
 * @example Fichiers sous `storage/app/public/changelog/` : `intro.md`, `1.3.2.md`.
 */
final class ChangelogMarkdownService
{
    private const VERSION_PATTERN = '/^\d+\.\d+\.\d+$/';

    /**
     * @return list<string>
     */
    public function listSortedVersions(): array
    {
        $dir = $this->storageDir();
        if (! is_dir($dir)) {
            return [];
        }

        $glob = glob($dir.DIRECTORY_SEPARATOR.'*.md');
        $versions = [];
        foreach ($glob ?: [] as $path) {
            $base = basename((string) $path, '.md');
            if ($base === '' || strtolower($base) === 'intro') {
                continue;
            }
            if (! preg_match(self::VERSION_PATTERN, $base)) {
                continue;
            }
            $versions[] = $base;
        }

        usort($versions, static fn (string $a, string $b): int => version_compare($a, $b));

        /** @var list<string> $versions */
        return $versions;
    }

    public function isValidVersionSlug(string $version): bool
    {
        return (bool) preg_match(self::VERSION_PATTERN, $version);
    }

    public function versionMarkdownPath(string $version): string
    {
        return $this->storageDir().DIRECTORY_SEPARATOR.$version.'.md';
    }

    /**
     * @throws NotFoundHttpException
     */
    public function composeFeedMarkdown(string $version): string
    {
        $path = $this->versionMarkdownPath($version);
        if (! is_readable($path)) {
            abort(404);
        }

        $chunks = [];

        $introPath = $this->storageDir().DIRECTORY_SEPARATOR.'intro.md';
        if (is_readable($introPath)) {
            $chunks[] = rtrim(file_get_contents($introPath) ?: '')."\n\n";
        }

        $versions = $this->listSortedVersions();
        $chunks[] = $this->navigationMarkdown($version, $versions);

        $chunks[] = rtrim(file_get_contents($path) ?: '')."\n";

        return implode('', $chunks);
    }

    private function navigationMarkdown(string $current, array $sortedVersions): string
    {
        if ($sortedVersions === []) {
            return '';
        }

        $parts = [];
        foreach ($sortedVersions as $v) {
            if ($v === $current) {
                $parts[] = "**{$v}**";
            } else {
                $parts[] = sprintf('[*%s*](/changelog/feed/%s)', $v, $v);
            }
        }

        return "### Navigation des versions\n\n"
            .implode(' · ', $parts)
            ."\n\n---\n\n";
    }

    private function storageDir(): string
    {
        return storage_path('app/public/changelog');
    }
}

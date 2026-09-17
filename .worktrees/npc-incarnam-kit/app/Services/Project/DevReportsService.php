<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Liste et télécharge les rapports Markdown générés par `project:review`.
 */
final class DevReportsService
{
    private const REPORTS_SUBDIR = 'dev-reports';

    public function storageDirectory(): string
    {
        return storage_path('app/'.self::REPORTS_SUBDIR);
    }

    /**
     * @return Collection<int, array{basename: string, size: int, modified_at: string}>
     */
    public function listMarkdownReports(): Collection
    {
        $dir = $this->storageDirectory();
        if (! is_dir($dir)) {
            return collect();
        }

        $files = collect(File::files($dir))
            ->filter(fn (\SplFileInfo $f) => str_ends_with(strtolower($f->getFilename()), '.md'))
            ->sortByDesc(fn (\SplFileInfo $f) => $f->getMTime())
            ->values()
            ->map(fn (\SplFileInfo $f) => [
                'basename' => $f->getFilename(),
                'size' => $f->getSize(),
                'modified_at' => date('c', $f->getMTime()),
            ]);

        return $files;
    }

    /**
     * @return array{path: string, basename: string}|null
     */
    public function resolveSafeDownloadPath(string $basename): ?array
    {
        $clean = basename($basename);
        if (
            $clean === ''
            || str_starts_with($clean, '.')
            || $clean !== $basename
            || str_contains($clean, '..')
            || str_contains($basename, "\0")
        ) {
            return null;
        }

        $dir = realpath($this->storageDirectory());
        if ($dir === false) {
            return null;
        }

        $full = $dir.DIRECTORY_SEPARATOR.$clean;
        if (! is_file($full)) {
            return null;
        }

        $real = realpath($full);
        if ($real === false || $real === $dir) {
            return null;
        }

        $dirPrefix = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (! str_starts_with($real, $dirPrefix)) {
            return null;
        }

        return ['path' => $real, 'basename' => $clean];
    }

    /**
     * Vérifie qu’un chemin absolu de fichier cible (.md) est bien résolu sous {@see storageDirectory()}
     * (le fichier peut ne pas encore exister). Protection contre payloads de job forgés ou chemins mal formés.
     */
    public function isAllowedNewReportPath(string $absolutePath): bool
    {
        $cleanBase = basename($absolutePath);
        if (
            $cleanBase === ''
            || str_contains((string) $absolutePath, "\0")
            || str_contains($cleanBase, '..')
            || str_starts_with($cleanBase, '.')
            || ! str_ends_with(strtolower($cleanBase), '.md')
            || preg_match('/[^A-Za-z0-9._-]/', $cleanBase) === 1
        ) {
            return false;
        }

        $root = realpath($this->storageDirectory());
        $parentReal = realpath(dirname($absolutePath));

        return $root !== false
            && $parentReal !== false
            && $parentReal === $root;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Media;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Repère les fichiers publics MediaLibrary qui n'ont plus de ligne `media`.
 *
 * @example
 * $service->scan(delete: false); // dry-run, aucun fichier supprimé
 */
class OrphanPublicMediaCleanupService
{
    /** @var list<string> */
    private const SCANNED_ROOTS = [
        'images/entity',
        'images/users',
        'images/uploads/entity-placeholders',
        'sections',
    ];

    /** @var list<string> */
    private const PROTECTED_PREFIXES = [
        'changelog/',
        'legal/',
        'images/calendar/',
    ];

    /**
     * @return array{
     *   dryRun: bool,
     *   scannedRoots: list<string>,
     *   scannedFiles: int,
     *   candidateCount: int,
     *   deletedCount: int,
     *   candidates: list<array{path: string, size: int|null, reason: string}>
     * }
     */
    public function scan(bool $delete = false, int $limit = 200): array
    {
        $disk = Storage::disk('public');
        $files = $this->collectFiles($disk);
        $referencedDirs = $this->referencedMediaDirectories();
        $orphanPaths = $this->filterOrphanFiles($files, $referencedDirs);

        $candidates = [];
        $deletedCount = 0;
        foreach ($orphanPaths as $path) {
            $candidates[] = [
                'path' => $path,
                'size' => $disk->exists($path) ? $disk->size($path) : null,
                'reason' => 'Aucune ligne media ne référence ce dossier/fichier.',
            ];

            if ($delete && $disk->exists($path)) {
                $disk->delete($path);
                $deletedCount++;
            }
        }

        return [
            'dryRun' => ! $delete,
            'scannedRoots' => self::SCANNED_ROOTS,
            'scannedFiles' => count($files),
            'candidateCount' => count($orphanPaths),
            'deletedCount' => $deletedCount,
            'candidates' => array_slice($candidates, 0, max(1, $limit)),
        ];
    }

    /**
     * Filtre pur, testé sans base ni filesystem réel.
     *
     * @param  list<string>  $files
     * @param  list<string>  $referencedDirs
     * @return list<string>
     */
    public function filterOrphanFiles(array $files, array $referencedDirs): array
    {
        $dirs = array_values(array_filter(array_map(
            fn (string $dir): string => rtrim($this->normalizePath($dir), '/').'/',
            $referencedDirs
        )));

        $orphans = [];
        foreach ($files as $file) {
            $path = $this->normalizePath($file);
            if ($path === '' || $this->isProtectedPath($path)) {
                continue;
            }

            $referenced = false;
            foreach ($dirs as $dir) {
                if (str_starts_with($path, $dir)) {
                    $referenced = true;
                    break;
                }
            }

            if (! $referenced) {
                $orphans[] = $path;
            }
        }

        return array_values(array_unique($orphans));
    }

    /**
     * @return list<string>
     */
    private function collectFiles(Filesystem $disk): array
    {
        $files = [];
        foreach (self::SCANNED_ROOTS as $root) {
            if (! $disk->exists($root)) {
                continue;
            }

            foreach ($disk->allFiles($root) as $file) {
                $files[] = $this->normalizePath((string) $file);
            }
        }

        return array_values(array_unique($files));
    }

    /**
     * @return list<string>
     */
    private function referencedMediaDirectories(): array
    {
        $pathGenerator = app(config('media-library.path_generator'));
        $dirs = [];

        Media::query()
            ->where(function ($query): void {
                $query->where('disk', 'public')
                    ->orWhere('conversions_disk', 'public');
            })
            ->select(['id', 'model_type', 'model_id', 'disk', 'conversions_disk'])
            ->orderBy('id')
            ->cursor()
            ->each(function (Media $media) use (&$dirs, $pathGenerator): void {
                foreach (['getPath', 'getPathForConversions', 'getPathForResponsiveImages'] as $method) {
                    if (method_exists($pathGenerator, $method)) {
                        $dirs[] = $this->normalizePath((string) $pathGenerator->{$method}($media));
                    }
                }
            });

        return array_values(array_unique(array_filter($dirs)));
    }

    private function isProtectedPath(string $path): bool
    {
        foreach (self::PROTECTED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function normalizePath(string $path): string
    {
        return trim(str_replace('\\', '/', $path), '/');
    }
}

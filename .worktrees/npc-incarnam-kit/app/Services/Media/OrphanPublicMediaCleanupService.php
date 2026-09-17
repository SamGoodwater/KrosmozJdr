<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\MediaCleanupJob;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Repère les fichiers publics MediaLibrary qui n'ont plus de ligne `media`.
 *
 * @example
 * $service->scan(delete: false); // dry-run, aucun fichier supprimé
 * $service->process($job); // suivi progressif avec annulation
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

    private const CANCEL_CHECK_EVERY = 500;

    private const PROGRESS_SAVE_EVERY = 500;

    /**
     * @return list<string>
     */
    public function scannedRoots(): array
    {
        return self::SCANNED_ROOTS;
    }

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

        if ($delete) {
            $this->cleanupEmptyDirectories($disk, $orphanPaths);
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
     * Traite un job suivi fichier par fichier (progress + annulation coopérative).
     */
    public function process(MediaCleanupJob $job): void
    {
        $disk = Storage::disk('public');
        $delete = $job->isDeleteMode();

        Log::info('media_cleanup.collect_start', ['job_id' => $job->id]);
        $files = $this->collectFiles($disk);
        Log::info('media_cleanup.collect_done', [
            'job_id' => $job->id,
            'files' => count($files),
        ]);

        $referencedDirs = $this->referencedMediaDirectories();
        $referencedSet = $this->buildReferencedDirSet($referencedDirs);
        Log::info('media_cleanup.refs_ready', [
            'job_id' => $job->id,
            'referenced_dirs' => count($referencedSet),
        ]);

        $total = count($files);
        $job->progress_total = $total;
        $job->progress_done = 0;
        $job->save();

        $candidateCount = 0;
        $deletedCount = 0;
        $bytesFreed = 0;
        $deletedPaths = [];
        $sampleCandidates = [];
        $done = 0;

        foreach ($files as $file) {
            if ($done % self::CANCEL_CHECK_EVERY === 0) {
                $job->refresh();
                if ($job->status === MediaCleanupJob::STATUS_CANCELLED) {
                    $job->progress_done = $done;
                    $this->finalizeJob($job, [
                        'scannedFiles' => $total,
                        'candidateCount' => $candidateCount,
                        'deletedCount' => $deletedCount,
                        'bytesFreed' => $bytesFreed,
                        'cancelled' => true,
                        'dryRun' => ! $delete,
                        'scannedRoots' => self::SCANNED_ROOTS,
                        'sampleCandidates' => $sampleCandidates,
                    ], cancelled: true);

                    return;
                }
            }

            $path = $this->normalizePath($file);
            $isOrphan = $path !== ''
                && ! $this->isProtectedPath($path)
                && ! $this->isPathReferenced($path, $referencedSet);

            if ($isOrphan) {
                $candidateCount++;
                $size = $disk->exists($path) ? (int) $disk->size($path) : 0;

                if (count($sampleCandidates) < 50) {
                    $sampleCandidates[] = [
                        'path' => $path,
                        'size' => $size > 0 ? $size : null,
                        'reason' => 'Aucune ligne media ne référence ce dossier/fichier.',
                    ];
                }

                if ($delete && $disk->exists($path)) {
                    $disk->delete($path);
                    $deletedCount++;
                    $bytesFreed += $size;
                    $deletedPaths[] = $path;
                }
            }

            $done++;
            if ($done % self::PROGRESS_SAVE_EVERY === 0 || $done === $total) {
                $job->progress_done = $done;
                $job->save();
                Log::info('media_cleanup.progress', [
                    'job_id' => $job->id,
                    'done' => $done,
                    'total' => $total,
                    'candidates' => $candidateCount,
                    'deleted' => $deletedCount,
                ]);
            }
        }

        if ($delete && $deletedPaths !== []) {
            $this->cleanupEmptyDirectories($disk, $deletedPaths);
        }

        $job->progress_done = $done;
        $this->finalizeJob($job, [
            'scannedFiles' => $total,
            'candidateCount' => $candidateCount,
            'deletedCount' => $deletedCount,
            'bytesFreed' => $bytesFreed,
            'cancelled' => false,
            'dryRun' => ! $delete,
            'scannedRoots' => self::SCANNED_ROOTS,
            'sampleCandidates' => $sampleCandidates,
        ], cancelled: false);
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
        $referencedSet = $this->buildReferencedDirSet($referencedDirs);

        $orphans = [];
        foreach ($files as $file) {
            $path = $this->normalizePath($file);
            if ($path === '' || $this->isProtectedPath($path)) {
                continue;
            }

            if (! $this->isPathReferenced($path, $referencedSet)) {
                $orphans[] = $path;
            }
        }

        return array_values(array_unique($orphans));
    }

    /**
     * @param  list<string>  $referencedDirs
     * @return array<string, true>
     */
    public function buildReferencedDirSet(array $referencedDirs): array
    {
        $set = [];
        foreach ($referencedDirs as $dir) {
            $normalized = rtrim($this->normalizePath($dir), '/');
            if ($normalized === '') {
                continue;
            }
            $set[$normalized] = true;
        }

        return $set;
    }

    /**
     * @param  array<string, true>  $referencedSet
     */
    public function isPathReferenced(string $path, array $referencedSet): bool
    {
        $dir = dirname($this->normalizePath($path));
        while ($dir !== '.' && $dir !== '' && $dir !== '/') {
            if (isset($referencedSet[$dir])) {
                return true;
            }
            $parent = dirname($dir);
            if ($parent === $dir) {
                break;
            }
            $dir = $parent;
        }

        return false;
    }

    /**
     * @param  array{
     *   scannedFiles: int,
     *   candidateCount: int,
     *   deletedCount: int,
     *   bytesFreed: int,
     *   cancelled: bool,
     *   dryRun: bool,
     *   scannedRoots: list<string>,
     *   sampleCandidates: list<array{path: string, size: int|null, reason: string}>
     * }  $summary
     */
    private function finalizeJob(MediaCleanupJob $job, array $summary, bool $cancelled): void
    {
        $job->summary = $summary;
        if (! $cancelled) {
            $job->progress_done = $job->progress_total;
        }
        $job->finished_at = now();

        if ($cancelled) {
            $job->status = MediaCleanupJob::STATUS_CANCELLED;
            $job->cancelled_at = $job->cancelled_at ?? now();
        } else {
            $job->status = MediaCleanupJob::STATUS_SUCCEEDED;
        }

        $job->save();
    }

    /**
     * @param  list<string>  $paths
     */
    private function cleanupEmptyDirectories(Filesystem $disk, array $paths): void
    {
        $dirs = [];
        foreach ($paths as $path) {
            $dir = dirname($this->normalizePath($path));
            while ($dir !== '.' && $dir !== '' && $dir !== '/') {
                $dirs[] = $dir;
                $parent = dirname($dir);
                if ($parent === $dir) {
                    break;
                }
                $dir = $parent;
            }
        }

        $dirs = array_values(array_unique($dirs));
        usort($dirs, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($dirs as $dir) {
            if (! $this->isUnderScannedRoot($dir)) {
                continue;
            }
            if (! $disk->exists($dir)) {
                continue;
            }
            if ($disk->allFiles($dir) !== [] || $disk->directories($dir) !== []) {
                continue;
            }
            $disk->deleteDirectory($dir);
        }
    }

    private function isUnderScannedRoot(string $dir): bool
    {
        $normalized = rtrim($this->normalizePath($dir), '/').'/';
        foreach (self::SCANNED_ROOTS as $root) {
            $rootPrefix = rtrim($root, '/').'/';
            if ($normalized === $rootPrefix || str_starts_with($normalized, $rootPrefix)) {
                // Ne jamais supprimer la racine scannée elle-même
                if (rtrim($this->normalizePath($dir), '/') === rtrim($root, '/')) {
                    return false;
                }

                return true;
            }
        }

        return false;
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

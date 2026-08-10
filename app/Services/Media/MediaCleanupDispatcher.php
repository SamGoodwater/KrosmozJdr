<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Jobs\ProcessMediaCleanupJob;
use App\Models\MediaCleanupJob;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Crée et dispatch un job de nettoyage MediaLibrary orphelin.
 *
 * @example app(MediaCleanupDispatcher::class)->dispatch(MediaCleanupJob::MODE_DRY_RUN, $userId);
 */
class MediaCleanupDispatcher
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(string $mode, ?int $requestedBy = null, array $payload = [], bool $skipNotify = false): MediaCleanupJob
    {
        if (! in_array($mode, [MediaCleanupJob::MODE_DRY_RUN, MediaCleanupJob::MODE_DELETE], true)) {
            throw new RuntimeException('Mode de nettoyage invalide.');
        }

        $active = MediaCleanupJob::query()
            ->whereIn('status', [MediaCleanupJob::STATUS_QUEUED, MediaCleanupJob::STATUS_RUNNING])
            ->exists();

        if ($active) {
            throw new RuntimeException('Un nettoyage de fichiers orphelins est déjà en cours.');
        }

        $job = MediaCleanupJob::query()->create([
            'status' => MediaCleanupJob::STATUS_QUEUED,
            'mode' => $mode,
            'requested_by' => $requestedBy,
            'payload' => array_merge($payload, [
                'skip_notify' => $skipNotify,
            ]),
            'progress_done' => 0,
            'progress_total' => 0,
        ]);

        Log::info('media_cleanup.dispatched', [
            'job_id' => $job->id,
            'mode' => $mode,
            'requested_by' => $requestedBy,
            'skip_notify' => $skipNotify,
        ]);

        ProcessMediaCleanupJob::dispatch($job->id);

        return $job;
    }
}

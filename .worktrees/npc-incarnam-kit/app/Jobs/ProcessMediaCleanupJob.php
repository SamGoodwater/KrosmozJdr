<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MediaCleanupJob;
use App\Services\Media\OrphanPublicMediaCleanupService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Exécute un {@see MediaCleanupJob} (scan / suppression fichiers orphelins) en file d’attente.
 */
class ProcessMediaCleanupJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    private const LOCK_KEY = 'media-cleanup';

    private const LOCK_TTL_SECONDS = 3600;

    public function __construct(private readonly string $mediaCleanupJobId) {}

    public function handle(OrphanPublicMediaCleanupService $cleanupService): void
    {
        $job = MediaCleanupJob::query()->find($this->mediaCleanupJobId);
        if (! $job || $job->isTerminal()) {
            return;
        }

        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);
        if (! $lock->get()) {
            Log::warning('media_cleanup.lock_busy', [
                'job_id' => $job->id,
            ]);
            $job->status = MediaCleanupJob::STATUS_FAILED;
            $job->error = 'Un autre nettoyage est déjà en cours (verrou).';
            $job->finished_at = now();
            $job->save();
            $this->notify($job, success: false);

            return;
        }

        $started = microtime(true);

        try {
            $job->status = MediaCleanupJob::STATUS_RUNNING;
            $job->started_at = now();
            $job->error = null;
            $job->save();

            Log::info('media_cleanup.started', [
                'job_id' => $job->id,
                'mode' => $job->mode,
                'requested_by' => $job->requested_by,
            ]);

            $cleanupService->process($job);
            $job->refresh();

            $event = $job->status === MediaCleanupJob::STATUS_CANCELLED
                ? 'media_cleanup.cancelled'
                : 'media_cleanup.finished';

            Log::info($event, [
                'job_id' => $job->id,
                'status' => $job->status,
                'summary' => $job->summary,
                'duration_seconds' => round(microtime(true) - $started, 2),
            ]);

            $this->notify($job, success: $job->status === MediaCleanupJob::STATUS_SUCCEEDED);
        } catch (Throwable $e) {
            Log::error('media_cleanup.failed', [
                'job_id' => $job->id,
                'exception' => $e->getMessage(),
            ]);

            $job->refresh();
            if (! $job->isTerminal()) {
                $job->status = MediaCleanupJob::STATUS_FAILED;
                $job->error = $e->getMessage();
                $job->finished_at = now();
                $job->save();
            }

            $this->notify($job, success: false);
            throw $e;
        } finally {
            $lock->release();
        }
    }

    public function failed(?Throwable $e): void
    {
        Log::error('media_cleanup.job_failed', [
            'job_id' => $this->mediaCleanupJobId,
            'exception' => $e?->getMessage(),
        ]);

        $job = MediaCleanupJob::query()->find($this->mediaCleanupJobId);
        if ($job && ! $job->isTerminal()) {
            $job->status = MediaCleanupJob::STATUS_FAILED;
            $job->error = $e?->getMessage() ?? 'Échec inconnu';
            $job->finished_at = now();
            $job->save();
            $this->notify($job, success: false);
        }
    }

    private function notify(MediaCleanupJob $job, bool $success): void
    {
        $payload = is_array($job->payload) ? $job->payload : [];
        if (! empty($payload['skip_notify'])) {
            return;
        }

        $summary = is_array($job->summary) ? $job->summary : [];
        $deleted = (int) ($summary['deletedCount'] ?? 0);
        $candidates = (int) ($summary['candidateCount'] ?? 0);
        $duration = 0.0;
        if ($job->started_at && $job->finished_at) {
            $duration = (float) $job->started_at->diffInSeconds($job->finished_at);
        }

        $message = match ($job->status) {
            MediaCleanupJob::STATUS_CANCELLED => sprintf(
                'Nettoyage annulé — %d candidat(s), %d fichier(s) supprimé(s).',
                $candidates,
                $deleted,
            ),
            MediaCleanupJob::STATUS_FAILED => 'Échec du nettoyage'.($job->error ? ' : '.$job->error : '.'),
            default => $job->isDeleteMode()
                ? sprintf('%d fichier(s) orphelin(s) supprimé(s) sur %d candidat(s).', $deleted, $candidates)
                : sprintf('Dry-run terminé — %d candidat(s) orphelin(s) détecté(s).', $candidates),
        };

        NotificationService::notifyProjectMaintenance(
            'clear-orphan-files',
            $success,
            $duration,
            ($job->finished_at ?? now())->toDateTimeString(),
            $message,
        );
    }
}

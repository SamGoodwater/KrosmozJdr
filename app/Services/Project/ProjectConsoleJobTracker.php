<?php

declare(strict_types=1);

namespace App\Services\Project;

use App\Models\ProjectConsoleJob;
use App\Models\User;
use App\Notifications\ProjectConsoleJobProgressNotification;
use App\Support\Console\ConsoleOutputSanitizer;
use App\Support\Console\ConsoleProgressEstimator;
use App\Support\Console\TrackingConsoleOutput;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * File d’attente + exécution suivie des commandes Artisan admin (log filtré, %).
 *
 * @example
 * $job = $tracker->tryQueue(ProjectConsoleDomain::CLEAR, 'project:clear --safe', $userId);
 * $tracker->runArtisan($job->id, 'project:clear', ['--safe' => true]);
 */
class ProjectConsoleJobTracker
{
    private const QUEUE_LOCK_TTL = 15;

    private const FLUSH_INTERVAL_SECONDS = 0.4;

    public function __construct(
        private readonly ConsoleOutputSanitizer $sanitizer,
    ) {}

    /**
     * Crée un enregistrement `queued` si le domaine est libre.
     */
    public function tryQueue(string $domain, string $commandLine, int $userId): ?ProjectConsoleJob
    {
        $lock = Cache::lock('project-console-queue:'.$domain, self::QUEUE_LOCK_TTL);
        if (! $lock->get()) {
            return null;
        }

        try {
            if (ProjectConsoleJob::hasActive($domain)) {
                return null;
            }

            $job = ProjectConsoleJob::query()->create([
                'domain' => $domain,
                'status' => ProjectConsoleJob::STATUS_QUEUED,
                'progress' => 1,
                'progress_label' => 'En file d’attente',
                'command' => $commandLine,
                'page_url' => ProjectConsoleDomain::pageUrl($domain),
                'output' => '',
                'triggered_by' => $userId,
            ]);

            $this->syncNotification($job);

            return $job;
        } finally {
            $lock->release();
        }
    }

    /**
     * Formate une ligne de commande affichable (sans secrets).
     *
     * @param  array<string, mixed>  $parameters
     */
    public static function commandLine(string $signature, array $parameters): string
    {
        $parts = [$signature];
        foreach ($parameters as $key => $value) {
            if (is_int($key)) {
                $parts[] = (string) $value;

                continue;
            }
            if ($value === true) {
                $parts[] = (string) $key;
            } elseif ($value !== false && $value !== null && $value !== '') {
                $parts[] = $key.'='.(is_scalar($value) ? (string) $value : json_encode($value));
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Exécute Artisan en streamant la sortie vers l’enregistrement.
     *
     * @param  array<string, mixed>  $parameters
     */
    public function runArtisan(?string $jobId, string $command, array $parameters): int
    {
        $record = $jobId ? ProjectConsoleJob::query()->find($jobId) : null;
        $estimator = new ConsoleProgressEstimator;
        $buffer = $record ? (string) $record->output : '';
        $lastFlush = 0.0;

        if ($record !== null) {
            $record->status = ProjectConsoleJob::STATUS_RUNNING;
            $record->started_at = now();
            $record->progress = 5;
            $record->progress_label = 'Démarrage';
            $record->save();
            $this->syncNotification($record);
        }

        $flush = function (bool $force) use (&$buffer, &$lastFlush, $record, $estimator): void {
            if ($record === null) {
                return;
            }
            $now = microtime(true);
            if (! $force && ($now - $lastFlush) < self::FLUSH_INTERVAL_SECONDS) {
                return;
            }
            $lastFlush = $now;
            $record->output = $this->sanitizer->cap($buffer);
            $record->progress = $estimator->percent();
            $record->progress_label = $estimator->label();
            $record->save();
            $this->syncNotification($record);
        };

        $output = new TrackingConsoleOutput(function (string $chunk) use (&$buffer, $estimator, $flush): void {
            $clean = $this->sanitizer->sanitize($chunk);
            if ($clean === '') {
                return;
            }
            $buffer .= $clean;
            $estimator->ingest($clean);
            $flush(false);
        });

        try {
            $code = Artisan::call($command, $parameters, $output);
        } catch (\Throwable $e) {
            $flush(true);
            $this->markFailed($jobId, $e->getMessage());
            throw $e;
        }
        $flush(true);

        if ($record !== null) {
            $record->exit_code = $code;
            $record->finished_at = now();
            $record->progress = 100;
            if ($code === 0) {
                $record->status = ProjectConsoleJob::STATUS_SUCCESS;
                $record->progress_label = 'Terminé';
            } else {
                $record->status = ProjectConsoleJob::STATUS_FAILED;
                $record->progress_label = 'Échec';
                $record->error = 'La commande a retourné le code '.$code.'.';
            }
            $record->output = $this->sanitizer->cap($buffer);
            $record->save();
            $this->syncNotification($record);
        }

        return $code;
    }

    public function markFailed(?string $jobId, string $message): void
    {
        if ($jobId === null || $jobId === '') {
            return;
        }

        $record = ProjectConsoleJob::query()->find($jobId);
        if ($record === null || $record->isTerminal()) {
            return;
        }

        $record->status = ProjectConsoleJob::STATUS_FAILED;
        $record->progress = 100;
        $record->progress_label = 'Échec';
        $record->error = $message;
        $record->finished_at = now();
        $record->save();
        $this->syncNotification($record);
    }

    private function syncNotification(ProjectConsoleJob $job): void
    {
        $user = User::query()->find($job->triggered_by);
        if ($user === null) {
            return;
        }

        $payload = [
            'kind' => 'console_job',
            'domain' => $job->domain,
            'message' => $this->notificationMessage($job),
            'url' => $job->page_url ?: ProjectConsoleDomain::pageUrl($job->domain),
            'status' => $job->status,
            'progress' => [
                'percent' => (int) $job->progress,
                'label' => $job->progress_label,
            ],
            'job_id' => $job->id,
            'error' => $job->error,
            'locked' => $job->isActive(),
        ];

        if (is_string($job->notification_id) && $job->notification_id !== '') {
            $existing = DatabaseNotification::query()->find($job->notification_id);
            if ($existing !== null) {
                $existing->forceFill(['data' => $payload])->save();

                return;
            }
        }

        $user->notify(new ProjectConsoleJobProgressNotification($payload));
        $created = $user->notifications()
            ->where('type', ProjectConsoleJobProgressNotification::class)
            ->latest('created_at')
            ->first();

        if ($created !== null) {
            $job->notification_id = $created->id;
            $job->save();
        }
    }

    private function notificationMessage(ProjectConsoleJob $job): string
    {
        $label = ProjectConsoleDomain::label($job->domain);
        $percent = (int) $job->progress;
        $phase = $job->progress_label ?: $job->status;

        return match ($job->status) {
            ProjectConsoleJob::STATUS_SUCCESS => $label.' terminé (100 %)',
            ProjectConsoleJob::STATUS_FAILED => $label.' en échec',
            default => $label.' — '.$phase.' ('.$percent.' %)',
        };
    }

    public function logArtisanFailure(string $context, int $userId, int $code): void
    {
        Log::error($context.' : commande en échec', [
            'user_id' => $userId,
            'exit_code' => $code,
        ]);
    }
}

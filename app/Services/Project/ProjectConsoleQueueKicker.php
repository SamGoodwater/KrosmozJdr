<?php

declare(strict_types=1);

namespace App\Services\Project;

use App\Support\Queue\ProjectQueues;
use Symfony\Component\Process\Process;

/**
 * Lance un worker ponctuel pour une file dédiée (bouton admin sans `queue:listen`).
 *
 * Les appels pendant une requête HTTP sont groupés et démarrés à la fin de la requête,
 * pour que toutes les notifications d’un même envoi soient déjà en base.
 *
 * @example
 * app(ProjectConsoleQueueKicker::class)->kick(ProjectQueues::BACKUP);
 */
class ProjectConsoleQueueKicker
{
    public const QUEUE_RULES_DOWNLOADS = ProjectQueues::RULES_DOWNLOADS;

    /** @var array<string, true> */
    private array $pending = [];

    private bool $flushRegistered = false;

    /**
     * Mémorise une file à démarrer. No-op en tests et si la connexion est `sync`.
     */
    public function kick(string $queue): void
    {
        if (app()->runningUnitTests() || config('queue.default') === 'sync') {
            return;
        }
        if (! ProjectQueues::isManaged($queue)) {
            return;
        }

        if ($this->insideQueueWorker()) {
            $this->start($queue);

            return;
        }

        $this->pending[$queue] = true;
        if ($this->flushRegistered) {
            return;
        }
        $this->flushRegistered = true;
        app()->terminating(function (): void {
            $this->flush();
        });
    }

    /**
     * Démarre les workers mémorisés (aussi appelé en fin de requête).
     */
    public function flush(): void
    {
        $queues = array_keys($this->pending);
        $this->pending = [];
        foreach ($queues as $queue) {
            $this->start($queue);
        }
    }

    private function insideQueueWorker(): bool
    {
        $command = $_SERVER['argv'][1] ?? '';

        return is_string($command) && in_array($command, ['queue:work', 'queue:listen'], true);
    }

    private function start(string $queue): void
    {
        $php = defined('PHP_BINARY') && PHP_BINARY !== '' ? PHP_BINARY : 'php';
        $timeout = ProjectQueues::workerTimeout($queue);
        $maxJobs = ProjectQueues::maxJobs($queue);
        $maxJobsArg = $maxJobs !== null ? ' --max-jobs='.$maxJobs : '';
        $lock = storage_path('framework/queue-kicker-'.$queue.'.lock');
        $command = sprintf(
            'nohup flock -n %s %s %s queue:work --queue=%s --stop-when-empty --tries=1 --timeout=%d%s --memory=512 >/dev/null 2>&1 &',
            escapeshellarg($lock),
            escapeshellarg($php),
            escapeshellarg(base_path('artisan')),
            escapeshellarg($queue),
            $timeout,
            $maxJobsArg
        );

        $process = Process::fromShellCommandline($command, base_path());
        $process->disableOutput();
        $process->setTimeout(8);
        $process->run();
    }
}

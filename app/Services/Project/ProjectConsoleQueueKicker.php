<?php

declare(strict_types=1);

namespace App\Services\Project;

use Symfony\Component\Process\Process;

/**
 * Lance un worker ponctuel pour une file dédiée (bouton admin sans `queue:listen`).
 *
 * @example
 * app(ProjectConsoleQueueKicker::class)->kick(ProjectConsoleQueueKicker::QUEUE_RULES_DOWNLOADS);
 */
class ProjectConsoleQueueKicker
{
    public const QUEUE_RULES_DOWNLOADS = 'rules-downloads';

    /**
     * Démarre `queue:work --stop-when-empty` en arrière-plan pour la file donnée.
     *
     * No-op en tests et si la connexion est `sync` (le job s’exécute déjà dans la requête).
     */
    public function kick(string $queue): void
    {
        if (app()->runningUnitTests() || config('queue.default') === 'sync') {
            return;
        }
        if (preg_match('/^[a-z0-9_-]+$/i', $queue) !== 1) {
            return;
        }

        $php = defined('PHP_BINARY') && PHP_BINARY !== '' ? PHP_BINARY : 'php';
        $command = sprintf(
            'nohup %s %s queue:work --queue=%s --stop-when-empty --tries=1 --timeout=1800 --max-jobs=1 --memory=512 >/dev/null 2>&1 &',
            escapeshellarg($php),
            escapeshellarg(base_path('artisan')),
            escapeshellarg($queue)
        );

        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(8);
        $process->run();
    }
}

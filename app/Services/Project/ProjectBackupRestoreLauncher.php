<?php

declare(strict_types=1);

namespace App\Services\Project;

use Symfony\Component\Process\Process;

/**
 * Démarre une restauration en processus détaché (hors file database).
 *
 * @example
 * app(ProjectBackupRestoreLauncher::class)->start('archive.zip', $userId);
 */
class ProjectBackupRestoreLauncher
{
    /**
     * @return array{ok: bool, message: string}
     */
    public function start(string $archiveName, int $userId, bool $noSafetyBackup = false): array
    {
        $backups = ProjectBackupService::fromConfig();
        $status = ProjectBackupOperationStatus::forBackupRoot($backups->resolvedBackupDirectory());

        if ($status->isBusy() || $backups->isLocked()) {
            return [
                'ok' => false,
                'message' => 'Une sauvegarde ou une restauration est déjà en cours.',
            ];
        }

        $status->write([
            'state' => 'queued',
            'archive' => $archiveName,
            'user_id' => $userId,
            'started_at' => now()->toIso8601String(),
            'phase' => 'queued',
            'progress' => 1,
            'message' => 'Restauration lancée — démarrage du processus…',
            'log' => ['['.now()->format('H:i:s').'] Restauration lancée.'],
        ]);

        $php = PHP_BINARY !== '' ? PHP_BINARY : 'php';
        $args = [
            $php,
            base_path('artisan'),
            'project:backup:restore',
            $archiveName,
            '--yes',
            '--write-status',
            '--skip-notify',
        ];
        if ($noSafetyBackup) {
            $args[] = '--no-safety-backup';
        }

        $logFile = $backups->resolvedBackupDirectory().DIRECTORY_SEPARATOR.'.restore-process.log';

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $process = new Process($args, base_path());
                $process->setTimeout(null);
                $process->disableOutput();
                $process->start();
            } else {
                // Détache le processus pour survivre à la fin de la requête HTTP.
                $cmd = 'nohup '.implode(' ', array_map('escapeshellarg', $args))
                    .' >> '.escapeshellarg($logFile).' 2>&1 &';
                $process = Process::fromShellCommandline($cmd, base_path());
                $process->setTimeout(30);
                $process->run();
                if (! $process->isSuccessful()) {
                    throw new \RuntimeException(trim($process->getErrorOutput().$process->getOutput()) ?: 'spawn failed');
                }
            }
        } catch (\Throwable $e) {
            $status->write([
                'state' => 'failed',
                'archive' => $archiveName,
                'finished_at' => now()->toIso8601String(),
                'message' => 'Impossible de démarrer le processus : '.$e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'Impossible de démarrer la restauration.',
            ];
        }

        return [
            'ok' => true,
            'message' => 'Restauration lancée. Suivi via le panneau de statut (fichier local).',
        ];
    }
}

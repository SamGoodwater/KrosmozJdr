<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Réconcilie sauvegarde/restauration après crash process / WSL / reboot.
 *
 * @example
 * $report = ProjectBackupRecovery::fromConfig()->reconcile();
 */
final class ProjectBackupRecovery
{
    public function __construct(
        private readonly ProjectBackupService $backups,
        private readonly ProjectBackupOperationStatus $status,
    ) {}

    public static function fromConfig(): self
    {
        $backups = ProjectBackupService::fromConfig();

        return new self(
            $backups,
            ProjectBackupOperationStatus::forBackupRoot($backups->resolvedBackupDirectory()),
        );
    }

    /**
     * @return array{
     *     restore_status: array<string, mixed>|null,
     *     operation_locked: bool,
     *     maintenance: bool,
     *     reconciled: bool,
     *     actions: list<string>
     * }
     */
    public function reconcile(): array
    {
        $actions = [];
        $reconciled = false;

        $data = $this->status->read();
        $busy = is_array($data) && in_array((string) ($data['state'] ?? ''), ['queued', 'running'], true);

        if ($busy) {
            $pid = isset($data['pid']) ? (int) $data['pid'] : 0;
            $processAlive = $pid > 0 && $this->isProcessAlive($pid);
            $lockHeld = $this->backups->isLocked();

            // Process mort + pas de verrou flock = interruption (crash WSL, kill -9, reboot).
            if (! $processAlive && ! $lockHeld) {
                $message = 'Opération interrompue (processus disparu — crash, reboot ou kill).';
                if (! empty($data['safety_archive'])) {
                    $message .= ' Une sauvegarde de secours existe : '.$data['safety_archive'].'.';
                }
                $this->status->write([
                    'state' => 'interrupted',
                    'phase' => 'interrupted',
                    'progress' => 100,
                    'finished_at' => now()->toIso8601String(),
                    'message' => $message,
                ]);
                $this->status->appendLog($message, false, 'interrupted', 100);
                $actions[] = 'restore_marked_interrupted';
                $reconciled = true;
                $data = $this->status->read();
            }
        }

        $cleaned = $this->cleanupOrphanWorkDirs();
        if ($cleaned > 0) {
            $actions[] = 'cleaned_orphan_dirs:'.$cleaned;
            $reconciled = true;
        }

        $maintenance = app()->isDownForMaintenance();
        $stillBusy = $this->status->isBusy() || $this->backups->isLocked();

        // Si la maintenance a été laissée par une restauration morte, on sort.
        if ($maintenance && ! $stillBusy) {
            try {
                Artisan::call('up');
                $actions[] = 'maintenance_cleared';
                $reconciled = true;
                $maintenance = false;
                $this->status->appendLog(
                    'Mode maintenance levé automatiquement après interruption.',
                    false,
                );
                Log::warning('ProjectBackupRecovery : mode maintenance levé (processus restauration absent).');
            } catch (\Throwable $e) {
                $actions[] = 'maintenance_clear_failed';
                Log::error('ProjectBackupRecovery : impossible de lever la maintenance', [
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return [
            'restore_status' => $this->status->read(),
            'operation_locked' => $this->backups->isLocked() || $this->status->isBusy(),
            'maintenance' => $maintenance,
            'reconciled' => $reconciled,
            'actions' => $actions,
        ];
    }

    private function isProcessAlive(int $pid): bool
    {
        if ($pid <= 0) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $out = [];
            exec('tasklist /FI "PID eq '.$pid.'" 2>NUL', $out);

            return implode("\n", $out) !== '' && str_contains(implode("\n", $out), (string) $pid);
        }

        // posix_kill(pid, 0) teste l’existence sans signaler.
        if (function_exists('posix_kill')) {
            return @posix_kill($pid, 0);
        }

        return is_dir('/proc/'.$pid);
    }

    /**
     * Supprime les dossiers .staging_* / .restore_* orphelins si aucune op. active.
     */
    private function cleanupOrphanWorkDirs(): int
    {
        if ($this->backups->isLocked() || $this->status->isBusy()) {
            return 0;
        }

        $root = $this->backups->resolvedBackupDirectory();
        if (! is_dir($root)) {
            return 0;
        }

        $removed = 0;
        foreach (scandir($root) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            if (! str_starts_with($name, '.staging_') && ! str_starts_with($name, '.restore_')) {
                continue;
            }
            $path = $root.DIRECTORY_SEPARATOR.$name;
            if (! is_dir($path)) {
                continue;
            }
            $this->removeDirectory($path);
            $removed++;
        }

        return $removed;
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            /** @var \SplFileInfo $item */
            $path = $item->getPathname();
            if ($item->isDir()) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\YesNoFlags;
use App\Services\NotificationService;
use App\Services\Project\ProjectBackupOperationStatus;
use App\Services\Project\ProjectBackupRestoreService;
use App\Services\Project\ProjectBackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Restaure une archive ZIP v2 (intégrité, secours, maintenance).
 *
 * @example php artisan project:backup:restore project-backup_2026-10-05_04-00-00_1234.zip --yes
 */
class ProjectBackupRestoreCommand extends Command
{
    use AcceptsYesNoFlags;

    protected $signature = 'project:backup:restore
        {name : Nom exact de l’archive ZIP v2}
        {--path= : Répertoire des sauvegardes}
        {--no-safety-backup : Ne pas créer de sauvegarde de secours avant restauration}
        {--skip-notify : Ne pas notifier les admins}
        {--write-status : Écrire le suivi fichier .restore-status.json}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Restaure BDD + storage/app + private/game depuis une archive ZIP vérifiée';

    public function handle(): int
    {
        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        $startedAt = microtime(true);
        $name = (string) $this->argument('name');

        try {
            $pathOpt = $this->option('path');
            $backups = ProjectBackupService::fromConfig(
                is_string($pathOpt) && $pathOpt !== '' ? $pathOpt : null
            );
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->warn('La restauration remplace la base, storage/app et private/game.');
        $this->warn('Une sauvegarde de secours sera créée sauf --no-safety-backup.');

        if (! $this->wantsYes()) {
            if ($this->wantsNo()) {
                $this->warn('Restauration annulée.');

                return ArtisanExitCode::FAILURE;
            }
            if (! $this->confirm('Restaurer définitivement « '.$name.' » ?', false)) {
                $this->warn('Restauration annulée.');

                return ArtisanExitCode::FAILURE;
            }
            if (! $this->confirm('Confirmez en tapant le nom exact de l’archive. Continuer ?', false)) {
                $this->warn('Restauration annulée.');

                return ArtisanExitCode::FAILURE;
            }
            $typed = (string) $this->ask('Nom exact de l’archive');
            if ($typed !== $name) {
                $this->error('Le nom saisi ne correspond pas.');

                return ArtisanExitCode::FAILURE;
            }
        }

        $status = (bool) $this->option('write-status')
            ? ProjectBackupOperationStatus::forBackupRoot($backups->resolvedBackupDirectory())
            : null;

        try {
            $backups->acquireLock();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            $status?->write([
                'state' => 'failed',
                'archive' => $name,
                'finished_at' => now()->toIso8601String(),
                'message' => $e->getMessage(),
            ]);

            return ArtisanExitCode::FAILURE;
        }

        // PID pour détecter un crash / reboot (réconciliation UI).
        $status?->write([
            'state' => 'running',
            'archive' => $name,
            'pid' => getmypid(),
            'phase' => 'verify',
            'progress' => 2,
            'message' => 'Processus de restauration démarré…',
        ]);

        try {
            $restorer = new ProjectBackupRestoreService(
                $backups,
                (string) config('project-backup.mysql_path', '') ?: 'mysql',
                max(60, (int) config('project-backup.restore_timeout', 7200)),
            );

            $result = $restorer->restore(
                $name,
                fn (string $m) => $this->line($m),
                fn (string $m) => $this->error($m),
                ! (bool) $this->option('no-safety-backup'),
                $status,
            );
        } finally {
            $backups->releaseLock();
        }

        $this->notifyResult((bool) $result['ok'], $startedAt, $result['message']);

        if ($result['ok']) {
            $this->info($result['message']);

            return ArtisanExitCode::SUCCESS;
        }

        $this->error($result['message']);

        return ArtisanExitCode::FAILURE;
    }

    private function notifyResult(bool $success, float $startedAt, string $message): void
    {
        if ((bool) $this->option('skip-notify')) {
            return;
        }

        try {
            NotificationService::notifyProjectMaintenance(
                'backup',
                $success,
                microtime(true) - $startedAt,
                now()->format('d/m/Y à H:i'),
                'Restauration : '.$message
            );
        } catch (Throwable $e) {
            $this->warn('Notification admin impossible : '.$e->getMessage());
        }
    }
}

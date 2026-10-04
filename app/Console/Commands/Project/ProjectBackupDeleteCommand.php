<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\AcceptsYesNoFlags;
use App\Console\YesNoFlags;
use App\Services\Project\ProjectBackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Supprime une archive de sauvegarde par nom canonique.
 *
 * @example php artisan project:backup:delete project-backup_2026-10-05_04-00-00_1234.zip --yes
 */
class ProjectBackupDeleteCommand extends Command
{
    use AcceptsYesNoFlags;

    protected $signature = 'project:backup:delete
        {name : Nom exact de l’archive (fichier .zip ou libellé legacy)}
        {--path= : Répertoire des sauvegardes}
        '.YesNoFlags::SIGNATURE;

    protected $description = 'Supprime une sauvegarde du répertoire configuré';

    public function handle(): int
    {
        if ($this->abortIfConflictingYesNoFlags()) {
            return ArtisanExitCode::FAILURE;
        }

        try {
            $pathOpt = $this->option('path');
            $service = ProjectBackupService::fromConfig(
                is_string($pathOpt) && $pathOpt !== '' ? $pathOpt : null
            );
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $name = (string) $this->argument('name');

        if (! $this->wantsYes()) {
            if ($this->wantsNo()) {
                $this->warn('Suppression annulée.');

                return ArtisanExitCode::FAILURE;
            }
            if (! $this->confirm('Supprimer définitivement « '.$name.' » ?', false)) {
                $this->warn('Suppression annulée.');

                return ArtisanExitCode::FAILURE;
            }
        }

        try {
            $service->acquireLock();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        try {
            $result = $service->deleteBackup(
                $name,
                fn (string $m) => $this->line($m),
                fn (string $m) => $this->error($m),
            );
        } finally {
            $service->releaseLock();
        }

        if ($result === null) {
            return ArtisanExitCode::FAILURE;
        }

        $this->info('Suppression OK ('.count($result['deleted']).' fichier(s)).');

        return ArtisanExitCode::SUCCESS;
    }
}

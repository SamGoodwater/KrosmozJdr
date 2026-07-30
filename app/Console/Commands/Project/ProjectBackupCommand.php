<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\NotificationService;
use App\Services\Project\ProjectBackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Sauvegarde MySQL/MariaDB/SQLite + archive compressée de storage/app (hors backups), rotation ~1 mois.
 *
 * @example php artisan project:backup
 * @example php artisan project:backup --no-storage
 * @example php artisan project:backup --retention-days=14
 * @example php artisan project:backup --prune-only --dry-run
 */
class ProjectBackupCommand extends Command
{
    protected $signature = 'project:backup
        {--no-database : Exclure le dump SQL (gzip)}
        {--no-storage : Exclure l’archive storage/app}
        {--path= : Répertoire des sauvegardes (défaut : config ou storage/app/backups)}
        {--retention-days= : Jours de conservation des fichiers (défaut : config, 30)}
        {--no-prune : Ne pas supprimer les sauvegardes plus anciennes que la rétention}
        {--prune-only : Exécuter uniquement la purge (pas de nouvelle sauvegarde)}
        {--dry-run : Avec --prune-only ou --no-prune absent : afficher les fichiers qui seraient supprimés}
        {--skip-notify : Ne pas notifier les admins du résultat de la sauvegarde}';

    protected $description = 'Sauvegarde BDD + storage/app compressés, purge des fichiers > rétention (défaut 30 j)';

    public function handle(): int
    {
        $startedAt = microtime(true);
        $service = $this->makeService();

        $pruneOnly = (bool) $this->option('prune-only');
        $dryRun = (bool) $this->option('dry-run');
        $prune = ! (bool) $this->option('no-prune');

        if ($pruneOnly) {
            $n = $service->pruneOldBackups(
                $dryRun,
                fn (string $m) => $this->line($m),
                fn (string $m) => $this->error($m)
            );
            $this->info($dryRun ? "Fichiers concernés (simulation) : {$n}" : "Fichiers supprimés : {$n}");

            $this->notifyBackupResult(true, $startedAt, $dryRun ? "Purge dry-run : {$n} fichier(s) concerné(s)." : "Purge : {$n} fichier(s) supprimé(s).");

            return ArtisanExitCode::SUCCESS;
        }

        $withDatabase = ! (bool) $this->option('no-database');
        $withStorage = ! (bool) $this->option('no-storage');

        if (! $withDatabase && ! $withStorage) {
            $this->error('Indiquez au moins une cible : ne pas passer --no-database et --no-storage ensemble (utilisez --prune-only pour purger seul).');
            $this->notifyBackupResult(false, $startedAt, 'Aucune cible de sauvegarde sélectionnée.');

            return ArtisanExitCode::FAILURE;
        }

        try {
            $result = $service->run(
                $withDatabase,
                $withStorage,
                $prune,
                $dryRun && $prune,
                fn (string $m) => $this->line($m),
                fn (string $m) => $this->error($m)
            );
        } catch (Throwable $e) {
            $this->error('Sauvegarde impossible : '.$e->getMessage());
            $this->notifyBackupResult(false, $startedAt, $e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        if ($result['run_id'] === '' && ($withDatabase || $withStorage)) {
            $this->notifyBackupResult(false, $startedAt, 'Sauvegarde interrompue avant création de run.');

            return ArtisanExitCode::FAILURE;
        }

        if ($result['files'] !== []) {
            $this->info('Sauvegarde terminée : '.count($result['files']).' fichier(s).');
        }

        $this->notifyBackupResult(
            $result['files'] !== [],
            $startedAt,
            $result['files'] !== []
                ? 'Fichier(s) créé(s) : '.count($result['files']).'.'
                : 'Aucun fichier créé.'
        );

        return ArtisanExitCode::SUCCESS;
    }

    private function makeService(): ProjectBackupService
    {
        $pathOpt = $this->option('path');
        $configuredPath = (string) config('project-backup.path', '');
        $backupRoot = is_string($pathOpt) && $pathOpt !== ''
            ? $pathOpt
            : ($configuredPath !== '' ? $configuredPath : storage_path('app/backups'));

        $retentionOpt = $this->option('retention-days');
        $retention = is_numeric($retentionOpt) && (string) $retentionOpt !== ''
            ? max(1, (int) $retentionOpt)
            : max(1, (int) config('project-backup.retention_days', 30));

        $mysqldump = (string) config('project-backup.mysqldump_path', '') ?: 'mysqldump';
        $prefix = (string) config('project-backup.filename_prefix', '') ?: 'project-backup';

        return new ProjectBackupService($backupRoot, $retention, $mysqldump, $prefix);
    }

    private function notifyBackupResult(bool $success, float $startedAt, ?string $message): void
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
                $message
            );
        } catch (Throwable $e) {
            $this->warn('Notification admin impossible : '.$e->getMessage());
        }
    }
}

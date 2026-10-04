<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\NotificationService;
use App\Services\Project\ProjectBackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Sauvegarde atomique ZIP (BDD + storage/app + private/game), rotation ~1 mois.
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
        {--no-storage : Exclure storage/app}
        {--no-game : Exclure private/game}
        {--no-seeder-data : Ne pas réécrire les fichiers de seed depuis la base (hors production)}
        {--path= : Répertoire des sauvegardes (défaut : config ou storage/app/backups)}
        {--retention-days= : Jours de conservation des fichiers (défaut : config, 30)}
        {--no-prune : Ne pas supprimer les sauvegardes plus anciennes que la rétention}
        {--prune-only : Exécuter uniquement la purge (pas de nouvelle sauvegarde)}
        {--dry-run : Avec --prune-only ou --no-prune absent : afficher les fichiers qui seraient supprimés}
        {--skip-notify : Ne pas notifier les admins du résultat de la sauvegarde}';

    protected $description = 'Sauvegarde ZIP (BDD + storage/app + private/game), purge > rétention (défaut 30 j)';

    public function handle(): int
    {
        $startedAt = microtime(true);

        try {
            $service = $this->makeService();
        } catch (Throwable $e) {
            $this->error('Configuration sauvegarde invalide : '.$e->getMessage());
            $this->notifyBackupResult(false, $startedAt, $e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $pruneOnly = (bool) $this->option('prune-only');
        $dryRun = (bool) $this->option('dry-run');
        $prune = ! (bool) $this->option('no-prune');

        try {
            $service->acquireLock();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            $this->notifyBackupResult(false, $startedAt, $e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        try {
            if ($pruneOnly) {
                $n = $service->pruneOldBackups(
                    $dryRun,
                    fn (string $m) => $this->line($m),
                    fn (string $m) => $this->error($m)
                );
                $message = $dryRun
                    ? "Purge dry-run : {$n} archive(s) concernée(s)."
                    : "Purge : {$n} fichier(s) supprimé(s).";
                $this->info($dryRun ? "Archives concernées (simulation) : {$n}" : "Fichiers supprimés : {$n}");
                $this->notifyBackupResult(true, $startedAt, $message);

                return ArtisanExitCode::SUCCESS;
            }

            $withDatabase = ! (bool) $this->option('no-database');
            $withStorage = ! (bool) $this->option('no-storage');
            $withGame = ! (bool) $this->option('no-game');
            $withSeederData = ! (bool) $this->option('no-seeder-data')
                && ! app()->environment('production');

            if (! $withDatabase && ! $withStorage && ! $withGame && ! $withSeederData) {
                $this->error('Indiquez au moins une cible : BDD, storage, private/game ou fichiers de seed (utilisez --prune-only pour purger seul).');
                $this->notifyBackupResult(false, $startedAt, 'Aucune cible de sauvegarde sélectionnée.');

                return ArtisanExitCode::FAILURE;
            }

            if ((bool) $this->option('no-seeder-data') === false && app()->environment('production')) {
                $this->line('Export des fichiers de seed ignoré (production).');
            }

            try {
                $result = $service->run(
                    $withDatabase,
                    $withStorage,
                    $prune,
                    $dryRun && $prune,
                    fn (string $m) => $this->line($m),
                    fn (string $m) => $this->error($m),
                    $withSeederData,
                    null,
                    $withGame,
                );
            } catch (Throwable $e) {
                $this->error('Sauvegarde impossible : '.$e->getMessage());
                $this->notifyBackupResult(false, $startedAt, $e->getMessage());

                return ArtisanExitCode::FAILURE;
            }

            $ok = (bool) ($result['ok'] ?? false);
            $needsArchive = $withDatabase || $withStorage || $withGame;

            if ($needsArchive && ! $ok) {
                $this->notifyBackupResult(false, $startedAt, 'Aucun fichier créé ou archive invalide.');

                return ArtisanExitCode::FAILURE;
            }

            if ($result['files'] !== []) {
                $this->info('Sauvegarde terminée : '.count($result['files']).' archive(s).');
            }
            if (($result['seeder_exports'] ?? []) !== []) {
                $this->info('Fichiers de seed mis à jour : '.count($result['seeder_exports']).' export(s).');
            }

            $success = $ok || (($result['seeder_exports'] ?? []) !== [] && ! $needsArchive);
            $this->notifyBackupResult(
                $success,
                $startedAt,
                $success
                    ? 'Archive(s) : '.count($result['files']).', export(s) seed : '.count($result['seeder_exports'] ?? []).'.'
                    : 'Aucun fichier créé.'
            );

            return $success ? ArtisanExitCode::SUCCESS : ArtisanExitCode::FAILURE;
        } finally {
            $service->releaseLock();
        }
    }

    private function makeService(): ProjectBackupService
    {
        $pathOpt = $this->option('path');
        $path = is_string($pathOpt) && $pathOpt !== '' ? $pathOpt : null;

        $retentionOpt = $this->option('retention-days');
        $retention = is_numeric($retentionOpt) && (string) $retentionOpt !== ''
            ? max(1, (int) $retentionOpt)
            : null;

        return ProjectBackupService::fromConfig($path, $retention);
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

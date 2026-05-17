<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Point d’entrée pour les tâches récurrentes (scheduler Laravel ou cron système via `schedule:run`).
 *
 * Compose des sous-tâches adaptées au déploiement continu : clears « légers », sauvegardes, etc.,
 * sans option `--cron` sur {@see ProjectClearCommand}.
 *
 * @example php artisan project:cron --clear
 * @example php artisan project:cron --backup
 * @example php artisan project:cron --clear --backup
 */
class ProjectCronCommand extends Command
{
    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:cron
        {--clear : Caches Laravel sûrs + rapports review + cache PHPStan local (voir ProjectRunService)}
        {--backup : Lance project:backup (BDD + storage par défaut, avec purge configurée)}
        {--backup-prune-only : Exécute uniquement la purge vieilles sauvegardes (project:backup --prune-only)}
        {--backup-no-database : (avec --backup) —no-database pour project:backup}
        {--backup-no-storage : (avec --backup) —no-storage pour project:backup}
        {--backup-no-prune : (avec --backup) —no-prune pour project:backup}
        {--backup-dry-run : (--backup ou --backup-prune-only) —dry-run}';

    protected $description = 'Tâches planifiables : clear léger, backups, prune — à brancher sur le Scheduler';

    public function handle(): int
    {
        $wantClear = (bool) $this->option('clear');
        $wantBackup = (bool) $this->option('backup');
        $wantPruneOnly = (bool) $this->option('backup-prune-only');

        if (! $wantClear && ! $wantBackup && ! $wantPruneOnly) {
            $this->warn('Aucune tâche : utilisez au moins --clear, --backup ou --backup-prune-only.');

            return ArtisanExitCode::FAILURE;
        }

        if ($wantBackup && $wantPruneOnly) {
            $this->error('Combinez soit --backup (sauvegarde + prune), soit --backup-prune-only, pas les deux.');

            return ArtisanExitCode::FAILURE;
        }

        if ($wantBackup === false && $wantPruneOnly === false && $this->option('backup-dry-run')) {
            $this->error('L’option --backup-dry-run doit accompagner --backup ou --backup-prune-only.');

            return ArtisanExitCode::FAILURE;
        }

        foreach (['backup-no-database', 'backup-no-storage', 'backup-no-prune'] as $key) {
            if ($wantPruneOnly && $this->option($key)) {
                $this->error("L’option --{$key} n’est pertinente qu’avec --backup.");

                return ArtisanExitCode::FAILURE;
            }
            if ($wantBackup === false && $wantPruneOnly === false && $this->option($key)) {
                $this->error("L’option --{$key} doit accompagner --backup.");

                return ArtisanExitCode::FAILURE;
            }
        }

        $errors = 0;

        if ($wantClear) {
            $this->info('=== project:cron — clear léger (prod-safe) ===');
            $code = $this->projectRunService->runOptionMap([
                'clear:cron' => true,
                'clear:reviews' => true,
                'clear:phpstan-cache' => true,
            ], $this);
            if ($code !== ArtisanExitCode::SUCCESS) {
                $errors++;
            }
            $this->newLine();
        }

        if ($wantPruneOnly) {
            $this->info('=== project:cron — purge des anciennes sauvegardes ===');
            $backupArgs = ['--prune-only' => true];
            if ($this->option('backup-dry-run')) {
                $backupArgs['--dry-run'] = true;
            }
            if ($this->call('project:backup', $backupArgs) !== ArtisanExitCode::SUCCESS) {
                $errors++;
            }
            $this->newLine();
        }

        if ($wantBackup) {
            $this->info('=== project:cron — sauvegarde projet ===');
            $backupArgs = [];
            if ($this->option('backup-no-database')) {
                $backupArgs['--no-database'] = true;
            }
            if ($this->option('backup-no-storage')) {
                $backupArgs['--no-storage'] = true;
            }
            if ($this->option('backup-no-prune')) {
                $backupArgs['--no-prune'] = true;
            }
            if ($this->option('backup-dry-run')) {
                $backupArgs['--dry-run'] = true;
            }
            if ($this->call('project:backup', $backupArgs) !== ArtisanExitCode::SUCCESS) {
                $errors++;
            }
            $this->newLine();
        }

        return $errors > 0 ? ArtisanExitCode::FAILURE : ArtisanExitCode::SUCCESS;
    }
}

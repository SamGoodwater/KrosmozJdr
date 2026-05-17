<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Réinstallation locale lourde : grand ménage disque puis optionnellement `setup --refresh`, `migrate:fresh`, puis clears alignés avec `project:cron --clear`.
 * Interdit en production.
 */
class ProjectRefreshCommand extends Command
{
    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:refresh
        {--hard : Exécuter setup --refresh (vendor + node_modules) avant migrate:fresh}
        {--without-seed : Ne pas passer --seed à migrate:fresh}
        {--force : Ne pas demander confirmation (scripts/CI)}';

    protected $description = 'Réinit locale : après confirmation — grand ménage (clear:deep), setup --hard optionnel, migrate:fresh, puis clears équivalent `project:cron --clear`';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Interdit en production.');

            return ArtisanExitCode::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('⚠️  migrate:fresh détruira toutes les tables. Continuer ?')) {
            $this->info('Annulé.');

            return ArtisanExitCode::FAILURE;
        }

        $this->info('→ Grand ménage local (CSS/pnpm via clear:deep + rapports review + logs Laravel + purge queue locale)');

        $code = $this->projectRunService->runOptionMap(['clear:deep' => true], $this);
        if ($code !== 0) {
            return $code;
        }

        if ($this->option('hard')) {
            $this->info('→ setup --refresh');
            $code = $this->call('setup', ['--refresh' => true]);
            if ($code !== 0) {
                return $code;
            }
        }

        $this->info('→ migrate:fresh');
        $seed = ! $this->option('without-seed');

        $migrateParams = ['--force' => true];
        if ($seed) {
            $migrateParams['--seed'] = true;
        }

        $code = $this->call('migrate:fresh', $migrateParams);
        if ($code !== 0) {
            return $code;
        }

        $this->info('→ Même jeu de clears qu’avec `project:cron --clear`');
        $code = $this->projectRunService->runOptionMap([
            'clear:cron' => true,
            'clear:reviews' => true,
            'clear:phpstan-cache' => true,
        ], $this);
        if ($code !== 0) {
            return $code;
        }

        $this->info('✅ project:refresh terminé.');

        return ArtisanExitCode::SUCCESS;
    }
}

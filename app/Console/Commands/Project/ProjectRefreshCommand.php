<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;

/**
 * Réinstallation lourde : dépendances, base vide, caches. À utiliser en local uniquement.
 */
class ProjectRefreshCommand extends Command
{
    protected $signature = 'project:refresh
        {--hard : Exécuter setup --refresh (vendor + node_modules) avant migrate:fresh}
        {--without-seed : Ne pas passer --seed à migrate:fresh}
        {--force : Ne pas demander confirmation (scripts/CI)}';

    protected $description = 'Repartir sur une base propre : optionnellement réinstaller les libs, migrate:fresh, caches';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Interdit en production.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('⚠️  migrate:fresh détruira toutes les tables. Continuer ?')) {
            $this->info('Annulé.');

            return self::FAILURE;
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

        $this->info('→ run --clear:all');
        $this->call('run', ['--clear:all' => true]);

        $this->info('✅ project:refresh terminé.');

        return self::SUCCESS;
    }
}

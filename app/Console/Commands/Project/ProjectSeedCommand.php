<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

/**
 * Ensemencement « données locales » sans appels réseau DofusDB (types API ni scrapping entités).
 *
 * Délègue à {@see ProjectInitCommand} avec `--skip-scrapping` et `--skip-types` :
 * migrations, seeders Krosmoz, import règles, capacités legacy, sync pages bibliothèque.
 *
 * @example php artisan project:seed
 * @example php artisan project:seed --fresh
 * @example php artisan project:seed --skip-capabilities --skip-specializations
 */
class ProjectSeedCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'project:seed
        {--fresh : migrate:fresh --force avant les seeders}
        {--skip-migrate : Ne pas lancer les migrations}
        {--skip-capabilities : Ne pas importer capabilities:import-legacy}
        {--skip-specializations : Ne pas exécuter SpecializationSeeder (HTML legacy)}
        {--init-scheduler : Afficher la ligne cron scheduler (comme project:init)}
        {--skip-notify : Ne pas notifier les admin à la fin}
        {--skip-super-admin-prompt : Ne pas demander la création du super_admin (CI / scripts)}';

    protected $description = 'Seeders et données locales (sans types ni scrapping DofusDB) — délègue à project:init';

    public function handle(): int
    {
        if (! $this->guardNotProduction(
            'project:seed est interdit en production. Utilisez des migrations et seeders ciblés.'
        )) {
            return ArtisanExitCode::FAILURE;
        }

        $this->info('=== project:seed (données locales, sans DofusDB) ===');
        $this->line('  → project:init --skip-scrapping --skip-types');
        $this->newLine();

        return $this->call('project:init', $this->buildProjectInitArguments());
    }

    /**
     * @return array<string, bool>
     */
    private function buildProjectInitArguments(): array
    {
        $arguments = [
            '--skip-scrapping' => true,
            '--skip-types' => true,
        ];

        foreach ([
            'fresh',
            'skip-migrate',
            'skip-capabilities',
            'skip-specializations',
            'init-scheduler',
            'skip-notify',
            'skip-super-admin-prompt',
        ] as $name) {
            if ($this->option($name)) {
                $arguments['--'.$name] = true;
            }
        }

        return $arguments;
    }
}

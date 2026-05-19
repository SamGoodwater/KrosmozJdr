<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Réinstallation locale lourde : grand ménage disque puis optionnellement `setup --refresh`,
 * puis **pipeline complet** {@see ProjectInitCommand} (`project:init --fresh`) pour aligner la base
 * avec l’init standard (seeders, règles, capacités, types, scrapping selon options).
 * Termine par les clears alignés sur `project:cron --clear`.
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
        {--hard : Exécuter setup --refresh (vendor + node_modules) avant le pipeline d’init}
        {--without-seed : Transmet --skip-seeders à project:init (schéma migré, sans données seedées)}
        {--skip-scrapping : Transmet à project:init — accélère fortement la réinit}
        {--fast : Raccourci : --skip-scrapping et --skip-types (données locales sans DofusDB)}
        {--noimage : Transmet à project:init — pas de téléchargement d’images}
        {--skip-types : Transmet à project:init — pas de types DofusDB (API)}
        {--force : Ne pas demander confirmation (scripts/CI) ; transmet aussi --skip-super-admin-prompt à project:init}';

    protected $description = 'Réinit locale : grand ménage, setup --hard optionnel, project:init --fresh (pipeline complet), puis clears type project:cron --clear';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Interdit en production.');

            return ArtisanExitCode::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('⚠️  Cette commande exécute migrate:fresh via project:init et détruira toutes les tables. Continuer ?')) {
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

        $this->info('→ project:init --fresh (pipeline complet : migrations, seeders, règles, capacités, types, scrapping selon options)');

        $code = $this->call('project:init', $this->buildProjectInitArguments());
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

    /**
     * @return array<string, bool|string>
     */
    private function buildProjectInitArguments(): array
    {
        $arguments = [
            '--fresh' => true,
        ];

        if ($this->option('force') || ! $this->input->isInteractive()) {
            $arguments['--skip-super-admin-prompt'] = true;
        }

        if ($this->option('without-seed')) {
            $arguments['--skip-seeders'] = true;
        }

        if ($this->option('fast')) {
            $arguments['--skip-scrapping'] = true;
            $arguments['--skip-types'] = true;
        }

        foreach (['skip-scrapping', 'noimage', 'skip-types'] as $name) {
            if ($this->option($name)) {
                $arguments['--'.$name] = true;
            }
        }

        return $arguments;
    }
}

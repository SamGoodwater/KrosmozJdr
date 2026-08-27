<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectClearService;
use Illuminate\Console\Command;

/**
 * Vide les caches applicatifs et, hors production, permet un nettoyage large.
 *
 * En production, `--all` et `--safe` partagent le même périmètre (caches Laravel + review + PHPStan).
 *
 * @example php artisan project:clear --safe
 * @example php artisan project:clear --cache
 */
class ProjectClearCommand extends Command
{
    public function __construct(
        private readonly ProjectClearService $projectClearService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:clear
        {--safe : Caches Laravel sûrs + rapports review + cache PHPStan (preset cron / prod)}
        {--all : Production : identique à --safe. Local : tous les clears + review + phpstan}
        {--test : Supprimer uniquement les artefacts de tests (PHPUnit, coverage, storage/framework/testing)}
        {--reviews : Supprimer uniquement les rapports project:review (storage/app/dev-reports)}
        {--backups : Supprimer les fichiers dans le dossier des sauvegardes project}
        {--logs : Supprimer les fichiers *.log dans storage/logs}
        {--phpstan-cache : Supprimer le dossier storage/phpstan}
        {--kill : Arrêter les processus écoutant les ports 8000, 8001, 8002, 5173}
        {--css : Supprimer les CSS générés (pnpm)}
        {--cache : Vider le cache applicatif Laravel}
        {--config : Vider le cache config Laravel}
        {--route : Vider les routes compilées Laravel}
        {--view : Vider les vues compilées Laravel}
        {--debugbar : debugbar:clear}
        {--queue : Purger les jobs non terminés de la connexion queue par défaut (interdit en production)}
        {--schedule : Vider le cache du planificateur}
        {--event : event:clear}
        {--optimize : Laravel optimize:clear}';

    protected $description = 'Nettoie caches (en prod : --safe) et artefacts ponctuels en local';

    public function handle(): int
    {
        $production = app()->environment('production');

        if ($this->option('safe') || ($this->option('all') && $production)) {
            return $this->projectClearService->clearSafe($this);
        }

        if ($this->option('all')) {
            return $this->projectClearService->clearLocalAll($this);
        }

        $ran = false;

        if ($this->option('test')) {
            $this->projectClearService->clearTestArtifacts($this);
            $ran = true;
        }
        if ($this->option('kill')) {
            if ($production) {
                $this->error('En production, --kill est interdit.');

                return ArtisanExitCode::FAILURE;
            }
            $this->projectClearService->killServers($this);
            $ran = true;
        }

        $granular = [
            'css' => 'clearCss',
            'cache' => 'clearCache',
            'config' => 'clearConfig',
            'route' => 'clearRoute',
            'view' => 'clearView',
            'debugbar' => 'clearDebugbar',
            'queue' => 'clearQueue',
            'schedule' => 'clearSchedule',
            'event' => 'clearEvent',
            'optimize' => 'clearOptimize',
            'reviews' => 'clearDevReports',
            'backups' => 'clearProjectBackupFiles',
            'logs' => 'clearLaravelLogFiles',
            'phpstan-cache' => 'clearPhpstanStorageCache',
        ];

        foreach ($granular as $cli => $method) {
            if (! $this->option($cli)) {
                continue;
            }
            if ($production && in_array($cli, ['css', 'queue', 'debugbar', 'logs', 'backups'], true)) {
                $this->error("En production, --{$cli} est interdit — utilisez --safe.");

                return ArtisanExitCode::FAILURE;
            }
            $this->projectClearService->{$method}($this);
            $ran = true;
        }

        if (! $ran) {
            $this->warn('Indiquez au moins une option — en prod : `--safe`.');

            return ArtisanExitCode::FAILURE;
        }

        return ArtisanExitCode::SUCCESS;
    }
}

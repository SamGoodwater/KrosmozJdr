<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Vide les caches applicatifs et, hors production, permet un nettoyage large (pnpm, queues, artefacts).
 *
 * En **production**, `--all` active uniquement le jeu de clears « sûrs » (sans queue ni pnpm) + rapports dev-reviews +
 * dossier PHPStan sous `storage/`. Pour un enchaînement planifié avec d’autres tâches, préférez {@see ProjectCronCommand}.
 *
 * Pour le flux local « repart de zéro », préférer `project:refresh` / `project:reset`.
 *
 * @see ProjectRunService
 */
class ProjectClearCommand extends Command
{
    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:clear|project:clean
        {--all : Production : même périmètre sûr qu’avec `project:cron --clear`. Local : tous les clears + review + phpstan}
        {--test : Supprimer uniquement les artefacts de tests (PHPUnit, coverage, storage/framework/testing)}
        {--reviews : Supprimer uniquement les rapports `project:review` / `storage/app/dev-reports/`}
        {--backups : Supprimer les fichiers dans le dossier des sauvegardes project (`project-backup`, sous la racine du dépôt uniquement)}
        {--logs : Supprimer les fichiers `*.log` dans `storage/logs` (garde `.gitignore`)}
        {--phpstan-cache : Supprimer le dossier `storage/phpstan` (régénéré au prochain PHPStan)}
        {--kill : Arrêter les processus écoutant les ports 8000, 8001, 8002, 5173}
        {--css : Supprimer les CSS générés (pnpm)}
        {--cache : Vider le cache applicatif Laravel}
        {--config : Vider le cache config Laravel}
        {--route : Vider les routes compilées Laravel}
        {--view : Vider les vues compilées Laravel}
        {--debugbar : `debugbar:clear`}
        {--queue : Purger les jobs « non terminés » de la connexion queue par défaut (interdit en production)}
        {--schedule : Vider le cache du planificateur (`schedule:clear-cache`)}
        {--event : `event:clear`}
        {--optimize : Laravel `optimize:clear`}';

    protected $description = 'Nettoie caches (en prod : privilégier --all ou `project:cron --clear`) et artefacts ponctuels en local';

    public function handle(): int
    {
        $production = app()->environment('production');

        $map = [];

        if ($this->option('all') && $production) {
            $map['clear:cron'] = true;
            $map['clear:reviews'] = true;
            $map['clear:phpstan-cache'] = true;
        } elseif ($this->option('all')) {
            $map['clear:all'] = true;
            $map['clear:reviews'] = true;
            $map['clear:phpstan-cache'] = true;
        }

        $presetClear = isset($map['clear:cron']) || isset($map['clear:all']);

        if (! $presetClear) {
            if ($this->option('test')) {
                $map['clear:test'] = true;
            }
            if ($this->option('kill')) {
                $map['kill'] = true;
            }

            foreach ([
                'css' => 'clear:css',
                'cache' => 'clear:cache',
                'config' => 'clear:config',
                'route' => 'clear:route',
                'view' => 'clear:view',
                'debugbar' => 'clear:debugbar',
                'queue' => 'clear:queue',
                'schedule' => 'clear:schedule',
                'event' => 'clear:event',
                'optimize' => 'clear:optimize',
            ] as $cli => $runKey) {
                if ($this->option($cli)) {
                    $map[$runKey] = true;
                }
            }
        }

        /** Le preset « cron-safe » évite les doublons si l’on ajoute encore --reviews / --phpstan-cache depuis le CLI */
        $suppressAccumulatorExtrasFromGranularCli = isset($map['clear:cron']);

        if (! $suppressAccumulatorExtrasFromGranularCli) {
            if ($this->option('reviews')) {
                $map['clear:reviews'] = true;
            }
            if ($this->option('backups')) {
                $map['clear:backups'] = true;
            }
            if ($this->option('logs')) {
                $map['clear:logs'] = true;
            }
            if ($this->option('phpstan-cache')) {
                $map['clear:phpstan-cache'] = true;
            }
        }

        if ($map === []) {
            $this->warn('Indiquez au moins une option — en prod : `--all` ou `php artisan project:cron --clear`.');

            return ArtisanExitCode::FAILURE;
        }

        if ($production && ! $this->mapAllowedInProduction($map)) {
            return ArtisanExitCode::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }

    /**
     * @param  array<string, mixed>  $map
     */
    private function mapAllowedInProduction(array $map): bool
    {
        $forbiddenLabels = [];

        foreach ([
            'kill' => '--kill',
            'clear:test' => '--test',
            'clear:deep' => 'clear interne local',
            'clear:logs' => '--logs',
            'clear:backups' => '--backups',
            'clear:css' => '--css',
            'clear:queue' => '--queue',
            'clear:debugbar' => '--debugbar',
            'clear:all' => 'clear « tout » développeur (pnpm/queue)',
        ] as $key => $human) {
            if ($this->mapHasTruthy($map, $key)) {
                $forbiddenLabels[] = $human;
            }
        }

        if ($forbiddenLabels !== []) {
            $this->error('En production, les options suivantes sont interdites : '.implode(', ', $forbiddenLabels).' — utilisez `php artisan project:cron --clear` ou `project:clear --all`.');

            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $map
     */
    private function mapHasTruthy(array $map, string $key): bool
    {
        $v = $map[$key] ?? false;

        return $v === true || $v === 1 || $v === '1';
    }
}

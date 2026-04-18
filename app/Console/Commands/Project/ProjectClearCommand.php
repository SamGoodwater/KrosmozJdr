<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Nettoyages caches / vues / queues ({@see ProjectRunService}). L’option --test retire uniquement les artefacts PHPUnit / coverage / storage testing.
 */
class ProjectClearCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:clear
        {--all : Tout nettoyer (cache, config, routes, vues, CSS générés, etc.)}
        {--test : Supprimer uniquement les artefacts de tests (PHPUnit, coverage, storage/framework/testing)}
        {--kill : Arrêter les serveurs sur les ports 8000, 8001, 8002, 5173}
        {--css : Supprimer les CSS générés}
        {--cache : Vider le cache applicatif}
        {--config : Vider la config}
        {--route : Vider les routes}
        {--view : Vider les vues}
        {--debugbar : Vider le debugbar}
        {--queue : Vider la queue}
        {--schedule : Vider le cache du planificateur}
        {--event : Vider les événements}
        {--optimize : Vider optimize (avant rebuild)}';

    protected $description = 'Nettoie caches et artefacts du projet.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        $map = [];
        if ($this->option('test')) {
            $map['clear:test'] = true;
        }
        if ($this->option('all')) {
            $map['clear:all'] = true;
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

        if ($map === []) {
            $this->warn('Indiquez au moins une option (--all, --kill, --cache, …).');

            return self::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }
}

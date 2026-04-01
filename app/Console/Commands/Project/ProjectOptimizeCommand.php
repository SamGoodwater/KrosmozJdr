<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * IDE Helper et optimise Laravel ({@see ProjectRunService}).
 */
class ProjectOptimizeCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:optimize
        {--all : IDE Helper + optimize:clear Laravel}
        {--ide : Générer les fichiers IDE Helper uniquement}
        {--laravel : Nettoyer les optimisations Laravel uniquement}';

    protected $description = 'Régénère IDE Helper et/ou nettoie les optimisations Laravel.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        $map = [];
        if ($this->option('all')) {
            $map['optimise:all'] = true;
        } else {
            if ($this->option('ide')) {
                $map['optimise:ide'] = true;
            }
            if ($this->option('laravel')) {
                $map['optimise:laravel'] = true;
            }
        }

        if ($map === []) {
            $this->warn('Indiquez --all, --ide et/ou --laravel.');

            return self::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }
}

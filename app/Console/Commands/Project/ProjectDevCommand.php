<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Environnement de développement : préparation locale puis serveurs PHP + Vite.
 * Délègue à {@see ProjectRunService}.
 */
class ProjectDevCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:dev
        {--prepare : Nettoyage complet + deps de base + optimisations + migrations}
        {--migrate : Migrations uniquement (setup --db)}
        {--watch : Mode watch CSS au lieu du serveur dev optimisé}';

    protected $description = 'Prépare l’environnement dev et lance PHP + Vite (via ProjectRunService).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        if ($this->option('prepare')) {
            return $this->projectRunService->runOptionMap(['prepare' => true], $this);
        }

        if ($this->option('migrate')) {
            return $this->projectRunService->runOptionMap(['migrate' => true], $this);
        }

        if ($this->option('watch')) {
            return $this->projectRunService->runOptionMap(['dev:watch' => true], $this);
        }

        return $this->projectRunService->runOptionMap(['dev' => true], $this);
    }
}

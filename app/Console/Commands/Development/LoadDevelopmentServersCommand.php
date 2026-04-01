<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Alias de confort vers le même flux que `project:dev` (serveur Laravel + Vite).
 *
 * Pour lancer aussi la file d’attente et le CSS en parallèle : `composer run dev` (voir composer.json).
 */
class LoadDevelopmentServersCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'server:load';

    protected $description = 'Lance l’environnement dev (optimize + ProjectRunService, comme project:dev)';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Cette commande est interdite en production.')) {
            return self::FAILURE;
        }

        $this->info('Optimisation Laravel (cache)…');
        $this->call('optimize');

        return $this->projectRunService->runOptionMap(['dev' => true], $this);
    }
}

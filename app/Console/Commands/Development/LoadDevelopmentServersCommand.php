<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

/**
 * Alias de confort vers `project:dev` (prepare + optimize + serveurs Laravel + Vite).
 *
 * Pour lancer aussi la file d’attente et le CSS en parallèle : `composer run dev` (voir composer.json).
 */
class LoadDevelopmentServersCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'server:load';

    protected $description = 'Équivalent à project:dev (prepare, optimize, puis serveurs)';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Cette commande est interdite en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        return $this->call('project:dev');
    }
}

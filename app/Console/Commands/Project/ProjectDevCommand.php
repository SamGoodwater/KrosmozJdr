<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Environnement de développement : project:prepare et project:optimize par défaut, puis serveurs PHP + Vite.
 *
 * @example php artisan project:dev
 * @example php artisan project:dev --no-prepare --no-optimize
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
        {--no-prepare : Ne pas exécuter project:prepare avant les serveurs}
        {--no-optimize : Ne pas exécuter project:optimize avant les serveurs}
        {--prepare : Exécuter uniquement project:prepare puis quitter}
        {--clear : Supprimer les artefacts de tests avant project:prepare (équiv. project:prepare --clear)}
        {--migrate : Migrations uniquement (setup --db) puis quitter}
        {--watch : Mode watch CSS au lieu du serveur dev optimisé}';

    protected $description = 'Prépare (CSS, doc, migrations) et optimise le projet, puis lance PHP + Vite.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return self::FAILURE;
        }

        if ($this->option('migrate')) {
            return $this->projectRunService->runOptionMap(['migrate' => true], $this);
        }

        $prepareOptions = ['--clear' => $this->option('clear')];

        if ($this->option('prepare')) {
            return $this->call('project:prepare', $prepareOptions);
        }

        if (! $this->option('no-prepare')) {
            if ($this->call('project:prepare', $prepareOptions) !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        if (! $this->option('no-optimize')) {
            if ($this->call('project:optimize') !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        if ($this->option('watch')) {
            return $this->projectRunService->runOptionMap(['dev:watch' => true], $this);
        }

        return $this->projectRunService->runOptionMap(['dev' => true], $this);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Prépare l’environnement de dev : rebuild CSS, caches vues, documentation, migrations.
 *
 * @example php artisan project:prepare
 * @example php artisan project:prepare --clear
 * @example php artisan project:prepare --dev
 */
class ProjectPrepareCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:prepare
        {--clear : Supprimer les artefacts de tests (PHPUnit, coverage, storage/framework/testing) avant la préparation}
        {--dev : Après la préparation, enchaîner project:optimize puis les serveurs (comme project:dev sans double préparation)}';

    protected $description = 'Rebuild CSS, vide caches applicatifs/vues, régénère la doc, exécute les migrations.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->option('clear')) {
            $this->projectRunService->clearTestArtifacts($this);
        }

        $this->info('=== project:prepare ===');

        if ($this->projectRunService->runProjectPrepare($this) !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        if (! $this->option('dev')) {
            return ArtisanExitCode::SUCCESS;
        }

        if ($this->call('project:optimize') !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        return $this->projectRunService->runOptionMap(['dev' => true], $this);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectClearService;
use App\Services\Project\ProjectPrepareService;
use Illuminate\Console\Command;

/**
 * Prépare l’environnement de dev : rebuild CSS, caches, documentation, migrations, IDE / optimize.
 *
 * @example php artisan project:prepare
 * @example php artisan project:prepare --clear
 */
class ProjectPrepareCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectPrepareService $projectPrepareService,
        private readonly ProjectClearService $projectClearService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:prepare
        {--clear : Supprimer les artefacts de tests (PHPUnit, coverage, storage/framework/testing) avant la préparation}';

    protected $description = 'Rebuild CSS, vide caches/vues, régénère la doc, migrations, puis IDE Helper / optimize.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->option('clear')) {
            $this->projectClearService->clearTestArtifacts($this);
        }

        $this->info('=== project:prepare ===');

        return $this->projectPrepareService->prepare($this);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectPrepareService;
use App\Services\Project\RefusesRootExecution;
use Illuminate\Console\Command;

/**
 * Met à jour Composer et pnpm, optionnellement la stack système, puis le pipeline IDE / optimize.
 *
 * @example php artisan project:deps
 * @example php artisan project:deps --with-system
 * @example php artisan project:deps --composer --pnpm
 */
class ProjectDepsCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectPrepareService $projectPrepareService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:deps
        {--all : composer update + pnpm up + pipeline optimize (défaut si aucune cible explicite)}
        {--with-system : apt / outils via setup --update (avant composer & pnpm en mode --all)}
        {--apt : setup --update uniquement}
        {--composer : composer update}
        {--pnpm : pnpm up}';

    protected $description = 'Met à jour les dépendances (Composer, pnpm), optionnellement la stack OS ; le mode par défaut enchaîne optimize.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Utilisez des déploiements contrôlés en production, pas project:deps.')) {
            return ArtisanExitCode::FAILURE;
        }

        if (RefusesRootExecution::abort($this)) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->wantsAll()) {
            return $this->runFullDeps();
        }

        $ran = false;

        if ($this->option('with-system') || $this->option('apt')) {
            $this->projectPrepareService->runSetupUpdate($this);
            $ran = true;
        }

        if ($this->option('composer')) {
            if ($this->projectPrepareService->updateComposer($this) !== ArtisanExitCode::SUCCESS) {
                return ArtisanExitCode::FAILURE;
            }
            $ran = true;
        }

        if ($this->option('pnpm')) {
            if ($this->projectPrepareService->updatePnpm($this) !== ArtisanExitCode::SUCCESS) {
                return ArtisanExitCode::FAILURE;
            }
            $ran = true;
        }

        if (! $ran) {
            $this->warn('Aucune cible : utilisez le mode par défaut, --all, ou --with-system / --composer / --pnpm / --apt.');

            return ArtisanExitCode::FAILURE;
        }

        return ArtisanExitCode::SUCCESS;
    }

    private function runFullDeps(): int
    {
        if ($this->option('with-system')) {
            $this->projectPrepareService->runSetupUpdate($this);
        }

        if ($this->projectPrepareService->updateComposer($this) !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->projectPrepareService->updatePnpm($this) !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        return $this->projectPrepareService->optimize($this);
    }

    private function wantsAll(): bool
    {
        if ($this->option('all')) {
            return true;
        }

        return ! $this->option('with-system')
            && ! $this->option('apt')
            && ! $this->option('composer')
            && ! $this->option('pnpm');
    }
}

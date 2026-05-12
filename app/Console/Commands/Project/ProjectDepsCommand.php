<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Met à jour les dépendances du projet (Composer, pnpm), optionnellement la stack système,
 * puis enchaîne {@see ProjectOptimizeCommand} lorsque l’on utilise le mode « tout ».
 *
 * @example php artisan project:deps
 * @example php artisan project:deps --with-system
 * @example php artisan project:deps --composer --pnpm --optimize
 */
class ProjectDepsCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:deps
        {--all : composer update + pnpm up + project:optimize (défaut si aucune cible explicite)}
        {--with-system : apt / outils via setup --update (avant composer & pnpm en mode --all)}
        {--apt : setup --update uniquement (équivalent système)}
        {--composer : composer update}
        {--pnpm : pnpm up}
        {--css : rebuild CSS}
        {--docs : index + schéma documentation}
        {--dump : composer dump-autoload}
        {--migrate : migrations (setup --db)}
        {--optimize : enchaîne project:optimize après les autres cibles (hors mode --all)}';

    protected $description = 'Met à jour les dépendances (Composer, pnpm), optionnellement la stack OS ; le mode par défaut enchaîne project:optimize.';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Utilisez des déploiements contrôlés en production, pas project:deps.')) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->wantsAll()) {
            return $this->runFullDeps();
        }

        $ran = false;

        if ($this->option('with-system') || $this->option('apt')) {
            $this->call('setup', ['--update' => true]);
            $ran = true;
        }

        if ($this->option('composer')) {
            if ($this->projectRunService->runComposerProjectUpdate($this) !== ArtisanExitCode::SUCCESS) {
                return ArtisanExitCode::FAILURE;
            }
            $ran = true;
        }

        if ($this->option('pnpm')) {
            if ($this->projectRunService->runPnpmProjectUpdate($this) !== ArtisanExitCode::SUCCESS) {
                return ArtisanExitCode::FAILURE;
            }
            $ran = true;
        }

        $map = [];
        if ($this->option('css')) {
            $map['update:css'] = true;
            $ran = true;
        }
        if ($this->option('docs')) {
            $map['update:docs'] = true;
            $ran = true;
        }
        if ($this->option('dump')) {
            $map['dump'] = true;
            $ran = true;
        }
        if ($this->option('migrate')) {
            $map['migrate'] = true;
            $ran = true;
        }
        if ($this->option('optimize')) {
            $ran = true;
        }

        if (! $ran) {
            $this->warn('Aucune cible : utilisez le mode par défaut, --all, ou au moins une option (--with-system, --composer, …).');

            return ArtisanExitCode::FAILURE;
        }

        $code = $map === [] ? ArtisanExitCode::SUCCESS : $this->projectRunService->runOptionMap($map, $this);
        if ($code !== ArtisanExitCode::SUCCESS) {
            return $code;
        }

        if ($this->option('optimize')) {
            return $this->call('project:optimize');
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * Mode par défaut (aucune option) ou `--all` : dépendances projet + optimize.
     */
    private function runFullDeps(): int
    {
        if ($this->option('with-system')) {
            $this->call('setup', ['--update' => true]);
        }

        if ($this->projectRunService->runComposerProjectUpdate($this) !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        if ($this->projectRunService->runPnpmProjectUpdate($this) !== ArtisanExitCode::SUCCESS) {
            return ArtisanExitCode::FAILURE;
        }

        return $this->call('project:optimize');
    }

    private function wantsAll(): bool
    {
        if ($this->option('all')) {
            return true;
        }

        return ! $this->hasExplicitGranularTargets();
    }

    private function hasExplicitGranularTargets(): bool
    {
        return $this->option('with-system')
            || $this->option('apt')
            || $this->option('composer')
            || $this->option('pnpm')
            || $this->option('css')
            || $this->option('docs')
            || $this->option('dump')
            || $this->option('migrate')
            || $this->option('optimize');
    }
}

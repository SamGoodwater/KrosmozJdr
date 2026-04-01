<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Met à jour la stack outil (apt, composer, pnpm), regénère les assets CSS, la doc, dump-autoload, migrations.
 * Délègue à {@see ProjectRunService}.
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
        {--all : apt + composer + pnpm + css + docs + dump + migrate (défaut si aucune cible)}
        {--apt : apt update/upgrade (via setup)}
        {--composer : composer update}
        {--pnpm : pnpm update}
        {--css : rebuild CSS}
        {--docs : index + schéma documentation}
        {--dump : composer dump-autoload}
        {--migrate : migrations (setup --db)}
        {--ide : IDE Helper + meta}
        {--laravel-clear : optimize:clear Laravel}';

    protected $description = 'Met à jour apt/composer/pnpm, CSS, doc, autoload, migrations (via ProjectRunService).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Utilisez des déploiements contrôlés en production, pas project:deps.')) {
            return self::FAILURE;
        }

        if ($this->wantsAll()) {
            return $this->projectRunService->runOptionMap([
                'update:all' => true,
                'migrate' => true,
            ], $this);
        }

        $map = [];
        if ($this->option('apt')) {
            $map['update:system'] = true;
        }
        if ($this->option('composer')) {
            $map['update:composer'] = true;
        }
        if ($this->option('pnpm')) {
            $map['update:pnpm'] = true;
        }
        if ($this->option('css')) {
            $map['update:css'] = true;
        }
        if ($this->option('docs')) {
            $map['update:docs'] = true;
        }
        if ($this->option('dump')) {
            $map['dump'] = true;
        }
        if ($this->option('migrate')) {
            $map['migrate'] = true;
        }
        if ($this->option('ide')) {
            $map['optimise:ide'] = true;
        }
        if ($this->option('laravel-clear')) {
            $map['optimise:laravel'] = true;
        }

        if ($map === []) {
            $this->warn('Aucune cible : utilisez --all ou au moins une option (--apt, --composer, …).');

            return self::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }

    private function wantsAll(): bool
    {
        if ($this->option('all')) {
            return true;
        }

        return ! $this->option('apt')
            && ! $this->option('composer')
            && ! $this->option('pnpm')
            && ! $this->option('css')
            && ! $this->option('docs')
            && ! $this->option('dump')
            && ! $this->option('migrate')
            && ! $this->option('ide')
            && ! $this->option('laravel-clear');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;

/**
 * Met à jour la stack outil (apt, composer, pnpm), regénère les assets CSS, la doc, dump-autoload, migrations.
 * Délègue à `run` pour rester DRY avec l’implémentation existante.
 */
class ProjectDepsCommand extends Command
{
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

    protected $description = 'Met à jour apt/composer/pnpm, CSS, doc, autoload, migrations (enveloppe de `run`)';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Utilisez des déploiements contrôlés en production, pas project:deps.');

            return self::FAILURE;
        }

        if ($this->wantsAll()) {
            return $this->call('run', [
                '--update:all' => true,
                '--migrate' => true,
            ]);
        }

        $args = [];
        if ($this->option('apt')) {
            $args['--update:system'] = true;
        }
        if ($this->option('composer')) {
            $args['--update:composer'] = true;
        }
        if ($this->option('pnpm')) {
            $args['--update:pnpm'] = true;
        }
        if ($this->option('css')) {
            $args['--update:css'] = true;
        }
        if ($this->option('docs')) {
            $args['--update:docs'] = true;
        }
        if ($this->option('dump')) {
            $args['--dump'] = true;
        }
        if ($this->option('migrate')) {
            $args['--migrate'] = true;
        }
        if ($this->option('ide')) {
            $args['--optimise:ide'] = true;
        }
        if ($this->option('laravel-clear')) {
            $args['--optimise:laravel'] = true;
        }

        if ($args === []) {
            $this->warn('Aucune cible : utilisez --all ou au moins une option (--apt, --composer, …).');

            return self::FAILURE;
        }

        return $this->call('run', $args);
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

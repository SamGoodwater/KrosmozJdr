<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;

/**
 * Environnement de développement : préparation locale puis serveurs PHP + Vite.
 * Délègue à la commande historique `run`.
 */
class ProjectDevCommand extends Command
{
    protected $signature = 'project:dev
        {--prepare : Nettoyage complet + deps de base + optimisations + migrations (équiv. run --regenerate)}
        {--migrate : Migrations uniquement (setup --db)}
        {--watch : Mode watch CSS (run --dev:watch) au lieu du serveur dev optimisé}';

    protected $description = 'Prépare l’environnement dev et lance PHP + Vite (via `run`)';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Interdit en production.');

            return self::FAILURE;
        }

        if ($this->option('prepare')) {
            return $this->call('run', ['--regenerate' => true]);
        }

        if ($this->option('migrate')) {
            return $this->call('run', ['--migrate' => true]);
        }

        if ($this->option('watch')) {
            return $this->call('run', ['--dev:watch' => true]);
        }

        return $this->call('run', ['--dev' => true]);
    }
}

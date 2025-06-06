<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--force : Forcer l\'overwrite des modèles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyage complet et préparation du projet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production')) {
            $this->error('Cette commande ne doit pas être lancée en production !');
            return;
        }

        $this->info('Nettoyage des caches');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info('Génération des fichiers IDE Helper');
        $this->call('ide-helper:models', ['--nowrite' => !$this->option('force')]);
        $this->call('ide-helper:generate');
        $this->call('ide-helper:eloquent');
        $this->call('ide-helper:meta');

        $this->info('Regénération de l\'autoloader de Composer');
        exec('composer dump-autoload', $output, $returnVar);
        if ($returnVar !== 0) {
            $this->error('Erreur lors de composer dump-autoload');
        }

        $this->info('Installation des dépendances pnpm');
        exec('pnpm install', $output, $returnVar);
        if ($returnVar !== 0) {
            $this->error('Erreur lors de pnpm install');
        }

        $this->info('Exécution des migrations');
        $this->call('migrate');

        $this->info('Optimisation du framework Laravel');
        $this->call('optimize');
    }
}

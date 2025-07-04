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
        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9', $output, $returnVar);
        if ($returnVar !== 0) {
            $this->error('Erreur lors de l\'exécution de la commande pour tuer les processus sur les ports 8000, 8001, 8002, et 5173');
        } else {
            $this->info('Processus sur les ports 8000, 8001, 8002, et 5173 terminés avec succès');
        }

        // Supression des fichiers css (custom, reset et theme)
        $cssFiles = [
            resource_path('css/custom.css'),
            resource_path('css/custom.css.map'),
            resource_path('css/reset.css'),
            resource_path('css/reset.css.map'),
            resource_path('css/theme.css'),
            resource_path('css/theme.css.map'),
            resource_path('css/app.css'),
        ];
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                if (unlink($cssFile)) {
                    $this->info('Fichier ' . basename($cssFile) . ' supprimé avec succès.');
                } else {
                    $this->error('Erreur lors de la suppression du fichier ' . basename($cssFile) . '.');
                }
            } else {
                $this->info('Aucun fichier ' . basename($cssFile) . ' à supprimer.');
            }
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

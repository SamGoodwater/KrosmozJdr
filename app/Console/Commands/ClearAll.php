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
    protected $signature = 'clear:all {--force : Forcer l\'overwrite des modèles} {--update-system : Mettre à jour le système (apt update && apt upgrade)} {--update-pnpm : Mettre à jour pnpm} {--run : Lancer composer run dev} {--watch : Lancer composer run dev:css:optimized:watch}';

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

        // Mise à jour du système si l'option est activée
        if ($this->option('update-system')) {
            $this->info('Mise à jour du système...');
            exec('sudo apt update', $output, $returnVar);
            if ($returnVar !== 0) {
                $this->error('Erreur lors de apt update');
            } else {
                $this->info('apt update terminé avec succès');
            }
            
            exec('sudo apt upgrade -y', $output, $returnVar);
            if ($returnVar !== 0) {
                $this->error('Erreur lors de apt upgrade');
            } else {
                $this->info('apt upgrade terminé avec succès');
            }
        }

        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9', $output, $returnVar);
        if ($returnVar !== 0) {
            $this->error('Erreur lors de l\'exécution de la commande pour tuer les processus sur les ports 8000, 8001, 8002 et 5173');
        } else {
            $this->info('Processus sur les ports 8000, 8001, 8002 et 5173 terminés avec succès');
        }

        // Mise à jour de pnpm si l'option est activée
        if ($this->option('update-pnpm')) {
            $this->info('Mise à jour de pnpm...');
            exec('npm install -g pnpm@latest', $output, $returnVar);
            if ($returnVar !== 0) {
                $this->error('Erreur lors de la mise à jour de pnpm');
            } else {
                $this->info('pnpm mis à jour avec succès');
            }
        }

        // Supression des fichiers css (custom, reset et theme)
        $cssFiles = [
            resource_path('css/*.css'),
            resource_path('css/*.css.map'),
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

        $this->info('Exécution des migrations');
        $this->call('migrate');

        $this->info('Optimisation du framework Laravel');
        $this->call('optimize');

        // Lancement du serveur selon les options
        if ($this->option('run') || $this->option('watch')) {
            $this->info('Lancement du serveur...');

            // Déterminer quelle commande lancer
            $command = 'composer run dev:optimized'; // Par défaut
            $description = 'composer run dev:optimized';

            if ($this->option('watch')) {
                $command = 'composer run dev:css:optimized:watch';
                $description = 'composer run dev:css:optimized:watch';
            }

            $this->info("Lancement de $description...");

            // Utilisation de proc_open pour un contrôle complet avec couleurs
            $descriptorspec = [
                0 => STDIN,
                1 => STDOUT,
                2 => STDERR
            ];
            
            $process = proc_open($command, $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                $returnVar = proc_close($process);
                
                if ($returnVar !== 0) {
                    $this->error("Erreur lors de $description");
                } else {
                    $this->info("$description terminé avec succès");
                }
            } else {
                $this->error("Impossible de lancer $description");
            }
        }
    }
}

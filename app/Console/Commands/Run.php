<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Run extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run
        {--update:system : Mise à jour du système (apt update/upgrade)}
        {--update:docs : Générer l’index et le schéma de la doc}
        {--update:pnpm : Mettre à jour pnpm}
        {--install:pnpm : Installer pnpm}
        {--reset:pnpm : Réinitialiser pnpm (supprimer node_modules et pnpm-lock.yaml)}
        {--update:composer : Mettre à jour composer}
        {--install:composer : Installer composer}
        {--reset:composer : Réinitialiser composer (supprimer vendor et composer.lock)}
        {--update:css : Rebuild CSS}
        {--update:all : Tout mettre à jour (system, pnpm, composer, css, docs, dump)}
        {--update:base : Mise à jour base (pnpm, css, docs, dump)}
        {--kill : Tuer les serveurs en cours}
        {--clear:cache : Vider le cache}
        {--clear:config : Vider la config}
        {--clear:route : Vider les routes}
        {--clear:view : Vider les vues}
        {--clear:optimize : Vider les optimisations}
        {--clear:css : Supprimer les CSS générés}
        {--clear:all : Tout nettoyer (cache, config, route, view, css)}
        {--clear:debugbar : Vider le debugbar}
        {--clear:queue : Vider la queue}
        {--clear:schedule : Vider le schedule}
        {--clear:event : Vider les événements}
        {--optimise:ide : Générer les fichiers IDE Helper}
        {--optimise:laravel : Optimiser Laravel}
        {--optimise:all : Optimiser tout (ide + laravel)}
        {--dump : Composer dump-autoload}
        {--migrate : Exécuter les migrations}
        {--dev : Lancer le serveur en mode optimisé}
        {--dev:watch : Lancer le serveur en mode watch}
        {--clean : Nettoyage complet (clear:all, kill, update:base, optimise:all)}
        {--all : Faire tout (kill, clear, update, optimise, migrate, dev)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commande multifonction pour gérer, nettoyer, mettre à jour et lancer le projet (kill, clear, update, optimise, migrate, dev, etc.)';

    public function handle()
    {
        if (app()->environment('production')) {
            $this->error('Cette commande ne doit pas être lancée en production !');
            return;
        }

        $actions = $this->collectActions();
        $this->executeActions($actions);
    }

    /**
     * Collecte toutes les actions à exécuter selon les options, sans doublon.
     * Retourne un tableau d'actions (méthodes à appeler) dans l'ordre logique.
     */
    protected function collectActions()
    {
        $actions = [];
        // Ordre logique : kill -> clear -> update -> optimise -> migrate -> dev

        // 0. RESET (jamais inclus dans --all ou autres options englobantes)
        if ($this->option('reset:pnpm')) $actions[] = 'resetPnpm';
        if ($this->option('reset:composer')) $actions[] = 'resetComposer';

        // 1. KILL
        if ($this->option('kill') || $this->option('clean') || $this->option('all')) $actions[] = 'killServers';

        // 2. CLEAR
        if ($this->option('clear:all') || $this->option('clean') || $this->option('all')) {
            $actions = array_merge($actions, 
                    [
                        'clearCss',
                        'clearCache',
                        'clearConfig',
                        'clearRoute',
                        'clearView',
                        'clearDebugbar',
                        'clearQueue',
                        'clearSchedule',
                        'clearEvent',
                        'clearOptimize',
                    ]
                );
        } else {
            if ($this->option('clear:css')) $actions[] = 'clearCss';
            if ($this->option('clear:cache')) $actions[] = 'clearCache';
            if ($this->option('clear:config')) $actions[] = 'clearConfig';
            if ($this->option('clear:route')) $actions[] = 'clearRoute';
            if ($this->option('clear:view')) $actions[] = 'clearView';
            if ($this->option('clear:debugbar')) $actions[] = 'clearDebugbar';
            if ($this->option('clear:queue')) $actions[] = 'clearQueue';
            if ($this->option('clear:schedule')) $actions[] = 'clearSchedule';
            if ($this->option('clear:event')) $actions[] = 'clearEvent';
            if ($this->option('clear:optimize')) $actions[] = 'clearOptimize';
        }

        // 3. UPDATE/INSTALL
        if ($this->option('update:all') || $this->option('all')) {
            $actions = array_merge($actions, 
                        [
                            'updateSystem',
                            'updatePnpm',
                            'updateComposer',
                            'installPnpm',
                            'installComposer',
                            'updateCss',
                            'updateDocs',
                            'dumpAutoload',
                        ]
                    );
        } elseif ($this->option('update:base') || $this->option('clean')) {
            $actions = array_merge($actions, 
                        [
                            'installPnpm',
                            'installComposer',
                            'updateCss',
                            'updateDocs',
                            'dumpAutoload',
                        ]
                    );
        } else {
            if ($this->option('update:system')) $actions[] = 'updateSystem';
            if ($this->option('update:pnpm')) $actions[] = 'updatePnpm';
            if ($this->option('install:pnpm')) $actions[] = 'installPnpm';
            if ($this->option('update:composer')) $actions[] = 'updateComposer';
            if ($this->option('install:composer')) $actions[] = 'installComposer';
            if ($this->option('update:css')) $actions[] = 'updateCss';
            if ($this->option('update:docs')) $actions[] = 'updateDocs';
            if ($this->option('dump')) $actions[] = 'dumpAutoload';
        }

        // 4. OPTIMISE
        if ($this->option('optimise:all') || $this->option('clean') || $this->option('all')) {
            $actions = array_merge($actions, 
                        [
                            'optimiseIde',
                            'optimiseLaravel',
                        ]
                    );
        } else {
            if ($this->option('optimise:ide')) $actions[] = 'optimiseIde';
            if ($this->option('optimise:laravel')) $actions[] = 'optimiseLaravel';
        }

        // 5. MIGRATE
        if ($this->option('migrate') || $this->option('update:all') || $this->option('update:base') || $this->option('clean') || $this->option('all')) $actions[] = 'runMigrate';

        // 6. DEV
        if ($this->option('dev') || $this->option('all')) $actions[] = 'runDev';
        if ($this->option('dev:watch')) $actions[] = 'runDevWatch';

        // Supprimer les doublons tout en gardant l'ordre
        return array_values(array_unique($actions));
    }

    /**
     * Exécute les actions dans l'ordre logique.
     */
    protected function executeActions(array $actions)
    {
        foreach ($actions as $action) {
            if (method_exists($this, $action)) {
                $this->$action();
            } else {
                $this->error("Action inconnue : $action");
            }
        }
    }

    // === Méthodes unitaires ===
    protected function killServers() {
        $this->info('Arrêt des serveurs sur les ports 8000, 8001, 8002, 5173...');
        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9');
    }
    protected function clearCss() {
        $this->info('Suppression des fichiers CSS générés...');
        exec('pnpm run css:clean');
    }
    protected function clearCache() { $this->call('cache:clear'); }
    protected function clearConfig() { $this->call('config:clear'); }
    protected function clearRoute() { $this->call('route:clear'); }
    protected function clearEvent() { $this->call('event:clear'); }
    protected function clearView() { $this->call('view:clear'); }
    protected function clearDebugbar() { $this->call('debugbar:clear'); }
    protected function clearQueue() { $this->call('queue:clear'); }
    protected function clearSchedule() { $this->call('schedule:clear-cache'); }
    protected function clearOptimize() { $this->call('optimize:clear'); }
    protected function updateSystem() {
        $this->info('Mise à jour du système (Debian uniquement)');
        if (stripos(PHP_OS, 'Linux') !== false && file_exists('/etc/debian_version') && trim(shell_exec('which apt'))) {
            exec('sudo apt update && sudo apt upgrade -y');
        } else {
            $this->warn('Mise à jour système ignorée : cette commande ne fonctionne que sur Debian avec apt.');
        }
    }
    protected function updatePnpm() {
        $this->info('Mise à jour de pnpm...');
        exec('sudo pnpm install -g pnpm@latest');
    }
    protected function updateComposer() {
        $this->info('Mise à jour de composer...');
        exec('sudo composer self-update');

    }
    protected function updateCss() {
        $this->info('Rebuild CSS...');
        exec('pnpm run css');
    }
    protected function updateDocs() {
        $this->info('Génération de l’index et du schéma de la doc...');
        exec('pnpm run update:docs');
    }
    protected function dumpAutoload() {
        $this->info('Composer dump-autoload...');
        exec('composer dump-autoload');
    }
    protected function optimiseIde() {
        $this->info('Génération des fichiers IDE Helper...');
        $this->call('ide-helper:models', ['--nowrite' => true]);
        $this->call('ide-helper:generate');
        $this->call('ide-helper:eloquent');
        $this->call('ide-helper:meta');
    }
    protected function optimiseLaravel() {
        $this->info('Optimisation du framework Laravel...');
        $this->call('optimize');
    }
    protected function runMigrate() {
        $this->info('Exécution des migrations...');
        $this->call('migrate');
    }
    protected function resetPnpm() {
        $this->info('Suppression de node_modules et pnpm-lock.yaml...');
        exec('rm -rf node_modules pnpm-lock.yaml');
        $this->info('Réinstallation des dépendances pnpm...');
        exec('pnpm install');
    }
    protected function resetComposer() {
        $this->info('Suppression de vendor et composer.lock...');
        exec('rm -rf vendor composer.lock');
        $this->info('Réinstallation des dépendances composer...');
        exec('composer install');
    }
    protected function installPnpm() {
        $this->info('Installation des dépendances pnpm...');
        exec('pnpm install');
    }
    protected function installComposer() {
        $this->info('Installation des dépendances composer...');
        exec('composer install');
    }
    protected function runDev() {
        $this->info('Lancement du serveur (optimisé)...');
        $this->runProcess('pnpm run dev:optimized');
    }
    protected function runDevWatch() {
        $this->info('Lancement du serveur (watch)...');
        $this->runProcess('pnpm run dev:css:optimized:watch');
    }
    protected function runProcess($command) {
        $descriptorspec = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
        $process = proc_open($command, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $returnVar = proc_close($process);
            if ($returnVar !== 0) {
                $this->error("Erreur lors de $command");
            } else {
                $this->info("$command terminé avec succès");
            }
        } else {
            $this->error("Impossible de lancer $command");
        }
    }
}

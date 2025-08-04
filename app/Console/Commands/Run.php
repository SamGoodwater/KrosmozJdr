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
        {--update:docs : Générer l\'index et le schéma de la doc}
        {--update:pnpm : Mettre à jour pnpm}
        {--install:pnpm : Installer pnpm}
        {--reset:pnpm : Réinitialiser pnpm (supprimer node_modules et pnpm-lock.yaml)}
        {--update:composer : Mettre à jour composer}
        {--install:composer : Installer composer}
        {--reset:composer : Réinitialiser composer (supprimer vendor et composer.lock)}
        {--reset:all : Réinitialiser tout (pnpm, composer, css, docs, dump)}
        {--reset:full : Reset complet (reset:all + base de données)}
        {--update:css : Rebuild CSS}
        {--update:privilege= : Corriger les permissions du projet (spécifier l\'utilisateur)}
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
        {--regenerate : Nettoyage complet (clear:all, kill, update:base, optimise:all)}
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
        // === VÉRIFICATIONS DE SÉCURITÉ ===
        
        // 1. Empêcher l'exécution en tant que root (sauf pour update:privilege)
        $currentUser = trim(shell_exec('whoami'));
        if ($currentUser === 'root' && !$this->option('update:privilege')) {
            $this->error('⚠️  SÉCURITÉ : Cette commande ne doit pas être exécutée en tant que root !');
            $this->error('Cela pourrait créer des fichiers avec des permissions root et causer des problèmes.');
            $this->line('');
            $this->line('Solutions :');
            $this->line('1. Utilisez un utilisateur normal (non-root)');
            $this->line('2. Si vous devez corriger les permissions, utilisez :');
            $this->line('   php artisan run --update:privilege=nom_utilisateur');
            $this->line('3. Ou utilisez sudo pour exécuter en tant qu\'utilisateur normal :');
            $this->line('   sudo -u nom_utilisateur php artisan run [options]');
            $this->line('');
            return;
        }
        
        // 2. Vérifier l'environnement de production
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
        if ($this->option('reset:all')) $actions[] = 'resetAll';
        if ($this->option('reset:full')) $actions[] = 'resetFull';
        // 1. KILL
        if ($this->option('kill') || $this->option('regenerate') || $this->option('all')) $actions[] = 'killServers';

        // 2. CLEAR
        if ($this->option('clear:all') || $this->option('regenerate') || $this->option('all')) {
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
        } elseif ($this->option('update:base') || $this->option('regenerate')) {
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
            if ($this->option('update:privilege')) $actions[] = 'updatePrivileges';
            if ($this->option('dump')) $actions[] = 'dumpAutoload';
        }

        // 4. OPTIMISE
        if ($this->option('optimise:all') || $this->option('regenerate') || $this->option('all')) {
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
        if ($this->option('migrate') || $this->option('update:all') || $this->option('update:base') || $this->option('regenerate') || $this->option('all')) $actions[] = 'runMigrate';

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
        $result = shell_exec('pnpm run css:clean 2>&1');
        if ($result !== null) {
            $this->info($result);
        }
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
        exec('pnpm install -g pnpm@latest');
    }
    protected function updateComposer() {
        $this->info('Mise à jour de composer...');
        exec('composer self-update');

    }
    protected function updateCss() {
        $this->info('Rebuild CSS...');
        $result = shell_exec('pnpm run css 2>&1');
        if ($result !== null) {
            $this->info($result);
        }
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
        
        $errors = [];
        
        // Supprimer node_modules
        if (is_dir('node_modules')) {
            $result = shell_exec('rm -rf node_modules 2>&1');
            if ($result !== null && !empty(trim($result))) {
                $errors[] = "Erreur lors de la suppression de node_modules: $result";
            } else {
                $this->info('✅ node_modules supprimé');
            }
        } else {
            $this->warn('node_modules n\'existe pas, suppression ignorée');
        }
        
        // Supprimer pnpm-lock.yaml
        if (file_exists('pnpm-lock.yaml')) {
            $result = shell_exec('rm -f pnpm-lock.yaml 2>&1');
            if ($result !== null && !empty(trim($result))) {
                $errors[] = "Erreur lors de la suppression de pnpm-lock.yaml: $result";
            } else {
                $this->info('✅ pnpm-lock.yaml supprimé');
            }
        } else {
            $this->warn('pnpm-lock.yaml n\'existe pas, suppression ignorée');
        }
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }
        }
    }
    
    protected function resetComposer() {
        $this->info('Suppression de vendor et composer.lock...');
        
        $errors = [];
        
        // Supprimer vendor
        if (is_dir('vendor')) {
            $result = shell_exec('rm -rf vendor 2>&1');
            if ($result !== null && !empty(trim($result))) {
                $errors[] = "Erreur lors de la suppression de vendor: $result";
            } else {
                $this->info('✅ vendor supprimé');
            }
        } else {
            $this->warn('vendor n\'existe pas, suppression ignorée');
        }
        
        // Supprimer composer.lock
        if (file_exists('composer.lock')) {
            $result = shell_exec('rm -f composer.lock 2>&1');
            if ($result !== null && !empty(trim($result))) {
                $errors[] = "Erreur lors de la suppression de composer.lock: $result";
            } else {
                $this->info('✅ composer.lock supprimé');
            }
        } else {
            $this->warn('composer.lock n\'existe pas, suppression ignorée');
        }
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }
        }
    }
    protected function resetAll() {
        $this->info('Réinitialisation de tout (pnpm, composer, css, docs, dump)...');
        
        // 1. Arrêter les serveurs en cours
        $this->killServers();
        
        // 2. Nettoyer les caches Laravel
        $this->clearCss();
        $this->clearCache();
        $this->clearConfig();   
        $this->clearRoute();
        $this->clearView();
        $this->clearDebugbar();
        $this->clearQueue();
        $this->clearSchedule();
        $this->clearEvent();
        $this->clearOptimize();
        
        // 3. Supprimer les dépendances
        $this->resetPnpm();
        $this->resetComposer();
        
        // 4. Réinstaller les dépendances
        $this->info('Réinstallation des dépendances...');
        $this->installComposer();
        $this->installPnpm();
        
        // 5. Reconstruire les assets et optimiser
        $this->updateCss();
        $this->updateDocs();
        $this->dumpAutoload();
        $this->optimiseIde();
        $this->optimiseLaravel();
        
        $this->info('✅ Réinitialisation complète terminée !');
    }
    
    protected function resetFull() {
        $this->info('🔄 Reset complet (reset:all + base de données)...');
        
        // Demander confirmation pour le reset complet
        if (!$this->confirm('⚠️  ATTENTION : Cette opération va supprimer toutes les données de la base de données. Continuer ?')) {
            $this->info('Reset complet annulé.');
            return;
        }
        
        // 1. Exécuter le reset:all
        $this->resetAll();
        
        // 2. Reset de la base de données
        $this->info('🔄 Reset de la base de données...');
        
        // Vider toutes les tables
        $this->info('Suppression de toutes les données...');
        $this->call('migrate:fresh');
        
        // Exécuter les seeders si ils existent
        if (file_exists('database/seeders/DatabaseSeeder.php')) {
            $this->info('Exécution des seeders...');
            $this->call('db:seed');
        }
        
        $this->info('✅ Reset complet terminé !');
        $this->warn('⚠️  Toutes les données ont été supprimées et la base a été réinitialisée.');
    }
    
    protected function installPnpm() {
        $this->info('Installation des dépendances pnpm...');
        
        // Vérifier que pnpm est installé
        $pnpmPath = trim(shell_exec('which pnpm 2>/dev/null'));
        if (empty($pnpmPath)) {
            $this->error('pnpm n\'est pas installé. Veuillez l\'installer d\'abord.');
            return false;
        }
        
        // Vérifier que package.json existe
        if (!file_exists('package.json')) {
            $this->error('package.json n\'existe pas. Impossible d\'installer les dépendances pnpm.');
            return false;
        }
        
        $result = shell_exec('pnpm install 2>&1');
        if ($result !== null) {
            $this->info($result);
        }
        
        // Vérifier si l'installation a réussi
        if (is_dir('node_modules')) {
            $this->info('✅ Dépendances pnpm installées avec succès');
            return true;
        } else {
            $this->error('❌ Échec de l\'installation des dépendances pnpm');
            return false;
        }
    }
    
    protected function installComposer() {
        $this->info('Installation des dépendances composer...');
        
        // Vérifier que composer est installé
        $composerPath = trim(shell_exec('which composer 2>/dev/null'));
        if (empty($composerPath)) {
            $this->error('Composer n\'est pas installé. Veuillez l\'installer d\'abord.');
            return false;
        }
        
        // Vérifier que composer.json existe
        if (!file_exists('composer.json')) {
            $this->error('composer.json n\'existe pas. Impossible d\'installer les dépendances composer.');
            return false;
        }
        
        $result = shell_exec('composer install 2>&1');
        if ($result !== null) {
            $this->info($result);
        }
        
        // Vérifier si l'installation a réussi
        if (is_dir('vendor')) {
            $this->info('✅ Dépendances composer installées avec succès');
            return true;
        } else {
            $this->error('❌ Échec de l\'installation des dépendances composer');
            return false;
        }
    }
    protected function runDev() {
        $this->info('Lancement des serveurs de développement...');
        
        // Démarrer le serveur Laravel en arrière-plan
        $this->info('Démarrage du serveur Laravel sur le port 8000...');
        exec('php artisan serve --host=127.0.0.1 --port=8000 > /dev/null 2>&1 &');
        
        // Attendre un peu que Laravel démarre
        sleep(3);
        
        // Vérifier que Laravel fonctionne
        $laravelResponse = @file_get_contents('http://127.0.0.1:8000');
        if ($laravelResponse !== false) {
            $this->info('✅ Serveur Laravel démarré sur http://127.0.0.1:8000');
        } else {
            $this->warn('⚠️ Serveur Laravel en cours de démarrage...');
        }
        
        // Démarrer Vite
        $this->info('Démarrage de Vite sur le port 5173...');
        $this->runProcess('pnpm run dev:optimized');
    }
    protected function runDevWatch() {
        $this->info('Lancement du serveur (watch)...');
        $this->runProcess('pnpm run dev:css:optimized:watch');
    }
    protected function updatePrivileges() {
        $user = $this->option('update:privilege');
        
        // === VÉRIFICATIONS DE SÉCURITÉ ===
        
        // 1. Vérifier que l'utilisateur est spécifié
        if (empty($user)) {
            $this->error('Vous devez spécifier un utilisateur avec --update:privilege=nom_utilisateur');
            return;
        }
        
        // 2. Nettoyer et valider le nom d'utilisateur
        $user = trim($user);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $user)) {
            $this->error('Nom d\'utilisateur invalide. Utilisez uniquement des lettres, chiffres, tirets et underscores.');
            return;
        }
        
        // 3. Vérifier que l'utilisateur existe
        $this->info("Vérification de l'existence de l'utilisateur : $user");
        $userExists = shell_exec("id $user 2>/dev/null");
        if (empty($userExists)) {
            $this->error("L'utilisateur '$user' n'existe pas sur ce système.");
            $this->line("Utilisateurs disponibles :");
            $this->line(shell_exec("cut -d: -f1 /etc/passwd | grep -E '^[a-zA-Z]' | head -10"));
            return;
        }
        
        // 4. Vérifier que nous sommes dans un environnement de développement
        if (app()->environment('production')) {
            $this->error('Cette commande ne doit pas être exécutée en production !');
            return;
        }
        
        // 5. Vérifier que nous sommes dans le bon répertoire (projet Laravel)
        if (!file_exists('artisan') || !file_exists('composer.json')) {
            $this->error('Cette commande doit être exécutée depuis la racine du projet Laravel.');
            return;
        }
        
        // 6. Demander confirmation si l'utilisateur est différent de l'utilisateur actuel
        $currentUser = trim(shell_exec('whoami'));
        if ($user !== $currentUser) {
            $this->warn("Vous êtes actuellement connecté en tant que '$currentUser'");
            $this->warn("Vous allez changer les permissions pour l'utilisateur '$user'");
            
            // En mode non-interactif, on continue automatiquement
            if ($this->option('no-interaction')) {
                $this->info('Mode non-interactif : continuation automatique...');
            } else {
                if (!$this->confirm('Êtes-vous sûr de vouloir continuer ?')) {
                    $this->info('Opération annulée.');
                    return;
                }
            }
        }
        
        // 7. Vérifier les permissions actuelles avant modification
        $this->info('Analyse des permissions actuelles...');
        $rootFiles = trim(shell_exec("find . -user root 2>/dev/null"));
        if (!empty($rootFiles)) {
            $this->warn('Fichiers appartenant à root détectés :');
            $this->line($rootFiles);
        }
        
        // === EXÉCUTION SÉCURISÉE ===
        
        $this->info("Correction des permissions pour l'utilisateur : $user");
        
        try {
            // 1. Changer le propriétaire de tous les fichiers du projet
            $this->info('Changement du propriétaire de tous les fichiers...');
            $result = shell_exec("chown -R $user:$user . 2>&1");
            if ($result !== null) {
                $this->warn("Avertissements lors du changement de propriétaire : $result");
            }
            
            // 2. Corriger les permissions des dossiers critiques Laravel
            $this->info('Correction des permissions des dossiers Laravel...');
            if (is_dir('storage/')) {
                shell_exec("chmod -R 775 storage/");
            }
            if (is_dir('bootstrap/cache/')) {
                shell_exec("chmod -R 775 bootstrap/cache/");
            }
            if (is_dir('public/')) {
                shell_exec("chmod -R 775 public/");
            }
            
            // 3. S'assurer que les fichiers exécutables ont les bonnes permissions
            $this->info('Correction des permissions des fichiers exécutables...');
            if (file_exists('artisan')) {
                shell_exec("chmod 755 artisan");
            }
            shell_exec("find . -name '*.php' -executable -exec chmod 755 {} \\; 2>/dev/null");
            
            // 4. Corriger les permissions de Composer si installé globalement
            $composerPath = trim(shell_exec('which composer 2>/dev/null'));
            if (!empty($composerPath) && file_exists($composerPath)) {
                $this->info('Correction des permissions de Composer...');
                shell_exec("chown $user:$user $composerPath");
                shell_exec("chmod 755 $composerPath");
            }
            
            // 5. Vérifier et corriger les permissions de pnpm si installé globalement
            $pnpmPath = trim(shell_exec('which pnpm 2>/dev/null'));
            if (!empty($pnpmPath) && file_exists($pnpmPath)) {
                $this->info('Correction des permissions de pnpm...');
                shell_exec("chown $user:$user $pnpmPath");
                shell_exec("chmod 755 $pnpmPath");
            }
            
            // 6. Vérification finale
            $this->info('Vérification finale des permissions...');
            $finalRootFiles = trim(shell_exec("find . -user root 2>/dev/null"));
            if (!empty($finalRootFiles)) {
                $this->warn('Fichiers appartenant encore à root détectés :');
                $this->line($finalRootFiles);
                $this->info('Correction automatique...');
                shell_exec("find . -user root -exec chown $user:$user {} \\; 2>/dev/null");
            } else {
                $this->info('✅ Aucun fichier n\'appartient à root');
            }
            
            // 7. Test de validation
            $this->info('Test de validation des permissions...');
            $testResult = shell_exec("su - $user -c 'cd " . getcwd() . " && php artisan --version' 2>&1");
            if (strpos($testResult, 'Laravel Framework') !== false) {
                $this->info('✅ Test de validation réussi : Laravel fonctionne correctement');
            } else {
                $this->warn('⚠️ Test de validation échoué. Vérifiez manuellement les permissions.');
            }
            
            $this->info('✅ Permissions corrigées avec succès !');
            
        } catch (\Exception $e) {
            $this->error('Erreur lors de la correction des permissions : ' . $e->getMessage());
            $this->error('Vérifiez manuellement les permissions du projet.');
        }
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

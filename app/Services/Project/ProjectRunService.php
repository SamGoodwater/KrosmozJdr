<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use PDOException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Orchestration des actions CLI partagées par les commandes `project:*` (clear, deps, dev, effets, etc.).
 *
 * Les actions reçoivent la commande pour I/O et `Artisan::call` imbriqués.
 */
class ProjectRunService
{
    /**
     * Collecte les noms de méthodes à exécuter selon une carte d’options (même clés que l’ancienne commande `run`).
     *
     * @param  array<string, mixed>  $options  Typiquement {@see Command::options()} ou un sous-ensemble (flags à true).
     * @return list<string>
     */
    public function collectActionsFromOptionMap(array $options): array
    {
        $o = $options;
        $actions = [];

        if ($this->opt($o, 'reset:pnpm')) {
            $actions[] = 'runSetupRefresh';
        }
        if ($this->opt($o, 'reset:composer')) {
            $actions[] = 'runSetupRefresh';
        }
        if ($this->opt($o, 'reset:all')) {
            $actions[] = 'resetAll';
        }
        if ($this->opt($o, 'reset:full')) {
            $actions[] = 'resetFull';
        }

        if ($this->opt($o, 'kill')) {
            $actions[] = 'killServers';
        }

        if ($this->opt($o, 'clear:test')) {
            $actions[] = 'clearTestArtifacts';
        }

        $bundledClearsChosen = false;
        $clearDeep = $this->opt($o, 'clear:deep');
        $clearCron = $this->opt($o, 'clear:cron');
        $clearAll = $this->opt($o, 'clear:all');

        if ($clearDeep) {
            $bundledClearsChosen = true;
            $actions = array_merge($actions, $this->clearFullDeveloperStackMethodNames());
            $actions[] = 'clearDevReports';
            $actions[] = 'clearPhpstanStorageCache';
            $actions[] = 'clearLaravelLogFiles';
        } elseif ($clearCron) {
            $bundledClearsChosen = true;
            $actions = array_merge($actions, $this->clearCronSafeLaravelMethodNames());
        } elseif ($clearAll) {
            $bundledClearsChosen = true;
            $actions = array_merge($actions, $this->clearFullDeveloperStackMethodNames());
        }

        if (! $bundledClearsChosen) {
            if ($this->opt($o, 'clear:css')) {
                $actions[] = 'clearCss';
            }
            if ($this->opt($o, 'clear:cache')) {
                $actions[] = 'clearCache';
            }
            if ($this->opt($o, 'clear:config')) {
                $actions[] = 'clearConfig';
            }
            if ($this->opt($o, 'clear:route')) {
                $actions[] = 'clearRoute';
            }
            if ($this->opt($o, 'clear:view')) {
                $actions[] = 'clearView';
            }
            if ($this->opt($o, 'clear:debugbar')) {
                $actions[] = 'clearDebugbar';
            }
            if ($this->opt($o, 'clear:queue')) {
                $actions[] = 'clearQueue';
            }
            if ($this->opt($o, 'clear:schedule')) {
                $actions[] = 'clearSchedule';
            }
            if ($this->opt($o, 'clear:event')) {
                $actions[] = 'clearEvent';
            }
            if ($this->opt($o, 'clear:optimize')) {
                $actions[] = 'clearOptimize';
            }
        }

        if ($this->opt($o, 'clear:reviews')) {
            $actions[] = 'clearDevReports';
        }
        if ($this->opt($o, 'clear:backups')) {
            $actions[] = 'clearProjectBackupFiles';
        }
        if ($this->opt($o, 'clear:logs')) {
            $actions[] = 'clearLaravelLogFiles';
        }
        if ($this->opt($o, 'clear:phpstan-cache')) {
            $actions[] = 'clearPhpstanStorageCache';
        }

        if ($this->opt($o, 'update:all')) {
            $actions[] = 'runSetupInstall';
            $actions[] = 'runComposerProjectUpdate';
            $actions[] = 'runPnpmProjectUpdate';
        } elseif ($this->opt($o, 'update:base')) {
            $actions[] = 'runSetupInstall';
            $actions = array_merge($actions, ['updateCss', 'updateDocs', 'dumpAutoload']);
        } else {
            if ($this->opt($o, 'update:system') || $this->opt($o, 'update:pnpm') || $this->opt($o, 'update:composer')) {
                $actions[] = 'runSetupUpdate';
            }
            if ($this->opt($o, 'install:pnpm') || $this->opt($o, 'install:composer')) {
                $actions[] = 'runSetupInstall';
            }
            if ($this->opt($o, 'update:css')) {
                $actions[] = 'updateCss';
            }
            if ($this->opt($o, 'update:docs')) {
                $actions[] = 'updateDocs';
            }
            if ($this->truthyOrNonEmptyString($o, 'update:privilege')) {
                $actions[] = 'updatePrivileges';
            }
            if ($this->opt($o, 'dump')) {
                $actions[] = 'dumpAutoload';
            }
        }

        if ($this->opt($o, 'optimise:all')) {
            $actions = array_merge($actions, [
                'optimiseIde',
                'optimiseLaravel',
            ]);
        } else {
            if ($this->opt($o, 'optimise:ide')) {
                $actions[] = 'optimiseIde';
            }
            if ($this->opt($o, 'optimise:laravel')) {
                $actions[] = 'optimiseLaravel';
            }
        }

        if ($this->opt($o, 'migrate') || $this->opt($o, 'update:base')) {
            $actions[] = 'runSetupDb';
        }

        if ($this->opt($o, 'check:effects-quality')) {
            $actions[] = 'checkEffectsQuality';
        }
        if ($this->opt($o, 'check:effects-quality:dev')) {
            $actions[] = 'checkEffectsQualityDev';
        }
        if ($this->opt($o, 'pipeline:effects-quality')) {
            $actions[] = 'pipelineEffectsQuality';
        }
        if ($this->opt($o, 'pipeline:effects-quality:dev')) {
            $actions[] = 'pipelineEffectsQualityDev';
        }

        if ($this->opt($o, 'dev')) {
            $actions[] = 'runDev';
        }
        if ($this->opt($o, 'dev:watch')) {
            $actions[] = 'runDevWatch';
        }

        /** @var list<string> */
        return array_values(array_unique($actions));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function opt(array $options, string $key): bool
    {
        $v = $options[$key] ?? null;

        return $v === true || $v === 1 || $v === '1';
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function truthyOrNonEmptyString(array $options, string $key): bool
    {
        $v = $options[$key] ?? null;
        if ($v === true || $v === 1 || $v === '1') {
            return true;
        }
        if (is_string($v) && trim($v) !== '') {
            return true;
        }

        return false;
    }

    /**
     * Laravel uniquement — adapté cron / prod (sans pnpm/queue/Debugbar/CSS).
     *
     * @return list<string>
     */
    private function clearCronSafeLaravelMethodNames(): array
    {
        return [
            'clearCache',
            'clearConfig',
            'clearRoute',
            'clearView',
            'clearSchedule',
            'clearEvent',
            'clearOptimize',
        ];
    }

    /**
     * Nettoyage large local / refresh (pnpm, queue, caches artisans).
     *
     * @return list<string>
     */
    private function clearFullDeveloperStackMethodNames(): array
    {
        return [
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
        ];
    }

    /**
     * Point d’entrée unique pour les commandes `project:*` : carte d’options → actions → exécution.
     *
     * @param  array<string, mixed>  $optionMap
     */
    public function runOptionMap(array $optionMap, Command $command): int
    {
        if ($this->abortIfRootWithoutPrivilegeFix($optionMap, $command)) {
            return Command::FAILURE;
        }

        $ok = $this->executeActions($this->collectActionsFromOptionMap($optionMap), $command);

        return $ok ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Refuse l’exécution en tant que root (sauf flux `update:privilege` / `project:fix-permissions`).
     *
     * @param  array<string, mixed>  $optionMap
     */
    private function abortIfRootWithoutPrivilegeFix(array $optionMap, Command $command): bool
    {
        $currentUser = trim((string) shell_exec('whoami'));
        if ($currentUser !== 'root') {
            return false;
        }
        if ($this->truthyOrNonEmptyString($optionMap, 'update:privilege')) {
            return false;
        }

        $command->error('⚠️  SÉCURITÉ : ces commandes ne doivent pas être exécutées en tant que root !');
        $command->error('Cela pourrait créer des fichiers avec des permissions root et causer des problèmes.');
        $command->line('');
        $command->line('Solutions :');
        $command->line('1. Utilise un utilisateur normal (non-root)');
        $command->line('2. Si tu dois corriger les permissions :');
        $command->line('   php artisan project:fix-permissions nom_utilisateur');
        $command->line('3. Ou : sudo -u nom_utilisateur php artisan project:dev [options]');
        $command->line('');

        return true;
    }

    /**
     * @param  list<string>  $actions
     */
    public function executeActions(array $actions, Command $command): bool
    {
        foreach ($actions as $action) {
            if (! method_exists($this, $action)) {
                $command->error("Action inconnue : $action");

                return false;
            }

            $result = $this->{$action}($command);

            if (is_int($result) && $result !== 0) {
                $command->error("Action en échec : $action (code $result)");

                return false;
            }
        }

        return true;
    }

    public function killServers(Command $command): void
    {
        $command->info('Arrêt des serveurs sur les ports 8000, 8001, 8002, 5173...');
        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9');
    }

    /**
     * Supprime les artefacts laissés par les tests (PHPUnit, couverture, stockage testing).
     * Ne touche pas à la base ni aux dépendances — pour un nettoyage léger avant/après tests.
     */
    public function clearTestArtifacts(Command $command): void
    {
        $command->info('Suppression des artefacts de tests (PHPUnit, coverage, storage/framework/testing)…');

        $base = base_path();
        $paths = [
            $base.'/.phpunit.cache',
            $base.'/.phpunit.result.cache',
            $base.'/coverage',
        ];

        foreach ($paths as $path) {
            if (! File::exists($path)) {
                continue;
            }
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
                $command->line('  Supprimé : '.basename($path).'/');
            } else {
                File::delete($path);
                $command->line('  Supprimé : '.basename($path));
            }
        }

        $testingDir = storage_path('framework/testing');
        if (File::isDirectory($testingDir)) {
            File::cleanDirectory($testingDir);
            $command->line('  Vidé : storage/framework/testing/');
        }
    }

    /**
     * Supprime tout le contenu de `storage/app/dev-reports` (sorties Markdown de `project:review` / `dev:review`).
     */
    public function clearDevReports(Command $command): void
    {
        $command->info('Suppression des rapports `project:review` (`storage/app/dev-reports`)…');
        $dir = storage_path('app/dev-reports');
        File::ensureDirectoryExists($dir);
        $this->purgeDirectoryLeavingGitignore($dir, $command);
        $command->info('✅ Rapports dev-reports nettoyés.');
    }

    /**
     * Supprime les fichiers du dossier défini dans `project-backup` (défaut `storage/app/backups`),
     * uniquement si ce dossier demeure sous la racine du dépôt.
     */
    public function clearProjectBackupFiles(Command $command): void
    {
        $root = ProjectBackupService::fromConfig()->resolvedBackupDirectory();
        $command->info('Suppression des fichiers de sauvegarde (`'.$root.'`)…');

        if (! $this->isDestructiveCleanupPathInsideProjectRoot($root, $command)) {
            return;
        }

        if (! File::isDirectory($root)) {
            $command->line('  (répertoire absent — aucune sauvegarde à retirer)');

            return;
        }

        $this->purgeDirectoryLeavingGitignore($root, $command);
        $command->info('✅ Répertoire des sauvegardes nettoyé.');
    }

    /** Efface tous les fichiers `*.log` dans `storage/logs` (sans toucher `.gitignore`). */
    public function clearLaravelLogFiles(Command $command): void
    {
        $command->info('Suppression des fichiers `*.log` dans `storage/logs`…');
        $dir = storage_path('logs');

        if (! File::isDirectory($dir)) {
            return;
        }

        foreach (glob($dir.DIRECTORY_SEPARATOR.'*.log') ?: [] as $path) {
            if (File::isFile((string) $path)) {
                File::delete((string) $path);
                $command->line('  Supprimé : logs/'.basename((string) $path));
            }
        }

        $command->info('✅ Logs Laravel nettoyés.');
    }

    /** Supprime le cache d’analyse stocké sous `storage/phpstan` (régénéré au prochain lancement de PHPStan). */
    public function clearPhpstanStorageCache(Command $command): void
    {
        $dir = storage_path('phpstan');
        $command->info('Suppression du cache localement stocké sous `storage/phpstan`…');

        if (! File::isDirectory($dir)) {
            return;
        }

        File::deleteDirectory($dir);
        $command->info('✅ Répertoire `storage/phpstan` supprimé.');
    }

    /**
     * @param  non-empty-string  $absoluteDirectory
     */
    private function purgeDirectoryLeavingGitignore(string $absoluteDirectory, Command $command): void
    {
        foreach (glob($absoluteDirectory.DIRECTORY_SEPARATOR.'*') ?: [] as $path) {
            $name = basename((string) $path);
            if ($name === '.gitignore') {
                continue;
            }
            if (File::isDirectory((string) $path)) {
                File::deleteDirectory((string) $path);
                $command->line('  Supprimé : '.$name.'/');
            } elseif (File::exists((string) $path)) {
                File::delete((string) $path);
                $command->line('  Supprimé : '.$name);
            }
        }
    }

    /** Refuse tout chemin hors de {@see base_path()} pour éviter de vider une config `PROJECT_BACKUP_PATH` hors dépôt. */
    private function isDestructiveCleanupPathInsideProjectRoot(string $path, Command $command): bool
    {
        $base = realpath(base_path());
        if ($base === false) {
            $command->warn('Nettoyage annulé : racine projet introuvable.');

            return false;
        }

        $candidate = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);

        $resolved = realpath($candidate);
        if ($resolved !== false) {
            return str_starts_with($resolved, $base.DIRECTORY_SEPARATOR) || $resolved === $base;
        }

        $parent = dirname($candidate);
        $parentResolved = realpath($parent);

        return $parentResolved !== false
            && (str_starts_with($parentResolved, $base.DIRECTORY_SEPARATOR) || $parentResolved === $base);
    }

    public function clearCss(Command $command): void
    {
        $command->info('Suppression des fichiers CSS générés...');
        $result = shell_exec('pnpm run css:clean 2>&1');
        if ($result !== null) {
            $command->info($result);
        }
    }

    public function clearCache(Command $command): void
    {
        try {
            $command->call('cache:clear');
        } catch (QueryException|PDOException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'could not find driver') || str_contains($msg, 'Connection:')) {
                $command->warn('Cache non vidé : driver BDD indisponible (ex. extension pdo_mysql manquante).');
                $command->warn('Installez php-mysql ou définissez CACHE_STORE=file dans .env.');
            } else {
                throw $e;
            }
        }
    }

    public function clearConfig(Command $command): void
    {
        $command->call('config:clear');
    }

    public function clearRoute(Command $command): void
    {
        $command->call('route:clear');
    }

    public function clearEvent(Command $command): void
    {
        $command->call('event:clear');
    }

    public function clearView(Command $command): void
    {
        $command->call('view:clear');
    }

    public function clearDebugbar(Command $command): void
    {
        $command->call('debugbar:clear');
    }

    public function clearQueue(Command $command): void
    {
        $command->call('queue:clear');
    }

    public function clearSchedule(Command $command): void
    {
        $command->call('schedule:clear-cache');
    }

    public function clearOptimize(Command $command): void
    {
        $command->call('optimize:clear');
    }

    public function runSetupInstall(Command $command): void
    {
        $command->call('setup', ['--install' => true]);
    }

    public function runSetupUpdate(Command $command): void
    {
        $command->call('setup', ['--update' => true]);
    }

    public function runSetupRefresh(Command $command): void
    {
        $command->call('setup', ['--refresh' => true]);
    }

    public function runSetupDb(Command $command): void
    {
        $command->call('setup', ['--db' => true]);
    }

    /**
     * Met à jour les paquets Composer du projet (respecte composer.json / lock).
     */
    public function runComposerProjectUpdate(Command $command): int
    {
        if (! is_file(base_path('composer.json'))) {
            return Command::SUCCESS;
        }

        $command->info('Mise à jour des dépendances Composer (composer update)…');

        return $this->runShellInProject($command, 'composer update');
    }

    /**
     * Met à jour les dépendances npm selon package.json (pnpm up).
     */
    public function runPnpmProjectUpdate(Command $command): int
    {
        if (! is_file(base_path('package.json'))) {
            return Command::SUCCESS;
        }

        $command->info('Mise à jour des paquets npm (pnpm up)…');

        if ($this->runShellInProject($command, 'pnpm up') !== Command::SUCCESS) {
            return Command::FAILURE;
        }

        return $this->ensureTailwindNativeBinding($command);
    }

    /**
     * Pipeline unique : optimize:clear → IDE Helper → dump-autoload → optimize.
     */
    public function runProjectOptimizePipeline(Command $command, string $mode = 'full'): int
    {
        if ($mode === 'clear-only') {
            $this->optimiseLaravel($command);

            return Command::SUCCESS;
        }

        if ($mode === 'ide-only') {
            $this->optimiseIde($command);
            $this->dumpAutoload($command);

            return Command::SUCCESS;
        }

        $this->optimiseLaravel($command);
        $this->optimiseIde($command);
        $this->dumpAutoload($command);

        return $command->call('optimize');
    }

    /**
     * Prépare l’environnement dev : CSS, caches de vues, doc, migrations.
     */
    public function runProjectPrepare(Command $command): int
    {
        $this->updateCss($command);
        $this->clearCache($command);
        $command->call('view:clear');
        $this->updateAtomicIndexes($command);
        $this->updateDocs($command);

        return $command->call('setup', ['--db' => true]);
    }

    public function updateCss(Command $command): void
    {
        $command->info('Rebuild CSS...');
        $result = shell_exec('pnpm run css 2>&1');
        if ($result !== null) {
            $command->info($result);
        }
    }

    public function updateAtomicIndexes(Command $command): void
    {
        $command->info('Régénération des index Atomic Design (atoms/molecules/organisms)...');
        exec('pnpm run update:atomic-index');
    }

    public function updateDocs(Command $command): void
    {
        $command->info('Génération de l’index et du schéma de la doc...');
        exec('pnpm run update:docs');
    }

    public function dumpAutoload(Command $command): void
    {
        $command->info('Composer dump-autoload…');
        exec('composer dump-autoload');
    }

    public function optimiseIde(Command $command): void
    {
        $command->info('Génération des fichiers IDE Helper…');
        $command->call('ide-helper:models');
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
        $artisan = base_path('artisan');
        $commands = ['ide-helper:generate', 'ide-helper:eloquent', 'ide-helper:meta'];
        foreach ($commands as $cmd) {
            $process = new Process([$php, $artisan, $cmd], base_path());
            $process->run();
            if (! $process->isSuccessful()) {
                $command->warn("{$cmd} a échoué (incompatibilité connue avec Socialite 5.x).");
            }
        }
    }

    public function optimiseLaravel(Command $command): void
    {
        $command->info('Nettoyage des optimisations Laravel (config/routes/views)...');
        $command->call('optimize:clear');
    }

    public function resetAll(Command $command): void
    {
        $command->info('Grand ménage (rapports review, caches PHPStan, fichiers *.log Laravel)…');
        $this->clearDevReports($command);
        $this->clearPhpstanStorageCache($command);
        $this->clearLaravelLogFiles($command);

        $command->info('Réinitialisation de tout (pnpm, composer, css, docs, dump)...');
        $this->killServers($command);
        $this->clearCss($command);
        $this->clearCache($command);
        $this->clearConfig($command);
        $this->clearRoute($command);
        $this->clearView($command);
        $this->clearDebugbar($command);
        $this->clearQueue($command);
        $this->clearSchedule($command);
        $this->clearEvent($command);
        $this->clearOptimize($command);
        $command->call('setup', ['--refresh' => true]);
        $this->updateCss($command);
        $this->updateDocs($command);
        $this->dumpAutoload($command);
        $this->optimiseIde($command);
        $this->optimiseLaravel($command);
        $command->info('✅ Réinitialisation complète terminée !');
    }

    public function resetFull(Command $command): void
    {
        $command->info('🔄 Reset complet (reset:all + base de données)...');

        if (! $command->confirm('⚠️  ATTENTION : Cette opération va supprimer toutes les données de la base de données. Continuer ?')) {
            $command->info('Reset complet annulé.');

            return;
        }

        $this->resetAll($command);

        $command->info('🔄 Reset de la base de données...');
        $command->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $command->info('✅ Reset complet terminé !');
        $command->warn('⚠️  Toutes les données ont été supprimées et la base a été réinitialisée.');
    }

    public function runDev(Command $command): void
    {
        $command->info('Lancement des serveurs de développement...');

        // Libère 8000 / 5173 si une instance précédente tourne encore (évite « Port already in use »).
        $this->killServers($command);

        $command->info('Démarrage du serveur Laravel sur le port 8000...');
        exec('php artisan serve --host=127.0.0.1 --port=8000 > /dev/null 2>&1 &');

        sleep(3);

        $laravelResponse = @file_get_contents('http://127.0.0.1:8000');
        if ($laravelResponse !== false) {
            $command->info('✅ Serveur Laravel démarré sur http://127.0.0.1:8000');
        } else {
            $command->warn('⚠️ Serveur Laravel en cours de démarrage...');
        }

        $command->info('Démarrage de Vite sur le port 5173...');
        $this->runViteDevWithSelfHeal($command);
    }

    public function runDevWatch(Command $command): void
    {
        $command->info('Lancement du serveur (watch)...');
        $this->runProcess($command, 'pnpm run dev:css:optimized:watch');
    }

    public function updatePrivileges(Command $command): void
    {
        $user = $command->option('update:privilege');

        if (empty($user)) {
            $command->error('Tu dois spécifier un utilisateur avec --update:privilege=nom_utilisateur');

            return;
        }

        $user = trim((string) $user);
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $user)) {
            $command->error('Nom d\'utilisateur invalide. Utilise uniquement des lettres, chiffres, tirets et underscores.');

            return;
        }

        $command->info("Vérification de l'existence de l'utilisateur : $user");
        $userExists = shell_exec("id $user 2>/dev/null");
        if (empty($userExists)) {
            $command->error("L'utilisateur '$user' n'existe pas sur ce système.");
            $command->line('Utilisateurs disponibles :');
            $command->line((string) shell_exec("cut -d: -f1 /etc/passwd | grep -E '^[a-zA-Z]' | head -10"));

            return;
        }

        if (app()->environment('production')) {
            $command->error('Cette commande ne doit pas être exécutée en production !');

            return;
        }

        if (! file_exists('artisan') || ! file_exists('composer.json')) {
            $command->error('Cette commande doit être exécutée depuis la racine du projet Laravel.');

            return;
        }

        $currentUser = trim((string) shell_exec('whoami'));
        if ($user !== $currentUser) {
            $command->warn("Tu es actuellement connecté en tant que '$currentUser'");
            $command->warn("Tu vas changer les permissions pour l'utilisateur '$user'");

            if ($command->option('no-interaction')) {
                $command->info('Mode non-interactif : continuation automatique...');
            } elseif (! $command->confirm('Es-tu sûr de vouloir continuer ?')) {
                $command->info('Opération annulée.');

                return;
            }
        }

        $command->info('Analyse des permissions actuelles...');
        $rootFiles = trim((string) shell_exec('find . -user root 2>/dev/null'));
        if ($rootFiles !== '') {
            $command->warn('Fichiers appartenant à root détectés :');
            $command->line($rootFiles);
        }

        $command->info("Correction des permissions pour l'utilisateur : $user");

        try {
            $command->info('Changement du propriétaire de tous les fichiers...');
            $result = shell_exec("chown -R $user:$user . 2>&1");
            if ($result !== null) {
                $command->warn("Avertissements lors du changement de propriétaire : $result");
            }

            $command->info('Correction des permissions des dossiers Laravel...');
            if (is_dir('storage/')) {
                shell_exec('chmod -R 775 storage/');
            }
            if (is_dir('bootstrap/cache/')) {
                shell_exec('chmod -R 775 bootstrap/cache/');
            }
            if (is_dir('public/')) {
                shell_exec('chmod -R 775 public/');
            }

            $command->info('Correction des permissions des fichiers exécutables...');
            if (file_exists('artisan')) {
                shell_exec('chmod 755 artisan');
            }
            shell_exec("find . -name '*.php' -executable -exec chmod 755 {} \\; 2>/dev/null");

            $composerPath = trim((string) shell_exec('which composer 2>/dev/null'));
            if ($composerPath !== '' && file_exists($composerPath)) {
                $command->info('Correction des permissions de Composer...');
                shell_exec("chown $user:$user $composerPath");
                shell_exec("chmod 755 $composerPath");
            }

            $pnpmPath = trim((string) shell_exec('which pnpm 2>/dev/null'));
            if ($pnpmPath !== '' && file_exists($pnpmPath)) {
                $command->info('Correction des permissions de pnpm...');
                shell_exec("chown $user:$user $pnpmPath");
                shell_exec("chmod 755 $pnpmPath");
            }

            $command->info('Vérification finale des permissions...');
            $finalRootFiles = trim((string) shell_exec('find . -user root 2>/dev/null'));
            if ($finalRootFiles !== '') {
                $command->warn('Fichiers appartenant encore à root détectés :');
                $command->line($finalRootFiles);
                $command->info('Correction automatique...');
                shell_exec("find . -user root -exec chown $user:$user {} \\; 2>/dev/null");
            } else {
                $command->info('✅ Aucun fichier n\'appartient à root');
            }

            $command->info('Test de validation des permissions...');
            $cwd = getcwd();
            $testResult = shell_exec("su - $user -c 'cd ".escapeshellarg((string) $cwd)." && php artisan --version' 2>&1");
            if (is_string($testResult) && str_contains($testResult, 'Laravel Framework')) {
                $command->info('✅ Test de validation réussi : Laravel fonctionne correctement');
            } else {
                $command->warn('⚠️ Test de validation échoué. Vérifiez manuellement les permissions.');
            }

            $command->info('✅ Permissions corrigées avec succès !');

        } catch (\Exception $e) {
            $command->error('Erreur lors de la correction des permissions : '.$e->getMessage());
            $command->error('Vérifiez manuellement les permissions du projet.');
        }
    }

    public function checkEffectsQuality(Command $command): int
    {
        $command->info('Vérification qualité des effets de sorts (strict)...');

        return $command->call('scrapping:effects:quality-gate');
    }

    public function checkEffectsQualityDev(Command $command): int
    {
        $command->info('Vérification qualité des effets de sorts (dev, allow-empty)...');

        return $command->call('scrapping:effects:quality-gate', ['--allow-empty' => true]);
    }

    public function pipelineEffectsQuality(Command $command): int
    {
        $command->info('Pipeline effets de sorts (strict)...');

        return $command->call('scrapping:effects:pipeline', $this->buildEffectsPipelineArgs($command, false));
    }

    public function pipelineEffectsQualityDev(Command $command): int
    {
        $command->info('Pipeline effets de sorts (dev, allow-empty)...');

        return $command->call('scrapping:effects:pipeline', $this->buildEffectsPipelineArgs($command, true));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEffectsPipelineArgs(Command $command, bool $allowEmpty): array
    {
        $args = [
            '--limit' => max(1, (int) $command->option('limit')),
            '--max-pages' => max(0, (int) $command->option('max-pages')),
            '--max-items' => max(0, (int) $command->option('max-items')),
            '--include-relations' => (int) $command->option('include-relations') === 0 ? 0 : 1,
        ];

        if ($allowEmpty) {
            $args['--allow-empty'] = true;
        }
        if ((bool) $command->option('simulate')) {
            $args['--simulate'] = true;
        }
        if ((bool) $command->option('skip-cache')) {
            $args['--skip-cache'] = true;
        }

        foreach (['ids', 'levelMin', 'levelMax'] as $opt) {
            $value = $command->option($opt);
            if (is_string($value) && trim($value) !== '') {
                $args['--'.$opt] = $value;
            }
        }

        return $args;
    }

    private function runProcess(Command $command, string $shellCommand): void
    {
        $returnVar = $this->runInteractiveProcess($shellCommand);
        if ($returnVar !== 0) {
            $command->error("Erreur lors de $shellCommand");
        } else {
            $command->info("$shellCommand terminé avec succès");
        }
    }

    /**
     * Exécute une commande en mode interactif (TTY), pour conserver les couleurs ANSI.
     */
    private function runInteractiveProcess(string $shellCommand): int
    {
        $descriptorspec = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
        $process = proc_open($shellCommand, $descriptorspec, $pipes);
        if (! is_resource($process)) {
            return Command::FAILURE;
        }

        $returnVar = proc_close($process);

        if (! is_int($returnVar)) {
            return Command::FAILURE;
        }

        return $returnVar;
    }

    /**
     * Vérifie rapidement si le binding natif Tailwind est chargeable dans l'environnement courant.
     */
    private function hasTailwindNativeBinding(Command $command): bool
    {
        $check = $this->runShellInProject($command, "node -e \"require('@tailwindcss/oxide');\"");

        return $check === Command::SUCCESS;
    }

    /**
     * Démarre Vite en TTY et tente une réparation automatique si le binding natif Tailwind manque.
     */
    private function runViteDevWithSelfHeal(Command $command): void
    {
        $exitCode = $this->runInteractiveProcess('pnpm run dev:optimized');
        if ($exitCode === Command::SUCCESS) {
            return;
        }

        if ($this->hasTailwindNativeBinding($command)) {
            $command->error('Erreur lors de pnpm run dev:optimized');

            return;
        }

        $command->warn('⚠️ Dépendance native Tailwind manquante détectée. Tentative de réparation automatique...');
        $repair = $this->runShellInProject($command, 'pnpm install --force');
        if ($repair !== Command::SUCCESS) {
            $command->error('Échec de la réparation automatique des dépendances pnpm.');

            return;
        }

        $command->info('Relance de Vite après réparation des dépendances...');
        $retry = $this->runInteractiveProcess('pnpm run dev:optimized');
        if ($retry !== Command::SUCCESS) {
            $command->error('Erreur lors de pnpm run dev:optimized (après réparation automatique).');
        }
    }

    /**
     * Exécute une commande shell en streamant la sortie et la conserve pour diagnostic.
     *
     * @return array{exitCode:int, output:string}
     */
    private function runShellWithStreaming(Command $command, string $commandLine): array
    {
        $output = '';

        try {
            $process = Process::fromShellCommandline($commandLine, base_path());
            $process->setTimeout(null);
            $process->run(function (string $type, string $buffer) use ($command, &$output): void {
                $output .= $buffer;
                $command->getOutput()->write($buffer);
            });

            return [
                'exitCode' => $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE,
                'output' => $output,
            ];
        } catch (Throwable $e) {
            $command->error($e->getMessage());

            return [
                'exitCode' => Command::FAILURE,
                'output' => $output.$e->getMessage(),
            ];
        }
    }

    private function ensureTailwindNativeBinding(Command $command): int
    {
        $check = $this->runShellWithStreaming($command, "node -e \"require('@tailwindcss/oxide');\"");
        if ($check['exitCode'] === Command::SUCCESS) {
            return Command::SUCCESS;
        }

        $command->warn('Binding natif Tailwind indisponible après mise à jour pnpm. Réparation automatique...');
        $repair = $this->runShellInProject($command, 'pnpm install --force');
        if ($repair !== Command::SUCCESS) {
            $command->error('Échec de la réparation automatique des dépendances pnpm.');

            return Command::FAILURE;
        }

        $retry = $this->runShellWithStreaming($command, "node -e \"require('@tailwindcss/oxide');\"");

        return $retry['exitCode'];
    }

    /**
     * Exécute une commande shell dans la racine du projet et renvoie le code de sortie.
     */
    private function runShellInProject(Command $command, string $commandLine): int
    {
        try {
            $process = Process::fromShellCommandline($commandLine, base_path());
            $process->setTimeout(null);
            $process->run(function (string $type, string $buffer) use ($command): void {
                $command->getOutput()->write($buffer);
            });

            return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
        } catch (Throwable $e) {
            $command->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}

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

        if ($this->opt($o, 'kill') || $this->opt($o, 'all')) {
            $actions[] = 'killServers';
        }

        if ($this->opt($o, 'clear:test')) {
            $actions[] = 'clearTestArtifacts';
        }

        if ($this->opt($o, 'clear:all') || $this->opt($o, 'all')) {
            $actions = array_merge($actions, [
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
            ]);
        } else {
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

        if ($this->opt($o, 'optimise:all') || $this->opt($o, 'all')) {
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

        if ($this->opt($o, 'migrate') || $this->opt($o, 'update:base') || $this->opt($o, 'all')) {
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

        if ($this->opt($o, 'dev') || $this->opt($o, 'all')) {
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
        $descriptorspec = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
        $process = proc_open($shellCommand, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $returnVar = proc_close($process);
            if ($returnVar !== 0) {
                $command->error("Erreur lors de $shellCommand");
            } else {
                $command->info("$shellCommand terminé avec succès");
            }
        } else {
            $command->error("Impossible de lancer $shellCommand");
        }
    }

    /**
     * Démarre Vite et tente une réparation automatique en cas de binding natif manquant.
     */
    private function runViteDevWithSelfHeal(Command $command): void
    {
        $result = $this->runShellWithStreaming($command, 'pnpm run dev:optimized');
        if ($result['exitCode'] === Command::SUCCESS) {
            return;
        }

        if (! $this->isTailwindNativeBindingError($result['output'])) {
            $command->error('Erreur lors de pnpm run dev:optimized');

            return;
        }

        $command->warn('⚠️ Dépendance native Tailwind manquante détectée. Tentative de réparation automatique...');
        $repair = $this->runShellInProject($command, 'CI=true pnpm install');
        if ($repair !== Command::SUCCESS) {
            $command->error('Échec de la réparation automatique des dépendances pnpm.');

            return;
        }

        $command->info('Relance de Vite après réparation des dépendances...');
        $retry = $this->runShellWithStreaming($command, 'pnpm run dev:optimized');
        if ($retry['exitCode'] !== Command::SUCCESS) {
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

    private function isTailwindNativeBindingError(string $output): bool
    {
        return str_contains($output, 'Cannot find native binding')
            && str_contains($output, '@tailwindcss/oxide');
    }

    private function ensureTailwindNativeBinding(Command $command): int
    {
        $check = $this->runShellWithStreaming($command, "node -e \"require('@tailwindcss/oxide');\"");
        if ($check['exitCode'] === Command::SUCCESS) {
            return Command::SUCCESS;
        }

        $command->warn('Binding natif Tailwind indisponible après mise à jour pnpm. Réparation automatique...');
        $repair = $this->runShellInProject($command, 'CI=true pnpm install');
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

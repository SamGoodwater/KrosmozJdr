<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use PDOException;

/**
 * Nettoyage caches / artefacts pour `project:clear` et `project:refresh`.
 */
class ProjectClearService
{
    /**
     * Preset cron / production : caches Laravel + rapports review + cache PHPStan.
     */
    public function clearSafe(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $this->clearCache($command);
        $this->clearConfig($command);
        $this->clearRoute($command);
        $this->clearView($command);
        $this->clearSchedule($command);
        $this->clearEvent($command);
        $this->clearOptimize($command);
        $this->clearDevReports($command);
        $this->clearPhpstanStorageCache($command);

        return Command::SUCCESS;
    }

    /**
     * Nettoyage large local (CSS généré, queue, debugbar) + review + PHPStan.
     */
    public function clearLocalAll(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $this->clearDeveloperStack($command);
        $this->clearDevReports($command);
        $this->clearPhpstanStorageCache($command);

        return Command::SUCCESS;
    }

    /**
     * Grand ménage avant `project:refresh` (stack dev + review + PHPStan + logs).
     */
    public function clearDeep(Command $command): int
    {
        if (RefusesRootExecution::abort($command)) {
            return Command::FAILURE;
        }

        $this->clearDeveloperStack($command);
        $this->clearDevReports($command);
        $this->clearPhpstanStorageCache($command);
        $this->clearLaravelLogFiles($command);

        return Command::SUCCESS;
    }

    public function clearDeveloperStack(Command $command): void
    {
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
    }

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

    public function clearDevReports(Command $command): void
    {
        $command->info('Suppression des rapports `project:review` (`storage/app/dev-reports`)…');
        $dir = storage_path('app/dev-reports');
        File::ensureDirectoryExists($dir);
        $this->purgeDirectoryLeavingGitignore($dir, $command);
        $command->info('Rapports dev-reports nettoyés.');
    }

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
        $command->info('Répertoire des sauvegardes nettoyé.');
    }

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

        $command->info('Logs Laravel nettoyés.');
    }

    public function clearPhpstanStorageCache(Command $command): void
    {
        $dir = storage_path('phpstan');
        $command->info('Suppression du cache localement stocké sous `storage/phpstan`…');

        if (! File::isDirectory($dir)) {
            return;
        }

        File::deleteDirectory($dir);
        $command->info('Répertoire `storage/phpstan` supprimé.');
    }

    public function killServers(Command $command): void
    {
        $command->info('Arrêt des serveurs sur les ports 8000, 8001, 8002, 5173...');
        exec('lsof -t -i:8000 -i:8001 -i:8002 -i:5173 | xargs -r kill -9');
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
}

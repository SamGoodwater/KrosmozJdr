<?php

declare(strict_types=1);

namespace App\Services\Project;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

/**
 * Restauration complète depuis une archive ZIP v2 (intégrité, secours, maintenance).
 *
 * @example
 * app(ProjectBackupRestoreService::class)->restore('project-backup_….zip', fn ($m) => null, fn ($m) => null);
 */
class ProjectBackupRestoreService
{
    public function __construct(
        private readonly ProjectBackupService $backups,
        private readonly string $mysqlBinary = 'mysql',
        private readonly int $restoreTimeout = 7200,
    ) {}

    public static function fromConfig(?ProjectBackupService $backups = null): self
    {
        $backups ??= ProjectBackupService::fromConfig();

        return new self(
            backups: $backups,
            mysqlBinary: (string) config('project-backup.mysql_path', '') ?: 'mysql',
            restoreTimeout: max(60, (int) config('project-backup.restore_timeout', 7200)),
        );
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     * @return array{ok: bool, safety_archive: string|null, message: string}
     */
    public function restore(
        string $archiveName,
        callable $log,
        callable $error,
        bool $createSafetyBackup = true,
        ?ProjectBackupOperationStatus $status = null,
    ): array {
        $status?->write([
            'state' => 'running',
            'archive' => $archiveName,
            'started_at' => now()->toIso8601String(),
            'message' => 'Vérification de l’archive…',
        ]);

        $extractDir = null;
        $maintenanceEnabled = false;
        $safetyArchive = null;

        try {
            $verified = $this->backups->openAndVerifyZip($archiveName, true);
            $manifest = $verified['manifest'];
            $archivePath = $verified['path'];
            $log('Archive vérifiée : '.$archiveName);
            $status?->appendLog('Archive vérifiée.');

            if ($createSafetyBackup) {
                $status?->appendLog('Création de la sauvegarde de secours…');
                $log('Création d’une sauvegarde de secours avant restauration…');
                $safety = $this->backups->run(
                    withDatabase: true,
                    withStorage: true,
                    prune: false,
                    dryRun: false,
                    log: $log,
                    error: $error,
                    withSeederData: false,
                    withGame: true,
                );
                if (! ($safety['ok'] ?? false) || ($safety['files'] ?? []) === []) {
                    $error('Impossible de créer la sauvegarde de secours — restauration annulée.');

                    return [
                        'ok' => false,
                        'safety_archive' => null,
                        'message' => 'Sauvegarde de secours impossible.',
                    ];
                }
                $safetyArchive = basename((string) $safety['files'][0]);
                $log('Secours : '.$safetyArchive);
                $status?->appendLog('Secours créé : '.$safetyArchive);
            }

            $extractDir = $this->backups->resolvedBackupDirectory()
                .DIRECTORY_SEPARATOR.'.restore_'.preg_replace('/[^a-zA-Z0-9_\-]/', '_', $archiveName);
            $this->removeDirectory($extractDir);
            if (! mkdir($extractDir, 0750, true) && ! is_dir($extractDir)) {
                throw new \RuntimeException('Impossible de créer le répertoire d’extraction.');
            }

            $status?->appendLog('Extraction sécurisée…');
            $this->extractZipSafely($archivePath, $extractDir, $log);

            $code = Artisan::call('down', [
                '--retry' => 60,
                '--secret' => bin2hex(random_bytes(8)),
            ]);
            if ($code !== 0) {
                throw new \RuntimeException('Activation du mode maintenance impossible.');
            }
            $maintenanceEnabled = true;
            $log('Mode maintenance activé.');
            $status?->appendLog('Mode maintenance activé.');

            $components = $manifest['components'] ?? [];
            if (isset($components['database'])) {
                $status?->appendLog('Restauration BDD…');
                $this->restoreDatabase($extractDir, $components['database'], $log, $error);
            }

            if (isset($components['storage'])) {
                $status?->appendLog('Restauration storage/app…');
                $this->restoreStorageApp($extractDir, $log);
            }

            if (isset($components['game'])) {
                $status?->appendLog('Restauration private/game…');
                $this->restoreGame($extractDir, $log);
            }

            $log('Restauration terminée.');
            $status?->write([
                'state' => 'success',
                'archive' => $archiveName,
                'safety_archive' => $safetyArchive,
                'finished_at' => now()->toIso8601String(),
                'message' => 'Restauration terminée.',
            ]);

            return [
                'ok' => true,
                'safety_archive' => $safetyArchive,
                'message' => 'Restauration terminée.'.($safetyArchive ? ' Secours : '.$safetyArchive : ''),
            ];
        } catch (\Throwable $e) {
            $error($e->getMessage());
            $status?->write([
                'state' => 'failed',
                'archive' => $archiveName,
                'safety_archive' => $safetyArchive,
                'finished_at' => now()->toIso8601String(),
                'message' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'safety_archive' => $safetyArchive,
                'message' => $e->getMessage(),
            ];
        } finally {
            if ($maintenanceEnabled) {
                try {
                    Artisan::call('up');
                    $log('Mode maintenance désactivé.');
                    $status?->appendLog('Mode maintenance désactivé.');
                } catch (\Throwable $e) {
                    $error('Impossible de sortir du mode maintenance : '.$e->getMessage());
                }
            }
            if ($extractDir !== null) {
                $this->removeDirectory($extractDir);
            }
        }
    }

    /**
     * @param  callable(string): void  $log
     */
    private function extractZipSafely(string $archivePath, string $extractDir, callable $log): void
    {
        $zip = new \ZipArchive;
        if ($zip->open($archivePath) !== true) {
            throw new \RuntimeException('Ouverture ZIP impossible pour extraction.');
        }

        $extractReal = realpath($extractDir);
        if ($extractReal === false) {
            $zip->close();
            throw new \RuntimeException('Répertoire d’extraction invalide.');
        }

        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = (string) $zip->getNameIndex($i);
                $normalized = str_replace('\\', '/', $name);
                if ($normalized === '' || str_ends_with($normalized, '/')) {
                    continue;
                }
                if (str_contains($normalized, '..') || str_starts_with($normalized, '/')
                    || str_contains($normalized, "\0")) {
                    throw new \RuntimeException('Entrée ZIP dangereuse : '.$name);
                }
                if ($normalized !== ProjectBackupService::MANIFEST_ENTRY
                    && $normalized !== ProjectBackupService::DATABASE_MYSQL_ENTRY
                    && $normalized !== ProjectBackupService::DATABASE_SQLITE_ENTRY
                    && ! str_starts_with($normalized, ProjectBackupService::STORAGE_PREFIX)
                    && ! str_starts_with($normalized, ProjectBackupService::GAME_PREFIX)) {
                    throw new \RuntimeException('Entrée ZIP hors liste blanche : '.$name);
                }

                $target = $extractReal.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $normalized);
                $targetDir = dirname($target);
                if (! is_dir($targetDir) && ! mkdir($targetDir, 0750, true) && ! is_dir($targetDir)) {
                    throw new \RuntimeException('Création dossier extraction impossible.');
                }

                $targetRealDir = realpath($targetDir);
                if ($targetRealDir === false
                    || ($targetRealDir !== $extractReal
                        && ! str_starts_with($targetRealDir, $extractReal.DIRECTORY_SEPARATOR))) {
                    throw new \RuntimeException('Path traversal détecté lors de l’extraction.');
                }

                $stream = $zip->getStream($name);
                if ($stream === false) {
                    throw new \RuntimeException('Lecture entrée ZIP impossible : '.$name);
                }
                $out = fopen($target, 'wb');
                if ($out === false) {
                    fclose($stream);
                    throw new \RuntimeException('Écriture extraction impossible : '.$name);
                }
                stream_copy_to_stream($stream, $out);
                fclose($out);
                fclose($stream);
            }
        } finally {
            $zip->close();
        }

        $log('Extraction terminée dans '.$extractDir);
    }

    /**
     * @param  array<string, mixed>  $databaseComponent
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function restoreDatabase(string $extractDir, array $databaseComponent, callable $log, callable $error): void
    {
        $entry = (string) ($databaseComponent['entry'] ?? ProjectBackupService::DATABASE_MYSQL_ENTRY);
        $gzPath = $extractDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $entry);
        if (! is_file($gzPath)) {
            throw new \RuntimeException('Dump BDD absent de l’extraction : '.$entry);
        }

        $connection = (string) config('database.default');
        /** @var array<string, mixed> $config */
        $config = config("database.connections.{$connection}", []);
        $driver = (string) ($config['driver'] ?? 'mysql');

        if ($driver === 'sqlite' || $entry === ProjectBackupService::DATABASE_SQLITE_ENTRY) {
            $this->restoreSqlite($config, $gzPath, $log);

            return;
        }

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            throw new \RuntimeException('Driver BDD non supporté pour la restauration : '.$driver);
        }

        $this->restoreMysql($config, $gzPath, $log, $error);
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function restoreMysql(array $config, string $gzPath, callable $log, callable $error): void
    {
        $database = (string) ($config['database'] ?? '');
        if ($database === '') {
            throw new \RuntimeException('Nom de base de données vide.');
        }

        $defaultsFile = $this->writeMysqlDefaultsFile($config);
        if ($defaultsFile === null) {
            throw new \RuntimeException('Credentials MySQL temporaires impossibles.');
        }

        $tmpSql = tempnam(sys_get_temp_dir(), 'kzrestore');
        if ($tmpSql === false) {
            @unlink($defaultsFile);
            throw new \RuntimeException('Fichier temporaire SQL impossible.');
        }
        @unlink($tmpSql);
        $tmpSql .= '.sql';

        $gz = gzopen($gzPath, 'rb');
        if ($gz === false) {
            @unlink($defaultsFile);
            throw new \RuntimeException('Lecture gzip du dump impossible.');
        }
        $out = fopen($tmpSql, 'wb');
        if ($out === false) {
            gzclose($gz);
            @unlink($defaultsFile);
            throw new \RuntimeException('Écriture SQL temporaire impossible.');
        }

        try {
            while (! gzeof($gz)) {
                $chunk = gzread($gz, 1024 * 1024);
                if ($chunk === false || $chunk === '') {
                    break;
                }
                fwrite($out, $chunk);
            }
            fclose($out);
            $out = null;
            gzclose($gz);
            $gz = null;

            if (! is_file($tmpSql) || (filesize($tmpSql) ?: 0) === 0) {
                throw new \RuntimeException('Dump SQL vide.');
            }

            $log('Import MySQL…');
            $input = fopen($tmpSql, 'rb');
            if ($input === false) {
                throw new \RuntimeException('Relecture SQL temporaire impossible.');
            }

            $process = new Process([
                $this->mysqlBinary,
                '--defaults-extra-file='.$defaultsFile,
                '--default-character-set=utf8mb4',
                $database,
            ]);
            $process->setTimeout($this->restoreTimeout);
            $process->setInput($input);
            $process->run();
            fclose($input);

            if (! $process->isSuccessful()) {
                $error($process->getErrorOutput().$process->getOutput());
                throw new \RuntimeException('Import MySQL échoué.');
            }

            $log('Base MySQL restaurée.');
        } finally {
            if (is_resource($gz)) {
                gzclose($gz);
            }
            if (is_resource($out)) {
                fclose($out);
            }
            if (is_file($tmpSql)) {
                @unlink($tmpSql);
            }
            if (is_file($defaultsFile)) {
                @unlink($defaultsFile);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string): void  $log
     */
    private function restoreSqlite(array $config, string $gzPath, callable $log): void
    {
        $path = (string) ($config['database'] ?? '');
        if ($path === '') {
            throw new \RuntimeException('Chemin SQLite vide.');
        }
        if (! str_starts_with($path, '/') && ! preg_match('/^[A-Za-z]:\\\\/', $path)) {
            $path = base_path($path);
        }

        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Répertoire SQLite introuvable.');
        }

        $tmp = $path.'.restore-tmp';
        $gz = gzopen($gzPath, 'rb');
        if ($gz === false) {
            throw new \RuntimeException('Lecture gzip SQLite impossible.');
        }
        $out = fopen($tmp, 'wb');
        if ($out === false) {
            gzclose($gz);
            throw new \RuntimeException('Écriture SQLite temporaire impossible.');
        }

        while (! gzeof($gz)) {
            $chunk = gzread($gz, 1024 * 1024);
            if ($chunk === false || $chunk === '') {
                break;
            }
            fwrite($out, $chunk);
        }
        fclose($out);
        gzclose($gz);

        if (! is_file($tmp) || (filesize($tmp) ?: 0) === 0) {
            @unlink($tmp);
            throw new \RuntimeException('Fichier SQLite restauré vide.');
        }

        // Remplacement atomique si possible
        if (is_file($path)) {
            @unlink($path);
        }
        if (! @rename($tmp, $path)) {
            @unlink($tmp);
            throw new \RuntimeException('Remplacement du fichier SQLite impossible.');
        }

        $log('Base SQLite restaurée : '.$path);
    }

    /**
     * @param  callable(string): void  $log
     */
    private function restoreStorageApp(string $extractDir, callable $log): void
    {
        $source = $extractDir.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app';
        if (! is_dir($source)) {
            throw new \RuntimeException('Dossier storage/app absent de l’archive.');
        }

        $target = storage_path('app');
        $backupRoot = realpath($this->backups->resolvedBackupDirectory());
        $this->replaceDirectoryPreserving($source, $target, $backupRoot, $log);
        $log('storage/app restauré (répertoire backups préservé).');
    }

    /**
     * @param  callable(string): void  $log
     */
    private function restoreGame(string $extractDir, callable $log): void
    {
        $source = $extractDir.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'game';
        if (! is_dir($source)) {
            throw new \RuntimeException('Dossier private/game absent de l’archive.');
        }

        $target = base_path('private/game');
        $this->replaceDirectoryPreserving($source, $target, null, $log);
        $log('private/game restauré.');
    }

    /**
     * Remplace le contenu d’un dossier cible par la source, en préservant éventuellement un sous-arbre.
     *
     * @param  callable(string): void  $log
     */
    private function replaceDirectoryPreserving(
        string $source,
        string $target,
        ?string $preserveRealPath,
        callable $log,
    ): void {
        if (! is_dir($target) && ! mkdir($target, 0750, true) && ! is_dir($target)) {
            throw new \RuntimeException('Cible de restauration introuvable : '.$target);
        }

        $targetReal = realpath($target);
        $sourceReal = realpath($source);
        if ($targetReal === false || $sourceReal === false) {
            throw new \RuntimeException('Chemins de restauration invalides.');
        }

        // Supprime le contenu cible sauf préservation
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($targetReal, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            /** @var \SplFileInfo $item */
            $real = $item->getRealPath();
            if ($real === false) {
                continue;
            }
            if ($preserveRealPath !== false && $preserveRealPath !== null
                && ($real === $preserveRealPath || str_starts_with($real, $preserveRealPath.DIRECTORY_SEPARATOR))) {
                continue;
            }
            if ($item->isDir()) {
                @rmdir($real);
            } else {
                @unlink($real);
            }
        }

        // Copie source → cible
        $copy = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceReal, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($copy as $item) {
            /** @var \SplFileInfo $item */
            $real = $item->getRealPath();
            if ($real === false) {
                continue;
            }
            $relative = ltrim(substr($real, strlen($sourceReal)), DIRECTORY_SEPARATOR);
            $dest = $targetReal.($relative !== '' ? DIRECTORY_SEPARATOR.$relative : '');

            if ($preserveRealPath !== false && $preserveRealPath !== null
                && ($dest === $preserveRealPath || str_starts_with($dest, $preserveRealPath.DIRECTORY_SEPARATOR))) {
                continue;
            }

            if ($item->isDir()) {
                if (! is_dir($dest) && ! mkdir($dest, 0750, true) && ! is_dir($dest)) {
                    throw new \RuntimeException('Création dossier restauration impossible : '.$dest);
                }

                continue;
            }

            if ($item->isLink() || ! $item->isFile()) {
                continue;
            }

            $dir = dirname($dest);
            if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
                throw new \RuntimeException('Création dossier parent impossible : '.$dir);
            }
            if (! @copy($real, $dest)) {
                throw new \RuntimeException('Copie restauration impossible : '.$relative);
            }
        }

        $log('Remplacement : '.$targetReal);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function writeMysqlDefaultsFile(array $config): ?string
    {
        $user = (string) ($config['username'] ?? 'root');
        $password = (string) ($config['password'] ?? '');
        $socket = trim((string) ($config['unix_socket'] ?? ''));

        $escape = static fn (string $value): string => str_replace(
            ['\\', "\n", "\r", "'"],
            ['\\\\', '\\n', '\\r', "\\'"],
            $value
        );

        $lines = ['[client]', 'user='.$escape($user)];
        if ($password !== '') {
            $lines[] = 'password='.$escape($password);
        }
        if ($socket !== '') {
            $lines[] = 'socket='.$escape($socket);
        } else {
            $lines[] = 'host='.$escape((string) ($config['host'] ?? '127.0.0.1'));
            $lines[] = 'port='.$escape((string) ($config['port'] ?? '3306'));
        }

        $tmp = tempnam(sys_get_temp_dir(), 'kzmariadb');
        if ($tmp === false) {
            return null;
        }
        unlink($tmp);
        $path = $tmp.'.cnf';
        if (file_put_contents($path, implode("\n", $lines)."\n") === false) {
            return null;
        }
        chmod($path, 0600);

        return $path;
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            /** @var \SplFileInfo $item */
            $path = $item->getPathname();
            if ($item->isDir()) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}

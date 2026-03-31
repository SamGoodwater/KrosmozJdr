<?php

declare(strict_types=1);

namespace App\Services\Project;

use Symfony\Component\Process\Process;

/**
 * Sauvegarde BDD + dossier storage/app (hors backups), compression, rotation sur N jours.
 */
class ProjectBackupService
{
    public function __construct(
        private readonly string $backupRoot,
        private readonly int $retentionDays,
        private readonly string $mysqldumpBinary,
        private readonly string $filenamePrefix,
    ) {}

    public static function fromConfig(): self
    {
        $configured = (string) config('project-backup.path', '');
        $root = $configured !== '' ? $configured : storage_path('app/backups');

        return new self(
            backupRoot: $root,
            retentionDays: max(1, (int) config('project-backup.retention_days', 30)),
            mysqldumpBinary: (string) config('project-backup.mysqldump_path', '') ?: 'mysqldump',
            filenamePrefix: (string) config('project-backup.filename_prefix', '') ?: 'project-backup',
        );
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     * @return array{run_id: string, files: list<string>}
     */
    public function run(
        bool $withDatabase,
        bool $withStorage,
        bool $prune,
        bool $dryRun,
        callable $log,
        callable $error,
    ): array {
        if (! $withDatabase && ! $withStorage) {
            $error('Rien à sauvegarder : utilisez la BDD et/ou le storage (par défaut les deux).');

            return ['run_id' => '', 'files' => []];
        }

        $this->ensureDirectory($this->backupRoot);

        $runId = $this->makeRunId();
        $prefix = $this->filenamePrefix.'_'.$runId;
        $created = [];

        if ($withDatabase) {
            $sqlGz = $this->backupRoot.DIRECTORY_SEPARATOR.$prefix.'_mysql.sql.gz';
            $code = $this->dumpDatabase($sqlGz, $log, $error);
            if ($code !== 0) {
                return ['run_id' => $runId, 'files' => $created];
            }
            $created[] = $sqlGz;
            $log('Base : '.$sqlGz);
        }

        if ($withStorage) {
            $storagePath = $this->backupRoot.DIRECTORY_SEPARATOR.$prefix.'_storage.tar.gz';
            $actual = $this->archiveStorageApp($storagePath, $log, $error);
            if ($actual === null) {
                return ['run_id' => $runId, 'files' => $created];
            }
            $created[] = $actual;
            $log('Storage : '.$actual);
        }

        if ($prune) {
            $this->pruneOldBackups($dryRun, $log, $error);
        }

        return ['run_id' => $runId, 'files' => $created];
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    public function pruneOldBackups(bool $dryRun, callable $log, callable $error): int
    {
        if (! is_dir($this->backupRoot)) {
            return 0;
        }

        $cutoff = time() - ($this->retentionDays * 86400);
        $quotedPrefix = preg_quote($this->filenamePrefix, '/');
        $namePattern = '/^'.$quotedPrefix.'_.+_(mysql\.sql\.gz|storage\.tar\.gz|storage\.zip)$/';
        $removed = 0;

        foreach (scandir($this->backupRoot) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $full = $this->backupRoot.DIRECTORY_SEPARATOR.$name;
            if (! is_file($full)) {
                continue;
            }
            if (! preg_match($namePattern, $name)) {
                continue;
            }

            $mtime = @filemtime($full) ?: 0;
            if ($mtime >= $cutoff) {
                continue;
            }

            if ($dryRun) {
                $log('[dry-run] Supprimerait : '.$full.' (âge > '.$this->retentionDays.' j)');
                $removed++;

                continue;
            }

            if (@unlink($full)) {
                $log('Supprimé (expiration '.$this->retentionDays.' j) : '.$full);
                $removed++;
            } else {
                $error('Impossible de supprimer : '.$full);
            }
        }

        return $removed;
    }

    private function makeRunId(): string
    {
        return now()->format('Ymd_His').'_'.substr(str_replace('.', '', (string) microtime(true)), -4);
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path) && ! mkdir($path, 0755, true) && ! is_dir($path)) {
            throw new \RuntimeException('Impossible de créer le répertoire de sauvegarde : '.$path);
        }
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function dumpDatabase(string $outputSqlGz, callable $log, callable $error): int
    {
        $connection = (string) config('database.default');
        /** @var array<string, mixed> $dbConfig */
        $dbConfig = config("database.connections.{$connection}", []);
        $driver = (string) ($dbConfig['driver'] ?? 'mysql');

        if ($driver === 'sqlite') {
            return $this->dumpSqlite($dbConfig, $outputSqlGz, $log, $error);
        }

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            $error("Driver BDD non pris en charge pour le dump : {$driver} (supportés : mysql, mariadb, sqlite).");

            return 1;
        }

        return $this->dumpMysql($dbConfig, $outputSqlGz, $log, $error);
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function dumpMysql(array $config, string $outputSqlGz, callable $log, callable $error): int
    {
        $database = (string) ($config['database'] ?? '');
        if ($database === '') {
            $error('Nom de base de données vide.');

            return 1;
        }

        $defaultsFile = $this->writeMysqlDefaultsFile($config);
        if ($defaultsFile === null) {
            $error('Impossible d’écrire le fichier de credentials temporaire pour mysqldump.');

            return 1;
        }

        try {
            $log('Exécution mysqldump…');

            if (PHP_OS_FAMILY !== 'Windows') {
                $cmd = sprintf(
                    '%s --defaults-extra-file=%s --single-transaction --quick --routines --no-tablespaces --default-character-set=utf8mb4 %s | gzip -9 > %s',
                    escapeshellcmd($this->mysqldumpBinary),
                    escapeshellarg($defaultsFile),
                    escapeshellarg($database),
                    escapeshellarg($outputSqlGz)
                );
                $dump = Process::fromShellCommandline($cmd);
            } else {
                $args = [
                    $this->mysqldumpBinary,
                    '--defaults-extra-file='.$defaultsFile,
                    '--single-transaction',
                    '--quick',
                    '--routines',
                    '--no-tablespaces',
                    '--default-character-set=utf8mb4',
                    $database,
                ];
                $dump = new Process($args);
            }

            $dump->setTimeout(3600);
            $dump->run();

            if (! $dump->isSuccessful()) {
                $error('mysqldump a échoué : '.$dump->getErrorOutput().$dump->getOutput());

                return 1;
            }

            if (PHP_OS_FAMILY === 'Windows') {
                $sql = $dump->getOutput();
                $gz = gzencode($sql, 9);
                if ($gz === false) {
                    $error('Compression gzip du dump SQL impossible.');

                    return 1;
                }
                if (file_put_contents($outputSqlGz, $gz) === false) {
                    $error('Écriture impossible : '.$outputSqlGz);

                    return 1;
                }
            }

            return 0;
        } finally {
            if (is_file($defaultsFile)) {
                @unlink($defaultsFile);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function dumpSqlite(array $config, string $outputSqlGz, callable $log, callable $error): int
    {
        $path = (string) ($config['database'] ?? '');
        if ($path === '') {
            $error('Chemin SQLite vide.');

            return 1;
        }
        if (! str_starts_with($path, '/') && ! preg_match('/^[A-Za-z]:\\\\/', $path)) {
            $path = base_path($path);
        }
        if (! is_file($path)) {
            $error('Fichier SQLite introuvable : '.$path);

            return 1;
        }

        $log('Copie / compression SQLite…');
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            $error('Lecture SQLite impossible : '.$path);

            return 1;
        }

        $gzHandle = gzopen($outputSqlGz, 'wb9');
        if ($gzHandle === false) {
            fclose($handle);
            $error('Écriture gzip impossible : '.$outputSqlGz);

            return 1;
        }

        while (! feof($handle)) {
            $chunk = fread($handle, 1024 * 1024);
            if ($chunk === false) {
                break;
            }
            gzwrite($gzHandle, $chunk);
        }
        fclose($handle);
        gzclose($gzHandle);

        return 0;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function writeMysqlDefaultsFile(array $config): ?string
    {
        $user = (string) ($config['username'] ?? 'root');
        $password = (string) ($config['password'] ?? '');
        $socket = trim((string) ($config['unix_socket'] ?? ''));

        $lines = ['[client]', 'user='.$this->escapeMyCnfValue($user)];

        if ($password !== '') {
            $lines[] = 'password='.$this->escapeMyCnfValue($password);
        }

        if ($socket !== '') {
            $lines[] = 'socket='.$this->escapeMyCnfValue($socket);
        } else {
            $host = (string) ($config['host'] ?? '127.0.0.1');
            $port = (string) ($config['port'] ?? '3306');
            $lines[] = 'host='.$this->escapeMyCnfValue($host);
            $lines[] = 'port='.$this->escapeMyCnfValue($port);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'kzmysqldump');
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

    private function escapeMyCnfValue(string $value): string
    {
        return str_replace(['\\', "\n", "\r", "'"], ['\\\\', '\\n', '\\r', "\\'"], $value);
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     * @return non-empty-string|null Chemin réel du fichier créé (.tar.gz ou .zip)
     */
    private function archiveStorageApp(string $outputTarGz, callable $log, callable $error): ?string
    {
        $storage = realpath(storage_path());
        if ($storage === false) {
            $error('storage_path() introuvable.');

            return null;
        }

        $log('Compression storage/app (excl. app/backups)…');

        if (PHP_OS_FAMILY === 'Windows') {
            return $this->archiveStorageZip($outputTarGz, $error);
        }

        $process = Process::fromShellCommandline(
            'tar -czf '.escapeshellarg($outputTarGz).' -C '.escapeshellarg($storage).' --exclude='.escapeshellarg('app/backups').' app'
        );
        $process->setTimeout(7200);
        $process->run();

        if (! $process->isSuccessful()) {
            $error('tar a échoué : '.$process->getErrorOutput().$process->getOutput());
            $error('Tentative avec archive ZIP (plus lente)…');

            return $this->archiveStorageZip($outputTarGz, $error);
        }

        return $outputTarGz;
    }

    /**
     * @param  callable(string): void  $error
     * @return non-empty-string|null
     */
    private function archiveStorageZip(string $outputTarGz, callable $error): ?string
    {
        $zipPath = preg_replace('/\.tar\.gz$/i', '.zip', $outputTarGz);
        if ($zipPath === null || $zipPath === $outputTarGz) {
            $zipPath = $outputTarGz.'.zip';
        }

        $base = storage_path('app');
        $baseReal = realpath($base);
        if ($baseReal === false) {
            $error('storage/app introuvable.');

            return null;
        }

        $backupsReal = realpath($this->backupRoot);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $error('Impossible de créer le ZIP : '.$zipPath);

            return null;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (! $file->isFile()) {
                continue;
            }

            $real = realpath($file->getPathname());
            if ($real === false) {
                continue;
            }

            if ($backupsReal !== false && str_starts_with($real, $backupsReal.DIRECTORY_SEPARATOR)) {
                continue;
            }

            if (! str_starts_with($real, $baseReal.DIRECTORY_SEPARATOR) && $real !== $baseReal) {
                continue;
            }

            $relativeInsideApp = substr($real, strlen($baseReal));
            $relativeInsideApp = ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $relativeInsideApp), '/');
            $entryName = 'app/'.$relativeInsideApp;

            if (! $zip->addFile($real, $entryName)) {
                $zip->close();
                @unlink($zipPath);
                $error('ZIP addFile a échoué : '.$real);

                return null;
            }
        }

        $zip->close();

        return $zipPath;
    }
}

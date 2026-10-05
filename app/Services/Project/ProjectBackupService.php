<?php

declare(strict_types=1);

namespace App\Services\Project;

use Symfony\Component\Process\Process;

/**
 * Sauvegarde atomique ZIP (BDD + storage/app + private/game), inventaire, purge et verrou global.
 *
 * Format : `{prefix}_YYYY-MM-DD_HH-mm-ss_xxxx.zip` avec manifeste v2 et checksums.
 * Les anciennes paires `*_mysql.sql.gz` / `*_storage.*` restent listables et purgables.
 *
 * @example
 * $svc = ProjectBackupService::fromConfig();
 * $result = $svc->run(true, true, true, true, false, fn ($m) => null, fn ($m) => null);
 */
class ProjectBackupService
{
    public const MANIFEST_ENTRY = 'manifest.json';

    public const DATABASE_MYSQL_ENTRY = 'database/mysql.sql.gz';

    public const DATABASE_SQLITE_ENTRY = 'database/sqlite.db.gz';

    public const STORAGE_PREFIX = 'storage/app/';

    public const GAME_PREFIX = 'private/game/';

    private const LOCK_FILENAME = '.project-backup.lock';

    /** @var resource|null */
    private $lockHandle = null;

    public function __construct(
        private readonly string $backupRoot,
        private readonly int $retentionDays,
        private readonly string $mysqldumpBinary,
        private readonly string $filenamePrefix,
        private readonly int $dumpTimeout = 3600,
        private readonly int $archiveTimeout = 7200,
        private readonly int $lockTtl = 7200,
        private readonly int $manifestVersion = 2,
    ) {}

    /**
     * Répertoire des sauvegardes tel que résolu (config ou défaut {@see storage_path}('app/backups')).
     */
    public function resolvedBackupDirectory(): string
    {
        return $this->backupRoot;
    }

    public function filenamePrefix(): string
    {
        return $this->filenamePrefix;
    }

    public function retentionDays(): int
    {
        return $this->retentionDays;
    }

    public static function fromConfig(?string $pathOverride = null, ?int $retentionOverride = null): self
    {
        $configured = (string) config('project-backup.path', '');
        $root = $pathOverride !== null && $pathOverride !== ''
            ? $pathOverride
            : ($configured !== '' ? $configured : storage_path('app/backups'));

        $root = self::resolveAndAssertBackupPath($root);

        return new self(
            backupRoot: $root,
            retentionDays: max(1, $retentionOverride ?? (int) config('project-backup.retention_days', 30)),
            mysqldumpBinary: (string) config('project-backup.mysqldump_path', '') ?: 'mysqldump',
            filenamePrefix: (string) config('project-backup.filename_prefix', '') ?: 'project-backup',
            dumpTimeout: max(60, (int) config('project-backup.dump_timeout', 3600)),
            archiveTimeout: max(60, (int) config('project-backup.archive_timeout', 7200)),
            lockTtl: max(60, (int) config('project-backup.lock_ttl', 7200)),
            manifestVersion: max(1, (int) config('project-backup.manifest_version', 2)),
        );
    }

    /**
     * Résout un chemin de backup et exige qu’il reste sous la racine du projet.
     *
     * @throws \InvalidArgumentException
     */
    public static function resolveAndAssertBackupPath(string $path): string
    {
        $base = realpath(base_path());
        if ($base === false) {
            throw new \InvalidArgumentException('Racine projet introuvable.');
        }

        $candidate = str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\\\/', $path)
            ? $path
            : base_path($path);

        if (! is_dir($candidate) && ! mkdir($candidate, 0750, true) && ! is_dir($candidate)) {
            throw new \InvalidArgumentException('Impossible de créer le répertoire de sauvegarde : '.$candidate);
        }

        $resolved = realpath($candidate);
        if ($resolved === false) {
            throw new \InvalidArgumentException('Répertoire de sauvegarde introuvable : '.$candidate);
        }

        if (! str_starts_with($resolved, $base.DIRECTORY_SEPARATOR) && $resolved !== $base) {
            throw new \InvalidArgumentException(
                'Le répertoire de sauvegarde doit rester sous la racine du projet.'
            );
        }

        return $resolved;
    }

    /**
     * Acquiert le verrou global (CLI, cron, web).
     *
     * @throws \RuntimeException
     */
    public function acquireLock(): void
    {
        $this->ensureDirectory($this->backupRoot);
        $lockPath = $this->backupRoot.DIRECTORY_SEPARATOR.self::LOCK_FILENAME;
        $handle = fopen($lockPath, 'c+');
        if ($handle === false) {
            throw new \RuntimeException('Impossible d’ouvrir le verrou de sauvegarde.');
        }

        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            throw new \RuntimeException('Une sauvegarde ou une restauration est déjà en cours.');
        }

        ftruncate($handle, 0);
        fwrite($handle, (string) getmypid()."\n".time()."\n");
        fflush($handle);
        $this->lockHandle = $handle;
    }

    public function releaseLock(): void
    {
        if ($this->lockHandle === null) {
            return;
        }

        flock($this->lockHandle, LOCK_UN);
        fclose($this->lockHandle);
        $this->lockHandle = null;
    }

    public function isLocked(): bool
    {
        $lockPath = $this->backupRoot.DIRECTORY_SEPARATOR.self::LOCK_FILENAME;
        if (! is_file($lockPath)) {
            return false;
        }

        $handle = @fopen($lockPath, 'c+');
        if ($handle === false) {
            return true;
        }

        $got = flock($handle, LOCK_EX | LOCK_NB);
        if ($got) {
            flock($handle, LOCK_UN);
            fclose($handle);

            return false;
        }

        fclose($handle);

        return true;
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     * @return array{run_id: string, files: list<string>, seeder_exports: list<string>, ok: bool}
     */
    public function run(
        bool $withDatabase,
        bool $withStorage,
        bool $prune,
        bool $dryRun,
        callable $log,
        callable $error,
        bool $withSeederData = false,
        ?SeederDataExportService $seederDataExport = null,
        bool $withGame = true,
    ): array {
        if (! $withDatabase && ! $withStorage && ! $withGame && ! $withSeederData) {
            $error('Rien à sauvegarder : utilisez la BDD, le storage, private/game et/ou les fichiers de seed.');

            return ['run_id' => '', 'files' => [], 'seeder_exports' => [], 'ok' => false];
        }

        $this->ensureDirectory($this->backupRoot);

        $runId = $this->makeRunId();
        $archiveName = $this->filenamePrefix.'_'.$runId.'.zip';
        $finalPath = $this->backupRoot.DIRECTORY_SEPARATOR.$archiveName;
        $stagingDir = $this->backupRoot.DIRECTORY_SEPARATOR.'.staging_'.$runId;
        $created = [];
        $seederExports = [];
        $ok = false;
        $needsArchive = $withDatabase || $withStorage || $withGame;

        try {
            if (! $needsArchive) {
                if ($withSeederData) {
                    $exporter = $seederDataExport ?? app(SeederDataExportService::class);
                    $seederExports = $exporter->export($log);
                }
                if ($prune) {
                    $this->pruneOldBackups($dryRun, $log, $error);
                }

                return [
                    'run_id' => $runId,
                    'files' => [],
                    'seeder_exports' => $seederExports,
                    'ok' => $seederExports !== [],
                ];
            }

            $this->ensureDirectory($stagingDir);

            $components = [];
            $checksums = [];

            if ($withDatabase) {
                $dbRelative = $this->databaseEntryName();
                $dbPath = $stagingDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $dbRelative);
                $this->ensureDirectory(dirname($dbPath));
                $code = $this->dumpDatabase($dbPath, $log, $error);
                if ($code !== 0 || ! $this->isNonEmptyFile($dbPath)) {
                    $error('Dump BDD invalide ou vide.');

                    return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
                }
                $components['database'] = [
                    'entry' => $dbRelative,
                    'driver' => (string) config('database.connections.'.config('database.default').'.driver', 'mysql'),
                    'size' => filesize($dbPath) ?: 0,
                ];
                $checksums[$dbRelative] = hash_file('sha256', $dbPath) ?: '';
            }

            if ($withStorage) {
                $storageStaging = $stagingDir.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app';
                $copied = $this->copyDirectoryFiltered(
                    storage_path('app'),
                    $storageStaging,
                    $this->backupRoot,
                    $log,
                    $error,
                );
                if (! $copied) {
                    return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
                }
                $components['storage'] = ['prefix' => self::STORAGE_PREFIX];
            }

            if ($withGame) {
                $gameSrc = base_path('private/game');
                if (! is_dir($gameSrc)) {
                    $error('Répertoire private/game introuvable.');

                    return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
                }
                $gameStaging = $stagingDir.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'game';
                $copied = $this->copyDirectoryFiltered($gameSrc, $gameStaging, null, $log, $error);
                if (! $copied) {
                    return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
                }
                $components['game'] = ['prefix' => self::GAME_PREFIX];
            }

            if ($withStorage || $withGame) {
                foreach ($this->iterateFiles($stagingDir) as $absolute => $relative) {
                    if ($relative === self::MANIFEST_ENTRY || str_starts_with($relative, 'database/')) {
                        continue;
                    }
                    $hash = hash_file('sha256', $absolute);
                    if ($hash === false) {
                        $error('Checksum impossible : '.$relative);

                        return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
                    }
                    $checksums[$relative] = $hash;
                }
            }

            $manifest = [
                'version' => $this->manifestVersion,
                'run_id' => $runId,
                'created_at' => now()->toIso8601String(),
                'app_env' => (string) app()->environment(),
                'app_version' => (string) config('app.version', ''),
                'components' => $components,
                'checksums' => $checksums,
                'filename' => $archiveName,
            ];

            $manifestPath = $stagingDir.DIRECTORY_SEPARATOR.self::MANIFEST_ENTRY;
            $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($manifestJson === false || file_put_contents($manifestPath, $manifestJson) === false) {
                $error('Écriture du manifeste impossible.');

                return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
            }

            $tmpZip = $finalPath.'.partial';
            @unlink($tmpZip);
            if (! $this->buildZipFromStaging($stagingDir, $tmpZip, $log, $error)) {
                @unlink($tmpZip);

                return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
            }

            if (! $this->isNonEmptyFile($tmpZip) || ! $this->zipLooksValid($tmpZip)) {
                @unlink($tmpZip);
                $error('Archive ZIP invalide ou vide.');

                return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
            }

            @chmod($tmpZip, 0640);
            if (! @rename($tmpZip, $finalPath)) {
                @unlink($tmpZip);
                $error('Impossible de finaliser l’archive : '.$finalPath);

                return ['run_id' => $runId, 'files' => [], 'seeder_exports' => [], 'ok' => false];
            }

            $created[] = $finalPath;
            $log('Archive : '.$finalPath);
            $ok = true;

            if ($withSeederData) {
                $exporter = $seederDataExport ?? app(SeederDataExportService::class);
                $seederExports = $exporter->export($log);
            }

            if ($prune) {
                $this->pruneOldBackups($dryRun, $log, $error);
            }

            return ['run_id' => $runId, 'files' => $created, 'seeder_exports' => $seederExports, 'ok' => $ok];
        } finally {
            $this->removeDirectory($stagingDir);
            if (isset($tmpZip) && is_file($tmpZip)) {
                @unlink($tmpZip);
            }
        }
    }

    /**
     * @return list<array{
     *     name: string,
     *     path: string,
     *     size: int,
     *     mtime: int,
     *     format: 'zip'|'legacy',
     *     run_id: string,
     *     components: list<string>,
     *     integrity: 'ok'|'unknown'|'invalid'|'legacy',
     *     created_at: string|null
     * }>
     */
    public function listBackups(): array
    {
        if (! is_dir($this->backupRoot)) {
            return [];
        }

        $items = [];
        $legacyGroups = [];

        $quotedPrefix = preg_quote($this->filenamePrefix, '/');
        $zipPattern = '/^'.$quotedPrefix.'_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_\d{4})\.zip$/';
        $legacyPattern = '/^'.$quotedPrefix.'_(.+)_(mysql\.sql\.gz|storage\.tar\.gz|storage\.zip)$/';

        foreach (scandir($this->backupRoot) ?: [] as $name) {
            if ($name === '.' || $name === '..' || str_starts_with($name, '.')) {
                continue;
            }
            $full = $this->backupRoot.DIRECTORY_SEPARATOR.$name;
            if (! is_file($full)) {
                continue;
            }

            if (preg_match($zipPattern, $name, $m)) {
                $meta = $this->inspectZipArchive($full);
                $items[] = [
                    'name' => $name,
                    'path' => $full,
                    'size' => (int) (filesize($full) ?: 0),
                    'mtime' => (int) (@filemtime($full) ?: 0),
                    'format' => 'zip',
                    'run_id' => $m[1],
                    'components' => $meta['components'],
                    'integrity' => $meta['integrity'],
                    'created_at' => $meta['created_at'],
                ];

                continue;
            }

            if (preg_match($legacyPattern, $name, $m)) {
                $runId = $m[1];
                $legacyGroups[$runId] ??= [
                    'name' => $this->filenamePrefix.'_'.$runId,
                    'files' => [],
                    'size' => 0,
                    'mtime' => 0,
                    'components' => [],
                ];
                $legacyGroups[$runId]['files'][] = $name;
                $legacyGroups[$runId]['size'] += (int) (filesize($full) ?: 0);
                $legacyGroups[$runId]['mtime'] = max(
                    $legacyGroups[$runId]['mtime'],
                    (int) (@filemtime($full) ?: 0)
                );
                if (str_contains($m[2], 'mysql')) {
                    $legacyGroups[$runId]['components'][] = 'database';
                }
                if (str_starts_with($m[2], 'storage')) {
                    $legacyGroups[$runId]['components'][] = 'storage';
                }
            }
        }

        foreach ($legacyGroups as $runId => $group) {
            $items[] = [
                'name' => (string) $group['name'],
                'path' => $this->backupRoot.DIRECTORY_SEPARATOR.(string) $group['files'][0],
                'size' => (int) $group['size'],
                'mtime' => (int) $group['mtime'],
                'format' => 'legacy',
                'run_id' => (string) $runId,
                'components' => array_values(array_unique($group['components'])),
                'integrity' => 'legacy',
                'created_at' => null,
                'legacy_files' => $group['files'],
            ];
        }

        usort($items, static fn (array $a, array $b): int => ($b['mtime'] <=> $a['mtime']) ?: strcmp($b['name'], $a['name']));

        return $items;
    }

    /**
     * Résout un nom d’archive canonique sous le répertoire de backup (anti path-traversal).
     *
     * @throws \InvalidArgumentException
     */
    public function resolveArchiveName(string $name): string
    {
        $name = basename(str_replace(["\0", '\\'], '', $name));
        if ($name === '' || str_contains($name, '..')) {
            throw new \InvalidArgumentException('Nom d’archive invalide.');
        }

        $quotedPrefix = preg_quote($this->filenamePrefix, '/');
        if (! preg_match('/^'.$quotedPrefix.'_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_\d{4}\.zip$/', $name)
            && ! preg_match('/^'.$quotedPrefix.'_.+$/', $name)) {
            throw new \InvalidArgumentException('Nom d’archive non reconnu.');
        }

        return $name;
    }

    /**
     * @return array{deleted: list<string>, format: string}|null
     */
    public function deleteBackup(string $name, callable $log, callable $error): ?array
    {
        try {
            $name = $this->resolveArchiveName($name);
        } catch (\InvalidArgumentException $e) {
            $error($e->getMessage());

            return null;
        }

        $zipPath = $this->backupRoot.DIRECTORY_SEPARATOR.$name;
        if (is_file($zipPath) && str_ends_with($name, '.zip')) {
            if (@unlink($zipPath)) {
                $log('Supprimé : '.$zipPath);

                return ['deleted' => [$name], 'format' => 'zip'];
            }
            $error('Impossible de supprimer : '.$zipPath);

            return null;
        }

        // Legacy group by run label (prefix_runId without extension)
        $quotedPrefix = preg_quote($this->filenamePrefix, '/');
        $legacyPattern = '/^'.$quotedPrefix.'_(.+)$/';
        if (! preg_match($legacyPattern, $name, $m)) {
            $error('Archive introuvable : '.$name);

            return null;
        }
        $runId = $m[1];
        $deleted = [];
        foreach (scandir($this->backupRoot) ?: [] as $file) {
            if (! preg_match(
                '/^'.$quotedPrefix.'_'.preg_quote($runId, '/').'_(mysql\.sql\.gz|storage\.tar\.gz|storage\.zip)$/',
                $file
            )) {
                continue;
            }
            $full = $this->backupRoot.DIRECTORY_SEPARATOR.$file;
            if (@unlink($full)) {
                $log('Supprimé : '.$full);
                $deleted[] = $file;
            } else {
                $error('Impossible de supprimer : '.$full);
            }
        }

        if ($deleted === []) {
            $error('Archive introuvable : '.$name);

            return null;
        }

        return ['deleted' => $deleted, 'format' => 'legacy'];
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
        $removed = 0;

        foreach ($this->listBackups() as $item) {
            if ($item['mtime'] >= $cutoff) {
                continue;
            }

            if ($dryRun) {
                $log('[dry-run] Supprimerait : '.$item['name'].' (âge > '.$this->retentionDays.' j)');
                $removed++;

                continue;
            }

            $result = $this->deleteBackup($item['name'], $log, $error);
            if ($result !== null) {
                $removed += count($result['deleted']);
            }
        }

        return $removed;
    }

    /**
     * @return array{manifest: array<string, mixed>, path: string}
     *
     * @throws \RuntimeException
     */
    public function openAndVerifyZip(string $archiveName, bool $verifyChecksums = true): array
    {
        $archiveName = $this->resolveArchiveName($archiveName);
        if (! str_ends_with($archiveName, '.zip')) {
            throw new \RuntimeException('Seules les archives ZIP v2 peuvent être restaurées automatiquement.');
        }

        $path = $this->backupRoot.DIRECTORY_SEPARATOR.$archiveName;
        $real = realpath($path);
        $rootReal = realpath($this->backupRoot);
        if ($real === false || $rootReal === false
            || (! str_starts_with($real, $rootReal.DIRECTORY_SEPARATOR) && $real !== $rootReal)
            || ! is_file($real)) {
            throw new \RuntimeException('Archive introuvable : '.$archiveName);
        }

        $zip = new \ZipArchive;
        if ($zip->open($real) !== true) {
            throw new \RuntimeException('Impossible d’ouvrir l’archive ZIP.');
        }

        try {
            $this->assertZipEntriesSafe($zip);
            $manifestRaw = $zip->getFromName(self::MANIFEST_ENTRY);
            if ($manifestRaw === false) {
                throw new \RuntimeException('Manifeste absent de l’archive.');
            }
            $manifest = json_decode($manifestRaw, true);
            if (! is_array($manifest) || (int) ($manifest['version'] ?? 0) < 2) {
                throw new \RuntimeException('Manifeste invalide ou version non supportée.');
            }

            if ($verifyChecksums) {
                $checksums = $manifest['checksums'] ?? [];
                if (! is_array($checksums) || $checksums === []) {
                    throw new \RuntimeException('Checksums absents du manifeste.');
                }
                foreach ($checksums as $entry => $expected) {
                    if (! is_string($entry) || ! is_string($expected) || $expected === '') {
                        throw new \RuntimeException('Checksum invalide pour une entrée.');
                    }
                    $this->assertZipEntryAllowed($entry);
                    $data = $zip->getFromName($entry);
                    if ($data === false) {
                        throw new \RuntimeException('Entrée manquante dans l’archive : '.$entry);
                    }
                    if (hash('sha256', $data) !== $expected) {
                        throw new \RuntimeException('Checksum invalide : '.$entry);
                    }
                }
            }

            return ['manifest' => $manifest, 'path' => $real];
        } finally {
            $zip->close();
        }
    }

    public function makeRunId(): string
    {
        return now()->format('Y-m-d_H-i-s').'_'.substr(str_replace('.', '', (string) microtime(true)), -4);
    }

    private function databaseEntryName(): string
    {
        $connection = (string) config('database.default');
        $driver = (string) config("database.connections.{$connection}.driver", 'mysql');

        return $driver === 'sqlite' ? self::DATABASE_SQLITE_ENTRY : self::DATABASE_MYSQL_ENTRY;
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path) && ! mkdir($path, 0750, true) && ! is_dir($path)) {
            throw new \RuntimeException('Impossible de créer le répertoire : '.$path);
        }
    }

    private function isNonEmptyFile(string $path): bool
    {
        return is_file($path) && (filesize($path) ?: 0) > 0;
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function dumpDatabase(string $outputGz, callable $log, callable $error): int
    {
        $connection = (string) config('database.default');
        /** @var array<string, mixed> $dbConfig */
        $dbConfig = config("database.connections.{$connection}", []);
        $driver = (string) ($dbConfig['driver'] ?? 'mysql');

        if ($driver === 'sqlite') {
            return $this->dumpSqlite($dbConfig, $outputGz, $log, $error);
        }

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            $error("Driver BDD non pris en charge pour le dump : {$driver} (supportés : mysql, mariadb, sqlite).");

            return 1;
        }

        return $this->dumpMysql($dbConfig, $outputGz, $log, $error);
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function dumpMysql(array $config, string $outputGz, callable $log, callable $error): int
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

        $gzHandle = gzopen($outputGz, 'wb9');
        if ($gzHandle === false) {
            @unlink($defaultsFile);
            $error('Écriture gzip impossible : '.$outputGz);

            return 1;
        }

        try {
            $log('Exécution mysqldump…');
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
            $dump->setTimeout($this->dumpTimeout);
            $dump->run(function (string $type, string $buffer) use ($gzHandle): void {
                if ($type === Process::OUT) {
                    gzwrite($gzHandle, $buffer);
                }
            });

            gzclose($gzHandle);
            $gzHandle = null;

            if (! $dump->isSuccessful()) {
                @unlink($outputGz);
                $error('mysqldump a échoué : '.$dump->getErrorOutput().$dump->getOutput());

                return 1;
            }

            if (! $this->isNonEmptyFile($outputGz)) {
                @unlink($outputGz);
                $error('Dump gzip vide après mysqldump.');

                return 1;
            }

            return 0;
        } finally {
            if (is_resource($gzHandle)) {
                gzclose($gzHandle);
            }
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
    private function dumpSqlite(array $config, string $outputGz, callable $log, callable $error): int
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

        $gzHandle = gzopen($outputGz, 'wb9');
        if ($gzHandle === false) {
            fclose($handle);
            $error('Écriture gzip impossible : '.$outputGz);

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

        if (! $this->isNonEmptyFile($outputGz)) {
            @unlink($outputGz);
            $error('Dump SQLite gzip vide.');

            return 1;
        }

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
     */
    private function copyDirectoryFiltered(
        string $source,
        string $destination,
        ?string $excludeRealPath,
        callable $log,
        callable $error,
    ): bool {
        $sourceReal = realpath($source);
        if ($sourceReal === false || ! is_dir($sourceReal)) {
            $error('Source introuvable : '.$source);

            return false;
        }

        $excludeReal = $excludeRealPath !== null ? realpath($excludeRealPath) : false;
        $log('Copie filtrée : '.$sourceReal);

        $this->ensureDirectory($destination);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceReal, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            /** @var \SplFileInfo $item */
            $real = $item->getRealPath();
            if ($real === false) {
                continue;
            }

            if ($excludeReal !== false
                && ($real === $excludeReal || str_starts_with($real, $excludeReal.DIRECTORY_SEPARATOR))) {
                continue;
            }

            if (! str_starts_with($real, $sourceReal.DIRECTORY_SEPARATOR) && $real !== $sourceReal) {
                continue;
            }

            $relative = ltrim(substr($real, strlen($sourceReal)), DIRECTORY_SEPARATOR);
            $target = $destination.($relative !== '' ? DIRECTORY_SEPARATOR.$relative : '');

            if ($item->isDir()) {
                $this->ensureDirectory($target);

                continue;
            }

            if ($item->isLink()) {
                continue;
            }

            if (! $item->isFile()) {
                continue;
            }

            $this->ensureDirectory(dirname($target));
            if (! @copy($real, $target)) {
                $error('Copie impossible : '.$real);

                return false;
            }
        }

        return true;
    }

    /**
     * @param  callable(string): void  $log
     * @param  callable(string): void  $error
     */
    private function buildZipFromStaging(string $stagingDir, string $zipPath, callable $log, callable $error): bool
    {
        $log('Compression ZIP…');
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $error('Impossible de créer le ZIP : '.$zipPath);

            return false;
        }

        $stagingReal = realpath($stagingDir);
        if ($stagingReal === false) {
            $zip->close();
            $error('Staging introuvable.');

            return false;
        }

        foreach ($this->iterateFiles($stagingReal) as $absolute => $relative) {
            if (! $zip->addFile($absolute, $relative)) {
                $zip->close();
                $error('ZIP addFile a échoué : '.$relative);

                return false;
            }
        }

        if (! $zip->close()) {
            $error('Fermeture ZIP impossible.');

            return false;
        }

        return true;
    }

    /**
     * @return \Generator<string, string> absolute => relative posix
     */
    private function iterateFiles(string $directory): \Generator
    {
        $baseReal = realpath($directory);
        if ($baseReal === false) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseReal, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (! $file->isFile()) {
                continue;
            }
            $real = $file->getRealPath();
            if ($real === false) {
                continue;
            }
            $relative = ltrim(str_replace(DIRECTORY_SEPARATOR, '/', substr($real, strlen($baseReal))), '/');
            if ($relative === '') {
                continue;
            }

            yield $real => $relative;
        }
    }

    private function zipLooksValid(string $path): bool
    {
        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            return false;
        }
        $hasManifest = $zip->locateName(self::MANIFEST_ENTRY) !== false;
        $zip->close();

        return $hasManifest;
    }

    /**
     * Inspection légère pour l’inventaire (pas de relecture complète des checksums).
     *
     * @return array{components: list<string>, integrity: 'ok'|'invalid'|'unknown', created_at: string|null}
     */
    private function inspectZipArchive(string $path): array
    {
        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            return ['components' => [], 'integrity' => 'invalid', 'created_at' => null];
        }

        try {
            try {
                $this->assertZipEntriesSafe($zip);
            } catch (\Throwable) {
                return ['components' => [], 'integrity' => 'invalid', 'created_at' => null];
            }

            $manifestRaw = $zip->getFromName(self::MANIFEST_ENTRY);
            if ($manifestRaw === false) {
                return ['components' => [], 'integrity' => 'unknown', 'created_at' => null];
            }

            $manifest = json_decode($manifestRaw, true);
            if (! is_array($manifest) || (int) ($manifest['version'] ?? 0) < 2) {
                return ['components' => [], 'integrity' => 'invalid', 'created_at' => null];
            }

            $components = array_values(array_map('strval', array_keys($manifest['components'] ?? [])));
            $checksums = $manifest['checksums'] ?? null;
            $integrity = is_array($checksums) && $checksums !== [] ? 'ok' : 'invalid';

            return [
                'components' => $components,
                'integrity' => $integrity,
                'created_at' => isset($manifest['created_at']) ? (string) $manifest['created_at'] : null,
            ];
        } finally {
            $zip->close();
        }
    }

    private function assertZipEntriesSafe(\ZipArchive $zip): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $this->assertZipEntryAllowed($name);
        }
    }

    private function assertZipEntryAllowed(string $entry): void
    {
        $normalized = str_replace('\\', '/', $entry);
        if ($normalized === '' || str_starts_with($normalized, '/')
            || str_contains($normalized, '..')
            || str_contains($normalized, "\0")) {
            throw new \RuntimeException('Entrée ZIP dangereuse refusée : '.$entry);
        }

        if ($normalized === self::MANIFEST_ENTRY
            || $normalized === self::DATABASE_MYSQL_ENTRY
            || $normalized === self::DATABASE_SQLITE_ENTRY
            || str_starts_with($normalized, self::STORAGE_PREFIX)
            || str_starts_with($normalized, self::GAME_PREFIX)) {
            return;
        }

        throw new \RuntimeException('Entrée ZIP hors liste blanche : '.$entry);
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

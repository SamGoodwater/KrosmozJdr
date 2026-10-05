<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Services\Project\ProjectBackupRestoreService;
use App\Services\Project\ProjectBackupService;
use Tests\TestCase;

/**
 * Restauration SQLite complète avec sauvegarde de secours.
 *
 * Le storage copié par le secours est un répertoire temporaire : le storage réel
 * contient des centaines de milliers de médias, et un test tué en cours laisserait
 * cette copie dans le projet (indexation IDE, saturation RAM WSL).
 */
final class ProjectBackupRestoreServiceTest extends TestCase
{
    private string $backupDir;

    private string $storageRoot;

    private string $sqlitePath;

    private string $originalDefault;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalDefault = (string) config('database.default');
        $token = uniqid('d', false);
        $this->storageRoot = sys_get_temp_dir().'/kz_backup_storage_'.$token;
        // La sortie ZIP doit rester sous la racine du projet (resolveAndAssertBackupPath).
        $this->backupDir = storage_path('framework/testing/backup-restore-'.$token);
        mkdir($this->storageRoot.'/app/public', 0700, true);
        mkdir($this->storageRoot.'/framework', 0700, true);
        mkdir($this->storageRoot.'/logs', 0700, true);
        mkdir($this->backupDir, 0700, true);
        file_put_contents($this->storageRoot.'/app/public/marker.txt', 'marker');
        $this->app->useStoragePath($this->storageRoot);

        $this->sqlitePath = sys_get_temp_dir().'/kz_backup_restore_'.$token.'.sqlite';
        file_put_contents($this->sqlitePath, 'ORIGINAL-SQLITE-CONTENT-'.$token);
    }

    protected function tearDown(): void
    {
        config(['database.default' => $this->originalDefault]);

        $this->removeTree($this->backupDir);
        $this->removeTree($this->storageRoot);
        if (is_file($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        // Le mode maintenance écrit sous le storage actif (temporaire, déjà supprimé).
        // Filet sur le storage du projet si un appel a échappé à useStoragePath.
        $down = base_path('storage/framework/down');
        if (is_file($down)) {
            @unlink($down);
        }

        parent::tearDown();
    }

    public function test_restore_sqlite_database_from_zip(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.driver' => 'sqlite',
            'database.connections.sqlite.database' => $this->sqlitePath,
            'project-backup.path' => $this->backupDir,
            'project-backup.filename_prefix' => 'project-backup',
        ]);

        $backups = ProjectBackupService::fromConfig($this->backupDir, 30);
        $created = $backups->run(true, false, false, false, static fn () => null, static fn () => null, false, null, false);
        $this->assertTrue($created['ok']);
        $archive = basename($created['files'][0]);

        file_put_contents($this->sqlitePath, 'MUTATED-CONTENT');

        $restorer = new ProjectBackupRestoreService($backups, 'mysql', 120);
        $result = $restorer->restore(
            $archive,
            static fn () => null,
            static fn () => null,
            createSafetyBackup: true,
        );

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertNotNull($result['safety_archive']);
        $this->assertStringStartsWith('ORIGINAL-SQLITE-CONTENT-', (string) file_get_contents($this->sqlitePath));
        $this->assertFileExists($this->backupDir.DIRECTORY_SEPARATOR.$result['safety_archive']);
    }

    /**
     * Supprime un répertoire temporaire, y compris les entrées cachées (`.staging_*`).
     */
    private function removeTree(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir() && ! $item->isLink()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }
        @rmdir($directory);
    }
}

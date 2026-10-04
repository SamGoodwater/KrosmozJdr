<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Services\Project\ProjectBackupRestoreService;
use App\Services\Project\ProjectBackupService;
use Tests\TestCase;

/**
 * Restauration SQLite complète avec sauvegarde de secours.
 */
final class ProjectBackupRestoreServiceTest extends TestCase
{
    private string $backupDir;

    private string $sqlitePath;

    private string $originalDefault;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalDefault = (string) config('database.default');
        $this->backupDir = storage_path('framework/testing/backup-restore-'.uniqid('d', false));
        mkdir($this->backupDir, 0700, true);

        $this->sqlitePath = sys_get_temp_dir().'/kz_backup_restore_'.uniqid('d', false).'.sqlite';
        file_put_contents($this->sqlitePath, 'ORIGINAL-SQLITE-CONTENT-'.uniqid('d', false));
    }

    protected function tearDown(): void
    {
        config(['database.default' => $this->originalDefault]);

        foreach (glob($this->backupDir.DIRECTORY_SEPARATOR.'*') ?: [] as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
        @rmdir($this->backupDir);
        if (is_file($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        // Sortir du mode maintenance si un test l’a laissé actif.
        $down = storage_path('framework/down');
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
}

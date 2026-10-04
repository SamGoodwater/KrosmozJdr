<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Services\Project\ProjectBackupService;
use App\Services\Project\SeederDataExportService;
use Mockery;
use Tests\TestCase;
use ZipArchive;

/**
 * Création d’archive ZIP v2 (SQLite CI-safe, hors dump MySQL réel).
 */
final class ProjectBackupServiceRunTest extends TestCase
{
    private string $backupDir;

    private string $sqlitePath;

    private string $originalDefault;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalDefault = (string) config('database.default');
        $this->backupDir = storage_path('framework/testing/backup-run-'.uniqid('d', false));
        mkdir($this->backupDir, 0700, true);

        $this->sqlitePath = sys_get_temp_dir().'/kz_backup_db_'.uniqid('d', false).'.sqlite';
        file_put_contents($this->sqlitePath, 'SQLite format 3'.str_repeat("\0", 100));
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

        parent::tearDown();
    }

    /**
     * Bascule temporaire vers SQLite fichier sans toucher la connexion MySQL de RefreshDatabase.
     */
    private function useSqliteForDump(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.driver' => 'sqlite',
            'database.connections.sqlite.database' => $this->sqlitePath,
            'project-backup.path' => $this->backupDir,
            'project-backup.filename_prefix' => 'project-backup',
        ]);
    }

    public function test_run_creates_timestamped_zip_with_database_manifest(): void
    {
        $this->useSqliteForDump();
        $service = ProjectBackupService::fromConfig($this->backupDir, 30);
        $result = $service->run(
            withDatabase: true,
            withStorage: false,
            prune: false,
            dryRun: false,
            log: static fn () => null,
            error: static fn () => null,
            withSeederData: false,
            withGame: false,
        );

        $this->assertTrue($result['ok'], 'dump/archive devrait réussir');
        $this->assertNotSame('', $result['run_id']);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_\d{4}$/',
            $result['run_id']
        );
        $this->assertCount(1, $result['files']);
        $archive = $result['files'][0];
        $this->assertFileExists($archive);
        $this->assertMatchesRegularExpression(
            '/project-backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_\d{4}\.zip$/',
            basename($archive)
        );

        $verified = $service->openAndVerifyZip(basename($archive), true);
        $this->assertSame(2, (int) $verified['manifest']['version']);
        $this->assertArrayHasKey('database', $verified['manifest']['components']);
        $this->assertArrayHasKey(ProjectBackupService::DATABASE_SQLITE_ENTRY, $verified['manifest']['checksums']);
    }

    public function test_list_and_delete_zip_archive(): void
    {
        $this->useSqliteForDump();
        $service = ProjectBackupService::fromConfig($this->backupDir, 30);
        $result = $service->run(true, false, false, false, static fn () => null, static fn () => null, false, null, false);
        $this->assertTrue($result['ok']);
        $name = basename($result['files'][0]);

        $list = $service->listBackups();
        $this->assertNotEmpty($list);
        $this->assertSame($name, $list[0]['name']);
        $this->assertSame('zip', $list[0]['format']);

        $deleted = $service->deleteBackup($name, static fn () => null, static fn () => null);
        $this->assertNotNull($deleted);
        $this->assertFileDoesNotExist($result['files'][0]);
    }

    public function test_verify_rejects_tampered_checksum(): void
    {
        $this->useSqliteForDump();
        $service = ProjectBackupService::fromConfig($this->backupDir, 30);
        $result = $service->run(true, false, false, false, static fn () => null, static fn () => null, false, null, false);
        $archive = $result['files'][0];

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($archive) === true);
        $manifest = json_decode((string) $zip->getFromName('manifest.json'), true);
        $manifest['checksums'][ProjectBackupService::DATABASE_SQLITE_ENTRY] = str_repeat('0', 64);
        $zip->deleteName('manifest.json');
        $zip->addFromString('manifest.json', (string) json_encode($manifest));
        $zip->close();

        $this->expectException(\RuntimeException::class);
        $service->openAndVerifyZip(basename($archive), true);
    }

    public function test_verify_rejects_zip_slip_entry(): void
    {
        config([
            'project-backup.path' => $this->backupDir,
            'project-backup.filename_prefix' => 'project-backup',
        ]);

        $archive = $this->backupDir.'/project-backup_2026-10-05_04-00-00_9999.zip';
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($archive, ZipArchive::CREATE) === true);
        $zip->addFromString('manifest.json', json_encode([
            'version' => 2,
            'components' => [],
            'checksums' => ['../evil.txt' => hash('sha256', 'x')],
        ]));
        $zip->addFromString('../evil.txt', 'x');
        $zip->close();

        $service = ProjectBackupService::fromConfig($this->backupDir, 30);
        $this->expectException(\RuntimeException::class);
        $service->openAndVerifyZip(basename($archive), true);
    }

    public function test_seeder_export_still_callable(): void
    {
        config([
            'project-backup.path' => $this->backupDir,
            'project-backup.filename_prefix' => 'project-backup',
        ]);

        $fake = Mockery::mock(SeederDataExportService::class);
        $fake->shouldReceive('export')->once()->andReturn(['fake']);

        $service = ProjectBackupService::fromConfig($this->backupDir, 30);
        $result = $service->run(
            false,
            false,
            false,
            false,
            static fn () => null,
            static fn () => null,
            true,
            $fake,
            false,
        );

        $this->assertSame(['fake'], $result['seeder_exports']);
        $this->assertTrue($result['ok']);
    }
}

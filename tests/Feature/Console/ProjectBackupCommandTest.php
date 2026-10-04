<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class ProjectBackupCommandTest extends TestCase
{
    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupDir = storage_path('framework/testing/backup-cmd-'.uniqid('d', false));
        mkdir($this->backupDir, 0700, true);
        config(['project-backup.path' => $this->backupDir]);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->backupDir.DIRECTORY_SEPARATOR.'*') ?: [] as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
        @rmdir($this->backupDir);

        parent::tearDown();
    }

    private function requireMysqldump(): void
    {
        $binary = (string) config('project-backup.mysqldump_path', '') ?: 'mysqldump';
        if (is_file($binary) && is_executable($binary)) {
            return;
        }

        $which = trim((string) shell_exec('command -v '.escapeshellarg($binary).' 2>/dev/null'));
        if ($which === '') {
            $this->markTestSkipped('mysqldump indisponible dans cet environnement.');
        }
    }

    public function test_backup_command_creates_zip_and_returns_success(): void
    {
        $this->requireMysqldump();

        $code = Artisan::call('project:backup', [
            '--path' => $this->backupDir,
            '--no-storage' => true,
            '--no-game' => true,
            '--no-seeder-data' => true,
            '--no-prune' => true,
            '--skip-notify' => true,
        ]);

        $this->assertSame(0, $code, Artisan::output());
        $zips = glob($this->backupDir.'/project-backup_*.zip') ?: [];
        $this->assertCount(1, $zips);
    }

    public function test_backup_command_fails_when_no_target(): void
    {
        $code = Artisan::call('project:backup', [
            '--path' => $this->backupDir,
            '--no-database' => true,
            '--no-storage' => true,
            '--no-game' => true,
            '--no-seeder-data' => true,
            '--skip-notify' => true,
        ]);

        $this->assertNotSame(0, $code);
    }

    public function test_backup_list_command(): void
    {
        $this->requireMysqldump();

        Artisan::call('project:backup', [
            '--path' => $this->backupDir,
            '--no-storage' => true,
            '--no-game' => true,
            '--no-seeder-data' => true,
            '--no-prune' => true,
            '--skip-notify' => true,
        ]);

        $code = Artisan::call('project:backup:list', ['--path' => $this->backupDir]);
        $this->assertSame(0, $code);
        $this->assertStringContainsString('project-backup_', Artisan::output());
    }

    public function test_backup_delete_command_with_yes(): void
    {
        $this->requireMysqldump();

        Artisan::call('project:backup', [
            '--path' => $this->backupDir,
            '--no-storage' => true,
            '--no-game' => true,
            '--no-seeder-data' => true,
            '--no-prune' => true,
            '--skip-notify' => true,
        ]);
        $zips = glob($this->backupDir.'/project-backup_*.zip') ?: [];
        $this->assertCount(1, $zips);
        $name = basename($zips[0]);

        $code = Artisan::call('project:backup:delete', [
            'name' => $name,
            '--path' => $this->backupDir,
            '--yes' => true,
        ]);

        $this->assertSame(0, $code);
        $this->assertFileDoesNotExist($zips[0]);
    }

    public function test_prune_only_dry_run(): void
    {
        $code = Artisan::call('project:backup', [
            '--path' => $this->backupDir,
            '--prune-only' => true,
            '--dry-run' => true,
            '--skip-notify' => true,
        ]);

        $this->assertSame(0, $code);
    }
}

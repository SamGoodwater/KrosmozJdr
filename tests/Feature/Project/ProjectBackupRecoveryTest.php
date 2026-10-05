<?php

declare(strict_types=1);

namespace Tests\Feature\Project;

use App\Services\Project\ProjectBackupOperationStatus;
use App\Services\Project\ProjectBackupRecovery;
use App\Services\Project\ProjectBackupService;
use Tests\TestCase;

/**
 * Résilience : détection d’une restauration orpheline après crash / reboot.
 */
final class ProjectBackupRecoveryTest extends TestCase
{
    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupDir = storage_path('framework/testing/backup-recovery-'.uniqid('d', false));
        mkdir($this->backupDir, 0700, true);
        config([
            'project-backup.path' => $this->backupDir,
            'project-backup.filename_prefix' => 'project-backup',
        ]);
    }

    protected function tearDown(): void
    {
        $status = ProjectBackupOperationStatus::forBackupRoot($this->backupDir);
        $status->clear();
        foreach (glob($this->backupDir.DIRECTORY_SEPARATOR.'*') ?: [] as $f) {
            if (is_file($f)) {
                @unlink($f);
            } elseif (is_dir($f)) {
                foreach (glob($f.DIRECTORY_SEPARATOR.'*') ?: [] as $child) {
                    @unlink($child);
                }
                @rmdir($f);
            }
        }
        // hidden files
        foreach (['.restore-status.json', '.restore-status.json.tmp', '.project-backup.lock'] as $hidden) {
            @unlink($this->backupDir.DIRECTORY_SEPARATOR.$hidden);
        }
        @rmdir($this->backupDir);
        parent::tearDown();
    }

    public function test_reconcile_marks_orphan_running_restore_as_interrupted(): void
    {
        $status = ProjectBackupOperationStatus::forBackupRoot($this->backupDir);
        $status->write([
            'state' => 'running',
            'archive' => 'project-backup_2026-10-05_00-00-00_0001.zip',
            'pid' => 99999999,
            'phase' => 'storage',
            'progress' => 70,
            'message' => 'Restauration storage/app…',
            'safety_archive' => 'project-backup_2026-10-05_00-00-00_0000.zip',
            'log' => [],
        ]);

        $orphan = $this->backupDir.DIRECTORY_SEPARATOR.'.staging_orphan';
        mkdir($orphan, 0700, true);
        file_put_contents($orphan.'/x.txt', 'x');

        $backups = ProjectBackupService::fromConfig($this->backupDir, 30);
        $recovery = new ProjectBackupRecovery($backups, $status);
        $report = $recovery->reconcile();

        $this->assertTrue($report['reconciled']);
        $this->assertSame('interrupted', $report['restore_status']['state'] ?? null);
        $this->assertStringContainsString('interrompue', (string) ($report['restore_status']['message'] ?? ''));
        $this->assertStringContainsString('0000.zip', (string) ($report['restore_status']['message'] ?? ''));
        $this->assertFalse($report['operation_locked']);
        $this->assertDirectoryDoesNotExist($orphan);
    }
}

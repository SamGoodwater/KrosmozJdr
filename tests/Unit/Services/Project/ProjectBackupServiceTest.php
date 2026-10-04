<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Project;

use App\Services\Project\ProjectBackupService;
use PHPUnit\Framework\TestCase;

class ProjectBackupServiceTest extends TestCase
{
    public function test_prune_removes_old_backup_files_only(): void
    {
        $dir = sys_get_temp_dir().'/kz_backup_test_'.uniqid('', true);
        self::assertTrue(mkdir($dir, 0700, true));

        try {
            $oldMysql = $dir.'/project-backup_oldrun_mysql.sql.gz';
            $oldStorage = $dir.'/project-backup_oldrun_storage.tar.gz';
            $recent = $dir.'/project-backup_newrun_mysql.sql.gz';
            $noise = $dir.'/other-file.txt';

            touch($oldMysql, time() - 40 * 86400);
            touch($oldStorage, time() - 40 * 86400);
            touch($recent, time() - 86400);
            file_put_contents($noise, 'x');

            $service = new ProjectBackupService($dir, 30, 'mysqldump', 'project-backup');
            $removed = $service->pruneOldBackups(false, fn () => null, fn () => null);

            self::assertSame(2, $removed);
            self::assertFileDoesNotExist($oldMysql);
            self::assertFileDoesNotExist($oldStorage);
            self::assertFileExists($recent);
            self::assertFileExists($noise);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_prune_dry_run_does_not_delete(): void
    {
        $dir = sys_get_temp_dir().'/kz_backup_dry_'.uniqid('', true);
        self::assertTrue(mkdir($dir, 0700, true));

        try {
            $old = $dir.'/project-backup_x_mysql.sql.gz';
            touch($old, time() - 40 * 86400);

            $service = new ProjectBackupService($dir, 30, 'mysqldump', 'project-backup');
            $removed = $service->pruneOldBackups(true, fn () => null, fn () => null);

            self::assertSame(1, $removed);
            self::assertFileExists($old);
            unlink($old);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_prune_removes_old_zip_archives(): void
    {
        $dir = sys_get_temp_dir().'/kz_backup_zip_prune_'.uniqid('', true);
        self::assertTrue(mkdir($dir, 0700, true));

        try {
            $oldZip = $dir.'/project-backup_2020-01-01_04-00-00_0001.zip';
            $recentZip = $dir.'/project-backup_2026-10-05_04-00-00_0002.zip';
            file_put_contents($oldZip, 'PK');
            file_put_contents($recentZip, 'PK');
            touch($oldZip, time() - 40 * 86400);
            touch($recentZip, time() - 86400);

            $service = new ProjectBackupService($dir, 30, 'mysqldump', 'project-backup');
            $removed = $service->pruneOldBackups(false, fn () => null, fn () => null);

            self::assertSame(1, $removed);
            self::assertFileDoesNotExist($oldZip);
            self::assertFileExists($recentZip);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_resolve_archive_name_rejects_traversal(): void
    {
        $service = new ProjectBackupService(sys_get_temp_dir(), 30, 'mysqldump', 'project-backup');

        $this->expectException(\InvalidArgumentException::class);
        $service->resolveArchiveName('../secret.zip');
    }
}

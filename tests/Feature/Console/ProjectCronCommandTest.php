<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Commands\Project\ProjectCronCommand;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/** @see ProjectCronCommand */
class ProjectCronCommandTest extends TestCase
{
    public function test_project_cron_requires_at_least_one_flag(): void
    {
        $code = Artisan::call('project:cron', []);

        $this->assertNotSame(0, $code);
    }

    public function test_project_cron_clear_succeeds(): void
    {
        $code = Artisan::call('project:cron', [
            '--clear' => true,
        ]);

        $this->assertSame(0, $code);
    }

    public function test_project_cron_rejects_backup_dry_run_without_backup_task(): void
    {
        $code = Artisan::call('project:cron', [
            '--clear' => true,
            '--backup-dry-run' => true,
        ]);

        $this->assertNotSame(0, $code);
    }

    public function test_project_cron_rejects_backup_and_prune_only_together(): void
    {
        $code = Artisan::call('project:cron', [
            '--backup' => true,
            '--backup-prune-only' => true,
        ]);

        $this->assertNotSame(0, $code);
    }

    public function test_project_cron_rejects_orphan_backup_flags(): void
    {
        $code = Artisan::call('project:cron', [
            '--backup-no-database' => true,
        ]);

        $this->assertNotSame(0, $code);
    }

    public function test_project_cron_rejects_orphan_update_flags(): void
    {
        $code = Artisan::call('project:cron', [
            '--update-entity' => 'monster',
        ]);

        $this->assertNotSame(0, $code);
    }

    public function test_project_cron_accepts_update_flag(): void
    {
        $code = Artisan::call('project:cron', [
            '--update' => true,
            '--update-skip-clear-queue' => true,
            '--update-skip-notify' => true,
            '--update-dry-run' => true,
        ]);

        $this->assertSame(0, $code);
    }
}

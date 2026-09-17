<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Jobs\ProcessMediaCleanupJob;
use App\Models\MediaCleanupJob;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @example php artisan test --filter=ProjectClearOrphanFilesCommandTest
 */
class ProjectClearOrphanFilesCommandTest extends TestCase
{
    public function test_queue_option_dispatches_media_cleanup_job(): void
    {
        Bus::fake();

        $this->artisan('project:clear-orphan-files', ['--queue' => true, '--skip-notify' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('media_cleanup_jobs', [
            'mode' => MediaCleanupJob::MODE_DRY_RUN,
            'status' => MediaCleanupJob::STATUS_QUEUED,
        ]);

        Bus::assertDispatched(ProcessMediaCleanupJob::class);
    }

    public function test_queue_delete_option_sets_delete_mode(): void
    {
        Bus::fake();

        $this->artisan('project:clear-orphan-files', [
            '--queue' => true,
            '--delete' => true,
            '--skip-notify' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('media_cleanup_jobs', [
            'mode' => MediaCleanupJob::MODE_DELETE,
            'status' => MediaCleanupJob::STATUS_QUEUED,
        ]);
    }
}

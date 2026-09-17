<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Jobs\ProcessMediaCleanupJob;
use App\Models\MediaCleanupJob;
use App\Models\User;
use App\Notifications\ProjectMaintenanceNotification;
use App\Services\Media\OrphanPublicMediaCleanupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @example php artisan test --filter=ProcessMediaCleanupJobTest
 */
class ProcessMediaCleanupJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_deletes_orphan_files_and_notifies_admins(): void
    {
        Storage::fake('public');
        Notification::fake();

        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        Storage::disk('public')->put('images/entity/spells/999/orphan.png', 'fake');
        Storage::disk('public')->put('images/calendar/javian.png', 'keep');

        $job = MediaCleanupJob::query()->create([
            'status' => MediaCleanupJob::STATUS_QUEUED,
            'mode' => MediaCleanupJob::MODE_DELETE,
            'requested_by' => $admin->id,
            'payload' => ['skip_notify' => false],
        ]);

        (new ProcessMediaCleanupJob($job->id))->handle(app(OrphanPublicMediaCleanupService::class));

        $job->refresh();
        $this->assertSame(MediaCleanupJob::STATUS_SUCCEEDED, $job->status);
        $this->assertSame(1, (int) ($job->summary['deletedCount'] ?? 0));
        $this->assertFalse(Storage::disk('public')->exists('images/entity/spells/999/orphan.png'));
        $this->assertTrue(Storage::disk('public')->exists('images/calendar/javian.png'));

        Notification::assertSentTo($admin, ProjectMaintenanceNotification::class);
    }

    public function test_process_respects_cancellation(): void
    {
        Storage::fake('public');
        Notification::fake();

        Storage::disk('public')->put('images/entity/spells/1/a.png', 'a');
        Storage::disk('public')->put('images/entity/spells/2/b.png', 'b');

        $job = MediaCleanupJob::query()->create([
            'status' => MediaCleanupJob::STATUS_QUEUED,
            'mode' => MediaCleanupJob::MODE_DELETE,
            'payload' => ['skip_notify' => true],
        ]);

        // Annulation avant démarrage du traitement fichier : le job se termine immédiatement
        $job->status = MediaCleanupJob::STATUS_CANCELLED;
        $job->cancelled_at = now();
        $job->finished_at = now();
        $job->save();

        (new ProcessMediaCleanupJob($job->id))->handle(app(OrphanPublicMediaCleanupService::class));

        $job->refresh();
        $this->assertSame(MediaCleanupJob::STATUS_CANCELLED, $job->status);
        $this->assertTrue(Storage::disk('public')->exists('images/entity/spells/1/a.png'));
        Notification::assertNothingSent();
    }
}

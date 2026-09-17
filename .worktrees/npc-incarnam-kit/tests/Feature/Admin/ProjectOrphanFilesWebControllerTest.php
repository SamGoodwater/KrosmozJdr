<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\ProcessMediaCleanupJob;
use App\Models\MediaCleanupJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @example php artisan test --filter=ProjectOrphanFilesWebControllerTest
 */
class ProjectOrphanFilesWebControllerTest extends TestCase
{
    public function test_guest_redirects_from_orphan_files_index(): void
    {
        $response = $this->get(route('admin.orphan-files.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_forbidden_on_orphan_files_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.orphan-files.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_orphan_files_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.orphan-files.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/orphan-files/Index')
            ->has('scannedRoots')
            ->has('recentJobs')
        );
    }

    public function test_super_admin_can_dispatch_orphan_cleanup_job_when_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.orphan-files.run'), [
                'delete' => true,
                'skip_notify' => true,
            ]);

        $response->assertRedirect(route('admin.orphan-files.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('media_cleanup_jobs', [
            'mode' => MediaCleanupJob::MODE_DELETE,
            'status' => MediaCleanupJob::STATUS_QUEUED,
            'requested_by' => $super->id,
        ]);

        Bus::assertDispatched(ProcessMediaCleanupJob::class);
    }

    public function test_admin_forbidden_on_orphan_files_run(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.orphan-files.run'), []);

        $response->assertForbidden();
        Bus::assertNothingDispatched();
    }

    public function test_super_admin_can_cancel_active_job(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $job = MediaCleanupJob::query()->create([
            'status' => MediaCleanupJob::STATUS_RUNNING,
            'mode' => MediaCleanupJob::MODE_DRY_RUN,
            'requested_by' => $super->id,
            'progress_done' => 2,
            'progress_total' => 10,
        ]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->postJson(route('admin.orphan-files.cancel', $job->id));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', MediaCleanupJob::STATUS_CANCELLED);

        $this->assertDatabaseHas('media_cleanup_jobs', [
            'id' => $job->id,
            'status' => MediaCleanupJob::STATUS_CANCELLED,
        ]);
    }

    public function test_status_endpoint_returns_job_payload(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $job = MediaCleanupJob::query()->create([
            'status' => MediaCleanupJob::STATUS_QUEUED,
            'mode' => MediaCleanupJob::MODE_DRY_RUN,
            'requested_by' => $super->id,
        ]);

        $response = $this->actingAs($super)
            ->getJson(route('admin.orphan-files.status', $job->id));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $job->id)
            ->assertJsonPath('data.status', MediaCleanupJob::STATUS_QUEUED);
    }
}

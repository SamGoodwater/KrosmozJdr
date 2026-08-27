<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectClearJob;
use App\Models\ProjectConsoleJob;
use App\Models\User;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectConsoleJobTrackerTest extends TestCase
{
    public function test_try_queue_rejects_second_job_on_same_domain(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $tracker = app(ProjectConsoleJobTracker::class);

        $first = $tracker->tryQueue(ProjectConsoleDomain::CLEAR, 'project:clear --safe', $super->id);
        $second = $tracker->tryQueue(ProjectConsoleDomain::CLEAR, 'project:clear --safe', $super->id);

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertTrue(ProjectConsoleJob::hasActive(ProjectConsoleDomain::CLEAR));
    }

    public function test_run_artisan_records_filtered_output_and_progress(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $tracker = app(ProjectConsoleJobTracker::class);
        $record = $tracker->tryQueue(ProjectConsoleDomain::CLEAR, 'list', $super->id);
        $this->assertNotNull($record);

        $code = $tracker->runArtisan($record->id, 'list', []);

        $record->refresh();
        $this->assertSame(0, $code);
        $this->assertSame(ProjectConsoleJob::STATUS_SUCCESS, $record->status);
        $this->assertSame(100, $record->progress);
        $this->assertNotSame('', $record->output);
        $this->assertStringNotContainsString("\e[", $record->output);
    }

    public function test_clear_run_blocked_when_domain_busy(): void
    {
        Bus::fake();
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($super)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.project-clear.run'), ['mode' => 'safe'])
            ->assertRedirect(route('admin.project-clear.index'))
            ->assertSessionHas('success');

        $this->actingAs($super)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.project-clear.run'), ['mode' => 'safe'])
            ->assertRedirect(route('admin.project-clear.index'))
            ->assertSessionHas('error');

        Bus::assertDispatchedTimes(RunProjectClearJob::class, 1);
    }

    public function test_super_admin_can_poll_console_job_status(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $job = ProjectConsoleJob::query()->create([
            'domain' => ProjectConsoleDomain::REVIEW,
            'status' => ProjectConsoleJob::STATUS_RUNNING,
            'progress' => 40,
            'progress_label' => 'Tests backend',
            'command' => 'project:review --test_back',
            'output' => "Tests backend : php artisan test…\n",
            'triggered_by' => $super->id,
        ]);

        $this->actingAs($super)
            ->getJson(route('admin.console-jobs.show', $job->id))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.progress', 40)
            ->assertJsonPath('data.status', 'running');
    }

    public function test_admin_forbidden_on_console_job_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $job = ProjectConsoleJob::query()->create([
            'domain' => ProjectConsoleDomain::CLEAR,
            'status' => ProjectConsoleJob::STATUS_QUEUED,
            'command' => 'project:clear --safe',
            'output' => '',
            'triggered_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.console-jobs.show', $job->id))
            ->assertForbidden();
    }

    public function test_admin_can_open_content_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/Dashboard/Index')
                ->where('permissions.access.contentManagement', true));
    }

    public function test_game_master_has_content_management_access_key(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('permissions.access.contentManagement', true));
    }
}

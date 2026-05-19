<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectReviewJob;
use App\Models\ProjectScheduleTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Espace super-admin : planification BDD + reviews dev.
 */
class ProjectSuperConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_open_project_schedule_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('admin.project-schedule.index'))->assertForbidden();
    }

    public function test_super_admin_can_view_project_schedule_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.project-schedule.index'));
        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, ProjectScheduleTask::query()->count());
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/project-schedule/Index')
            ->has('tasks')
        );
    }

    public function test_super_admin_patch_invalid_cron_returns_validation_error(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $row = ProjectScheduleTask::query()->firstOrFail();

        $this->actingAs($super)
            ->patch(route('admin.project-schedule.tasks.update', $row), [
                'cron_expression' => 'not-a-cron',
            ])
            ->assertSessionHasErrors(['cron_expression']);
    }

    public function test_review_run_dispatches_job_for_super_admin(): void
    {
        Bus::fake();

        $super = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $session = [
            ...$this->passwordConfirmedSession(),
        ];

        $this->actingAs($super)
            ->withSession($session)
            ->post(route('admin.project-review.run'), [
                'run_all' => false,
                'test_back' => true,
                'tests' => false,
                'pint' => false,
            ])
            ->assertRedirect();

        Bus::assertDispatched(RunProjectReviewJob::class);
    }

    public function test_schedule_list_contains_thumbnail_command_when_seeded_or_legacy(): void
    {
        Artisan::call('schedule:list');
        $this->assertStringContainsString('media:clean-thumbnails', Artisan::output());
    }
}

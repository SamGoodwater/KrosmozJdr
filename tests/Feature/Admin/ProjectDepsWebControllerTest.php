<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectDepsJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectDepsWebControllerTest extends TestCase
{
    public function test_guest_redirects_from_project_update_index(): void
    {
        $response = $this->get(route('admin.project-update.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_forbidden_on_project_update_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.project-update.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_project_update_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.project-update.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/project-update/Index')
            ->where('isProduction', false));
    }

    public function test_super_admin_can_dispatch_deps_job_when_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-update.run'), [
                'all' => true,
            ]);

        $response->assertRedirect(route('admin.project-update.index'));
        $response->assertSessionHas('success');

        Bus::assertDispatched(function (RunProjectDepsJob $job) use ($super) {
            $ref = new \ReflectionClass($job);
            $uid = $ref->getProperty('triggeredByUserId');
            $uid->setAccessible(true);
            $opts = $ref->getProperty('artisanOptions');
            $opts->setAccessible(true);

            return (int) $uid->getValue($job) === $super->id
                && ($opts->getValue($job)['--all'] ?? false) === true;
        });
    }

    /**
     * La garde production est aussi dans {@see \App\Jobs\RunProjectDepsJob} (évite un run CLI si l’UI dérape).
     */
    public function test_run_project_deps_job_aborts_in_production_before_side_effects(): void
    {
        $this->app['env'] = 'production';
        $job = new RunProjectDepsJob(1, ['--all' => true]);
        $this->expectException(\RuntimeException::class);
        $job->handle();
    }

    public function test_empty_targets_returns_validation_error_for_deps_run(): void
    {
        Bus::fake();
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-update.run'), [
                'all' => false,
            ]);

        $response->assertRedirect(route('admin.project-update.index'));
        $response->assertSessionHas('error');
        Bus::assertNothingDispatched();
    }
}

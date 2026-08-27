<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectClearJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectClearWebControllerTest extends TestCase
{
    public function test_guest_redirects_from_clear_index(): void
    {
        $response = $this->get(route('admin.project-clear.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_forbidden_on_clear_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.project-clear.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_clear_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.project-clear.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Admin/project-clear/Index'));
    }

    public function test_super_admin_can_dispatch_clear_job_when_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-clear.run'), [
                'mode' => 'safe',
            ]);

        $response->assertRedirect(route('admin.project-clear.index'));
        $response->assertSessionHas('success');

        Bus::assertDispatched(function (RunProjectClearJob $job) use ($super) {
            $ref = new \ReflectionClass($job);
            $uid = $ref->getProperty('triggeredByUserId');
            $uid->setAccessible(true);
            $opts = $ref->getProperty('artisanOptions');
            $opts->setAccessible(true);

            return (int) $uid->getValue($job) === $super->id
                && ($opts->getValue($job)['--safe'] ?? false) === true;
        });
    }

    public function test_admin_forbidden_on_clear_run(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-clear.run'), [
                'mode' => 'safe',
            ]);

        $response->assertForbidden();
        Bus::assertNothingDispatched();
    }
}

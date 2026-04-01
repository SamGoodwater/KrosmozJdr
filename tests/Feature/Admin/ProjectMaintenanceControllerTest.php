<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectDataSyncJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectMaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirects_from_index(): void
    {
        $response = $this->get(route('admin.project-maintenance.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_forbidden_on_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.project-maintenance.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_index_without_full_page_password_redirect(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.project-maintenance.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/project-maintenance/Index')
            ->has('entityChoices')
            ->has('catalogTypeChoices'));
    }

    public function test_super_admin_can_dispatch_sync_job_with_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-maintenance.sync'), [
                'entities' => ['monster'],
                'catalog_types' => [],
                'races' => false,
                'lang' => 'fr',
                'noimage' => false,
                'skip_cache' => false,
                'dry_run' => true,
                'skip_clear_queue' => false,
                'skip_notify' => false,
            ]);

        $response->assertRedirect(route('admin.project-maintenance.index'));
        $response->assertSessionHas('success');

        Bus::assertDispatched(function (RunProjectDataSyncJob $job) use ($super) {
            $ref = new \ReflectionClass($job);
            $uid = $ref->getProperty('triggeredByUserId');
            $uid->setAccessible(true);
            $params = $ref->getProperty('artisanParameters');
            $params->setAccessible(true);

            return (int) $uid->getValue($job) === $super->id
                && ($params->getValue($job)['--entity'] ?? '') === 'monster'
                && ($params->getValue($job)['--dry-run'] ?? false) === true;
        });
    }

    public function test_admin_forbidden_on_sync(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-maintenance.sync'), [
                'lang' => 'fr',
            ]);

        $response->assertForbidden();
        Bus::assertNothingDispatched();
    }
}

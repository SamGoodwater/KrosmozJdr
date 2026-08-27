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

        $response->assertRedirect(route('admin.content.dofusdb.index'));
    }

    public function test_super_admin_is_redirected_from_legacy_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.project-maintenance.index'));

        $response->assertRedirect(route('admin.content.dofusdb.index'));
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

        $response->assertRedirect(route('admin.content.dofusdb.index'));
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

    public function test_admin_can_dispatch_sync_via_legacy_post(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.project-maintenance.sync'), [
                'lang' => 'fr',
            ]);

        $response->assertRedirect(route('admin.content.dofusdb.index'));
        Bus::assertDispatched(RunProjectDataSyncJob::class);
    }
}

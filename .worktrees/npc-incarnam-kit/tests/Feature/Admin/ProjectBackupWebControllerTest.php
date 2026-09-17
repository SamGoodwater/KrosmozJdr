<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectBackupJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectBackupWebControllerTest extends TestCase
{
    public function test_guest_redirects_from_backup_index(): void
    {
        $response = $this->get(route('admin.backup.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_forbidden_on_backup_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.backup.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_view_backup_index(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->get(route('admin.backup.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Admin/backup/Index'));
    }

    public function test_super_admin_can_dispatch_backup_job_when_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.backup.run'), [
                'no_storage' => true,
                'dry_run' => false,
            ]);

        $response->assertRedirect(route('admin.backup.index'));
        $response->assertSessionHas('success');

        Bus::assertDispatched(function (RunProjectBackupJob $job) use ($super) {
            $ref = new \ReflectionClass($job);
            $uid = $ref->getProperty('triggeredByUserId');
            $uid->setAccessible(true);
            $opts = $ref->getProperty('artisanOptions');
            $opts->setAccessible(true);

            return (int) $uid->getValue($job) === $super->id
                && ($opts->getValue($job)['--no-storage'] ?? false) === true;
        });
    }

    public function test_admin_forbidden_on_backup_run(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.backup.run'), []);

        $response->assertForbidden();
        Bus::assertNothingDispatched();
    }
}

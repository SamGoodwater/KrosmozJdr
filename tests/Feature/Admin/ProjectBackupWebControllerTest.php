<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectBackupJob;
use App\Models\User;
use App\Services\Project\ProjectBackupService;
use App\Support\Queue\ProjectQueues;
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
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/backup/Index')
            ->where('seederExportAvailable', true)
            ->has('backups')
            ->has('schedule')
            ->has('backupDirectory'));
    }

    public function test_super_admin_can_dispatch_backup_job_when_password_confirmed(): void
    {
        Bus::fake();

        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.backup.run'), [
                'no_storage' => true,
                'no_game' => true,
                'no_seeder_data' => true,
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
            $options = $opts->getValue($job);

            return (int) $uid->getValue($job) === $super->id
                && ($options['--no-storage'] ?? false) === true
                && ($options['--no-game'] ?? false) === true
                && ($options['--no-seeder-data'] ?? false) === true
                && $job->queue === ProjectQueues::BACKUP;
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

    public function test_super_admin_can_delete_backup(): void
    {
        $dir = storage_path('framework/testing/backup-web-del-'.uniqid('', true));
        mkdir($dir, 0700, true);
        config(['project-backup.path' => $dir]);

        $name = 'project-backup_2026-10-05_04-00-00_1111.zip';
        file_put_contents($dir.'/'.$name, 'PK');

        try {
            $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
            $response = $this->actingAs($super)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('admin.backup.delete'), ['name' => $name]);

            $response->assertRedirect(route('admin.backup.index'));
            $response->assertSessionHas('success');
            $this->assertFileDoesNotExist($dir.'/'.$name);
        } finally {
            foreach (glob($dir.'/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
        }
    }

    public function test_restore_requires_matching_confirm_name(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.backup.restore'), [
                'name' => 'project-backup_2026-10-05_04-00-00_1111.zip',
                'confirm_name' => 'wrong.zip',
            ]);

        $response->assertSessionHasErrors('confirm_name');
    }

    public function test_restore_status_endpoint(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($super)->getJson(route('admin.backup.restore-status'));

        $response->assertOk();
        $response->assertJsonStructure(['restoreStatus', 'operationLocked']);
    }

    public function test_path_outside_project_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ProjectBackupService::resolveAndAssertBackupPath('/tmp/outside-krosmoz-backups');
    }
}

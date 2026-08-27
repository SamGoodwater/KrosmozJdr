<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunProjectDataSyncJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ContentDofusdbWorkshopControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirects_from_workshop(): void
    {
        $this->get(route('admin.content.dofusdb.index'))
            ->assertRedirect(route('login'));
    }

    public function test_game_master_forbidden_from_workshop(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.dofusdb.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_workshop(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.dofusdb.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/DofusdbWorkshop/Index')
                ->has('entityChoices')
                ->has('catalogTypeChoices'));
    }

    public function test_scrapping_url_redirects_to_workshop(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('scrapping.index'))
            ->assertRedirect(route('admin.content.dofusdb.index'));
    }

    public function test_project_maintenance_url_redirects_to_workshop(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($super)
            ->get(route('admin.project-maintenance.index'))
            ->assertRedirect(route('admin.content.dofusdb.index'));
    }

    public function test_admin_can_dispatch_auto_update_sync(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.content.dofusdb.sync'), [
                'entities' => ['monster'],
                'catalog_types' => [],
                'races' => false,
                'lang' => 'fr',
                'noimage' => false,
                'skip_cache' => false,
                'dry_run' => true,
                'skip_clear_queue' => false,
                'skip_notify' => false,
            ])
            ->assertRedirect(route('admin.content.dofusdb.index'))
            ->assertSessionHas('success');

        Bus::assertDispatched(RunProjectDataSyncJob::class);
    }

    public function test_game_master_forbidden_from_sync(): void
    {
        Bus::fake();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('admin.content.dofusdb.sync'), ['lang' => 'fr'])
            ->assertForbidden();

        Bus::assertNothingDispatched();
    }
}

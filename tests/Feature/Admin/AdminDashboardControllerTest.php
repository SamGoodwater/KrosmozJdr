<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirects_from_admin_root(): void
    {
        $this->get(route('admin.dashboard.index'))->assertRedirect(route('login'));
    }

    public function test_game_master_forbidden_from_admin_root(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.dashboard.index'))
            ->assertForbidden();
    }

    public function test_game_master_forbidden_from_content_dashboard(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.dashboard.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_content_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Content/Dashboard/Index'));
    }

    public function test_game_master_forbidden_from_admin_recap(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->withSession(['auth.password_confirmed_at' => time(), 'auth.password_last_activity_at' => time()])
            ->get(route('admin.recap.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_recap_with_password_confirmed(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time(), 'auth.password_last_activity_at' => time()])
            ->get(route('admin.recap.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Recap/Index')
                ->where('commands', []));
    }

    public function test_super_admin_recap_lists_command_guide_links(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($super)
            ->withSession(['auth.password_confirmed_at' => time(), 'auth.password_last_activity_at' => time()])
            ->get(route('admin.recap.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Recap/Index')
                ->has('commands')
                ->where('commands.0.signature', 'project:deps')
                ->where('commands.0.admin', '/admin/project-update'));
    }
}

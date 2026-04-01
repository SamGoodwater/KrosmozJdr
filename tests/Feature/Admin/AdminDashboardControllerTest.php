<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirects_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_player_forbidden_from_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $response = $this->actingAs($user)->get(route('admin.dashboard.index'));

        $response->assertForbidden();
    }

    public function test_game_master_can_view_admin_dashboard(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $response = $this->actingAs($gm)->get(route('admin.dashboard.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard/Index'));
    }
}

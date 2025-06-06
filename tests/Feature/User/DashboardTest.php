<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_accessible_by_authenticated_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) =>
            $page->component('Organisms/User/Dashboard')
                ->where('user.id', $user->id)
        );
    }

    public function test_dashboard_redirects_guest()
    {
        $response = $this->get('/user');
        $response->assertRedirect('/login');
    }
}

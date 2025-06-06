<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_routes()
    {
        $protectedRoutes = [
            '/user',
            '/user/edit',
            '/user/avatar',
        ];
        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_user_with_insufficient_role_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => User::roleValue('user')]);
        $adminRoutes = [
            '/user/create',
            '/user/1/edit',
            '/user/1/role',
        ];
        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertForbidden();
        }
    }

    public function test_csrf_protection_on_sensitive_routes()
    {
        $this->withoutMiddleware([
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\CheckRole::class,
        ]);
        $response = $this->post('/user/avatar', [
            'avatar' => 'fake',
        ], ['X-CSRF-TOKEN' => '']);
        $response->assertStatus(419); // 419 = CSRF token mismatch
    }
}

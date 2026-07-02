<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Zone sensible — ré-verrouillage après inactivité (middleware password.confirm).
 */
class RequirePasswordWithInactivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_recap_redirects_without_password_confirmation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.recap.index'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_inertia_visit_redirects_to_password_confirm_instead_of_json(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->withHeaders([
                'X-Inertia' => 'true',
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'text/html, application/xhtml+xml',
            ])
            ->get(route('admin.recap.index'));

        $response->assertRedirect(route('password.confirm'));
        $this->assertStringNotContainsString(
            'application/json',
            (string) $response->headers->get('Content-Type')
        );
    }

    public function test_json_api_returns_423_without_inertia_header(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('admin.recap.index'))
            ->assertStatus(423)
            ->assertJson(['message' => 'Password confirmation required.']);
    }

    public function test_admin_recap_redirects_after_inactivity_timeout(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $stale = time() - 7200;

        $this->actingAs($admin)
            ->withSession([
                'auth.password_confirmed_at' => $stale,
                'auth.password_last_activity_at' => $stale,
            ])
            ->get(route('admin.recap.index'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_admin_recap_accessible_with_recent_password_confirmation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->get(route('admin.recap.index'))
            ->assertOk();
    }

    public function test_protected_route_extends_activity_window(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $confirmedAt = time() - 7200;

        $session = [
            'auth.password_confirmed_at' => $confirmedAt,
            'auth.password_last_activity_at' => time() - 30,
        ];

        $this->actingAs($admin)
            ->withSession($session)
            ->get(route('admin.recap.index'))
            ->assertOk();

        $lastActivity = (int) session('auth.password_last_activity_at');
        $this->assertGreaterThan($confirmedAt, $lastActivity);
    }

    public function test_password_confirm_store_unlocks_sensitive_area(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => bcrypt('secret-password'),
        ]);

        $this->actingAs($admin)
            ->post(route('password.confirm.store'), ['password' => 'secret-password'])
            ->assertRedirect('/');

        $this->assertTrue(session()->has('auth.password_confirmed_at'));

        $this->actingAs($admin)
            ->get(route('admin.recap.index'))
            ->assertOk();
    }

    public function test_password_confirm_store_returns_validation_error_on_bad_password(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => bcrypt('secret-password'),
        ]);

        $this->actingAs($admin)
            ->post(route('password.confirm.store'), ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');
    }
}

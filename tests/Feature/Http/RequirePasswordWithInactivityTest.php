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
}

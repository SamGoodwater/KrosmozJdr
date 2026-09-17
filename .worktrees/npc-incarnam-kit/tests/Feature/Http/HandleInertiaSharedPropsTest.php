<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Props Inertia partagées par HandleInertiaRequests (shareOnce, defer, auth).
 */
class HandleInertiaSharedPropsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_inertia_page_includes_share_once_and_auth_props(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('permissions')
            ->has('ziggy')
            ->has('oauth_enabled_providers')
            ->has('auth')
            ->where('auth.isLogged', false)
            ->missing('characteristics')
        );
    }

    public function test_authenticated_inertia_page_includes_permissions_and_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.show'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('permissions')
            ->has('ziggy')
            ->has('auth.user')
            ->where('auth.isLogged', true)
            ->where('auth.user.id', $user->id)
        );
    }

    public function test_partial_reload_still_includes_auth_props(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.show', $user));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->reloadOnly('user', fn ($partial) => $partial
                ->has('auth.user')
                ->where('auth.isLogged', true)
                ->where('auth.user.id', $user->id)
            )
        );
    }
}

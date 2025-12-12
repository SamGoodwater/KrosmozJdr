<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature : redirections "intended" (routes protégées -> login/register -> retour).
 *
 * @description
 * Vérifie qu'un invité est redirigé vers la page de login lorsqu'il accède à une route protégée,
 * et qu'après authentification il est renvoyé vers l'URL initialement demandée.
 *
 * @example
 * GET /user/edit (guest) -> /login
 * POST /login -> /user/edit
 */
class IntendedRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_then_back_to_intended_url_after_login(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->get(route('user.edit'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('url.intended');

        $this->assertStringEndsWith('/user/edit', (string) session('url.intended'));

        $loginResponse = $this->post(route('login'), [
            'identifier' => $user->email,
            'password' => 'password',
            'remember' => false,
        ]);

        $loginResponse->assertRedirect();
        $this->assertStringEndsWith('/user/edit', (string) $loginResponse->headers->get('Location'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inertia_guest_request_gets_inertia_location_to_login_and_keeps_intended_url(): void
    {
        User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->withHeader('X-Inertia', 'true')
            ->get(route('user.edit'));

        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', route('login', absolute: false));
        $response->assertSessionHas('url.intended');
        $this->assertStringEndsWith('/user/edit', (string) session('url.intended'));
    }
}



<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature : login avec compte supprimé (SoftDeletes).
 *
 * @description
 * Un utilisateur soft-deleted n'est pas résolu par `Auth::attempt` (global scope),
 * ce qui ressemble à des "identifiants incorrects" alors que le mot de passe est bon.
 * On vérifie que l'erreur retournée est explicite.
 *
 * @example
 * user->delete() puis POST /login -> erreur "Ce compte a été supprimé..."
 */
class LoginDeletedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_with_explicit_message_when_account_is_soft_deleted(): void
    {
        $user = User::factory()->create([
            'email' => 'deleted@test.fr',
            'password' => 'password',
            'role' => User::ROLE_USER,
        ]);

        $user->delete();

        $response = $this->from(route('login'))->post(route('login'), [
            'identifier' => 'deleted@test.fr',
            'password' => 'password',
            'remember' => false,
        ]);

        $response->assertSessionHasErrors([
            'identifier' => 'Ce compte a été supprimé. Contactez un administrateur pour le restaurer.',
        ]);
        $this->assertGuest();
    }
}



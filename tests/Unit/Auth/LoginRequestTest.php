<?php

namespace Tests\Unit\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de validation avec des données valides.
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test de validation échouée sans identifiant.
     */
    public function test_validation_fails_without_identifier(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'password' => 'password123',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('identifier', $validator->errors()->toArray());
    }

    /**
     * Test de validation échouée sans mot de passe.
     */
    public function test_validation_fails_without_password(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
        ]);

        $rules = $request->rules();
        $validator = validator($request->all(), $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test d'authentification réussie avec email.
     */
    public function test_authenticate_succeeds_with_email(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password',
        ]);

        $request->authenticate();

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test d'authentification réussie avec nom d'utilisateur.
     */
    public function test_authenticate_succeeds_with_username(): void
    {
        $user = User::factory()->create(['name' => 'testuser']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'testuser',
            'password' => 'password',
        ]);

        $request->authenticate();

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test d'authentification échouée avec des identifiants invalides.
     */
    public function test_authenticate_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->expectException(ValidationException::class);
        $request->authenticate();
    }

    /**
     * Test d'authentification échouée avec un email inexistant.
     */
    public function test_authenticate_fails_with_nonexistent_email(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->expectException(ValidationException::class);
        $request->authenticate();
    }

    /**
     * Test d'authentification échouée avec un nom d'utilisateur inexistant.
     */
    public function test_authenticate_fails_with_nonexistent_username(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'nonexistent_user',
            'password' => 'password',
        ]);

        $this->expectException(ValidationException::class);
        $request->authenticate();
    }

    /**
     * Test d'authentification avec "se souvenir de moi".
     */
    public function test_authenticate_with_remember_me(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $request->authenticate();

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /**
     * Test d'authentification sans "se souvenir de moi".
     */
    public function test_authenticate_without_remember_me(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password',
            'remember' => false,
        ]);

        $request->authenticate();

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test de limitation de taux sur l'authentification échouée.
     */
    public function test_rate_limiting_on_failed_authentication(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Première tentative échouée
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            // Attendu
        }

        $this->assertEquals(1, RateLimiter::attempts($request->throttleKey()));

        // Deuxième tentative échouée
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            // Attendu
        }

        $this->assertEquals(2, RateLimiter::attempts($request->throttleKey()));
    }

    /**
     * Test de nettoyage du rate limiting après authentification réussie.
     */
    public function test_rate_limiting_cleared_after_successful_authentication(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Quelques tentatives échouées
        for ($i = 0; $i < 3; $i++) {
            try {
                $request->authenticate();
            } catch (ValidationException $e) {
                // Attendu
            }
        }

        $this->assertEquals(3, RateLimiter::attempts($request->throttleKey()));

        // Authentification réussie
        $request->merge(['password' => 'password']);
        $request->authenticate();

        $this->assertEquals(0, RateLimiter::attempts($request->throttleKey()));
    }

    /**
     * Test de génération de la clé de throttling.
     */
    public function test_throttle_key_generation(): void
    {
        $request = new LoginRequest();
        $request->merge(['identifier' => 'test@example.com']);
        
        // Simuler une IP en modifiant directement la requête
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $key = $request->throttleKey();
        $expectedKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower('test@example.com').'|127.0.0.1');

        $this->assertEquals($expectedKey, $key);
    }

    /**
     * Test d'authentification échouée avec un utilisateur supprimé.
     */
    public function test_authenticate_fails_with_deleted_user(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $user->delete();

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->expectException(ValidationException::class);
        $request->authenticate();
    }

    /**
     * Test d'authentification avec email insensible à la casse.
     */
    public function test_authenticate_with_case_insensitive_email(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
        ]);

        $request->authenticate();

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test d'authentification avec différents formats d'identifiants.
     */
    public function test_authenticate_with_various_identifier_formats(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'testuser'
        ]);

        // Test avec email
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'test@example.com',
            'password' => 'password',
        ]);

        $request->authenticate();
        $this->assertAuthenticatedAs($user);

        Auth::logout();

        // Test avec nom d'utilisateur
        $request = new LoginRequest();
        $request->merge([
            'identifier' => 'testuser',
            'password' => 'password',
        ]);

        $request->authenticate();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test que la méthode authorize retourne true.
     */
    public function test_authorize_method_returns_true(): void
    {
        $request = new LoginRequest();
        $this->assertTrue($request->authorize());
    }
} 
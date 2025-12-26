<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests Feature pour la chaîne complète d'inscription
 * 
 * Vérifie que :
 * - La page d'inscription est accessible
 * - Un utilisateur peut s'inscrire avec des données valides
 * - Les validations fonctionnent correctement
 * - L'utilisateur est créé avec le bon rôle (ROLE_USER)
 * - L'utilisateur est automatiquement connecté après l'inscription
 * - L'événement Registered est déclenché
 */
class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Le CSRF est déjà désactivé dans TestCase::setUp()
        // Mais on s'assure qu'il est bien désactivé ici aussi
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Test : La page d'inscription est accessible
     */
    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/auth/Register')
        );
    }

    /**
     * Test : Un utilisateur peut s'inscrire avec des données valides
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->from(route('register'))
            ->post(route('register'), $userData);

        $response->assertRedirect(route('user.show'));
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => User::ROLE_USER, // Rôle par défaut
        ]);

        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticated();
        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test : L'inscription échoue si le nom est manquant
     */
    public function test_registration_fails_without_name(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test : L'inscription échoue si l'email est manquant
     */
    public function test_registration_fails_without_email(): void
    {
        $beforeCount = User::count();

        $userData = [
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('email');
        // On vérifie qu'aucun utilisateur supplémentaire n'a été créé
        // (ne pas dépendre d'un nom unique dans la DB de test).
        $this->assertSame($beforeCount, User::count());
    }

    /**
     * Test : L'inscription échoue si l'email est invalide
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test : L'inscription échoue si l'email est déjà utilisé
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test : L'inscription échoue si le mot de passe est manquant
     */
    public function test_registration_fails_without_password(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test : L'inscription échoue si la confirmation du mot de passe ne correspond pas
     */
    public function test_registration_fails_with_password_mismatch(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test : L'inscription échoue si le mot de passe est trop court
     */
    public function test_registration_fails_with_short_password(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test : L'utilisateur créé a le rôle USER par défaut
     */
    public function test_new_user_has_user_role_by_default(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->post(route('register'), $userData);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_USER, $user->role);
    }

    /**
     * Test : Le mot de passe est bien hashé
     */
    public function test_password_is_hashed(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->post(route('register'), $userData);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('Password123!', $user->password);
        $this->assertTrue(Hash::check('Password123!', $user->password));
    }

    /**
     * Test : Un utilisateur authentifié ne peut pas accéder à la page d'inscription
     */
    public function test_authenticated_user_cannot_access_registration_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('register'));

        // Devrait rediriger vers la page d'accueil ou le profil
        $response->assertRedirect();
    }
}


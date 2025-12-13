<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use App\Models\Page;
use App\Enums\Visibility;
use App\Enums\PageState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de validation pour StorePageRequest
 * 
 * Vérifie que les règles de validation sont correctement appliquées :
 * - Titre obligatoire (max 255)
 * - Slug obligatoire, unique, format correct
 * - Enums valides (is_visible, can_edit_role, state)
 */
class StorePageRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Le titre est obligatoire
     */
    public function test_title_required(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'slug' => 'test-page',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
            // title manquant
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /**
     * Le titre ne peut pas dépasser 255 caractères
     */
    public function test_title_max_length(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => str_repeat('a', 256),
            'slug' => 'test-page',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /**
     * Le slug est généré automatiquement depuis le titre si absent
     */
    public function test_slug_auto_generated_from_title(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page Auto Slug',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
            // slug manquant = sera généré
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pages', [
            'title' => 'Test Page Auto Slug',
            'slug' => 'test-page-auto-slug', // Slug généré automatiquement
        ]);
    }

    /**
     * Le slug doit être unique
     */
    public function test_slug_unique(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        
        // Créer une page existante
        Page::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'existing-slug', // Slug déjà utilisé
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');
    }

    /**
     * Le slug doit respecter le format (kebab-case, lettres minuscules, chiffres, tirets)
     */
    public function test_slug_format(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        // Test avec espaces
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'invalid slug',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');

        // Test avec majuscules
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'InvalidSlug',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');

        // Test avec underscore (invalide selon la règle)
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'invalid_slug',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');
    }

    /**
     * is_visible doit être une valeur valide de l'enum Visibility
     */
    public function test_is_visible_enum(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'is_visible' => 999, // Valeur invalide
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('is_visible');
    }

    /**
     * can_edit_role doit être une valeur valide de l'enum Visibility
     */
    public function test_can_edit_role_enum(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => 999, // Valeur invalide
            'state' => PageState::DRAFT->value,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('can_edit_role');
    }

    /**
     * state doit être une valeur valide de l'enum PageState
     */
    public function test_state_enum(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => 'invalid-state', // Valeur invalide
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('state');
    }

    /**
     * Une requête valide crée la page avec succès
     */
    public function test_valid_request_creates_page(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Valid Page',
            'slug' => 'valid-page',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'state' => PageState::DRAFT->value,
            'in_menu' => true,
            'menu_order' => 0,
        ]);

        $response->assertStatus(302); // Redirect après création
        $this->assertDatabaseHas('pages', [
            'slug' => 'valid-page',
            'title' => 'Valid Page',
        ]);
    }
}


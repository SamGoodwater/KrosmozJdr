<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de validation pour StorePageRequest
 * 
 * Vérifie que les règles de validation sont correctement appliquées :
 * - Titre obligatoire (max 255)
 * - Slug obligatoire, unique, format correct
 * - Valeurs valides (read_level, write_level, state)
 */
class StorePageRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Le titre est obligatoire
     */
    public function test_title_required(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'slug' => 'test-page',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
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
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => str_repeat('a', 256),
            'slug' => 'test-page',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /**
     * Le slug est généré automatiquement depuis le titre si absent
     */
    public function test_slug_auto_generated_from_title(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page Auto Slug',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
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
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        
        // Créer une page existante
        Page::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'existing-slug', // Slug déjà utilisé
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');
    }

    /**
     * Le slug doit respecter le format (kebab-case, lettres minuscules, chiffres, tirets)
     */
    public function test_slug_format(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Test avec espaces
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'invalid slug',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');

        // Test avec majuscules
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'InvalidSlug',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');

        // Test avec underscore (invalide selon la règle)
        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'invalid_slug',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');
    }

    public function test_read_level_invalid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'read_level' => 999,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('read_level');
    }

    public function test_write_level_must_be_gte_read_level(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'read_level' => User::ROLE_ADMIN,
            'write_level' => User::ROLE_GAME_MASTER,
            'state' => Page::STATE_DRAFT,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('write_level');
    }

    /**
     * state doit être une valeur valide de l'enum PageState
     */
    public function test_state_enum(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
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
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Valid Page',
            'slug' => 'valid-page',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Page::STATE_DRAFT,
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


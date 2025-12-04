<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Page;
use App\Enums\PageState;
use App\Enums\Visibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour PageController
 * 
 * Vérifie que :
 * - Les admins peuvent créer des pages
 * - Les utilisateurs peuvent voir les pages selon leur visibilité
 * - Les utilisateurs peuvent modifier les pages selon can_edit_role
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        
        // Désactiver les notifications dans les tests pour éviter les erreurs
        \Illuminate\Support\Facades\Notification::fake();
    }

    /**
     * Test : Un admin peut créer une page
     */
    public function test_admin_can_create_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('pages.store'), [
                'title' => 'Page de test',
                'slug' => 'page-de-test',
                'is_visible' => Visibility::GUEST->value,
                'can_edit_role' => Visibility::ADMIN->value,
                'in_menu' => true,
                'state' => PageState::DRAFT->value,
                'menu_order' => 0,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'title' => 'Page de test',
            'slug' => 'page-de-test',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas créer une page
     */
    public function test_user_cannot_create_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'Page de test',
                'slug' => 'page-de-test',
                'is_visible' => Visibility::GUEST->value,
                'can_edit_role' => Visibility::ADMIN->value,
                'in_menu' => true,
                'state' => PageState::DRAFT->value,
                'menu_order' => 0,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : Un utilisateur peut voir une page publiée avec visibilité guest
     */
    public function test_user_can_view_published_page_with_guest_visibility(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $page = Page::factory()->create([
            'state' => PageState::PUBLISHED->value,
            'is_visible' => Visibility::GUEST->value,
        ]);

        $response = $this->actingAs($user)
            ->get(route('pages.show', $page->slug));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/page/Show')
            ->has('page')
        );
    }

    /**
     * Test : Un utilisateur peut modifier une page qu'il a créée
     */
    public function test_user_can_update_own_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'title' => 'Page originale',
            'created_by' => $user->id,
            'can_edit_role' => Visibility::ADMIN->value,
        ]);

        $response = $this->actingAs($user)
            ->from(route('pages.edit', $page))
            ->patch(route('pages.update', $page), [
                'title' => 'Page modifiée',
                'slug' => $page->slug,
                'is_visible' => $page->is_visible instanceof \App\Enums\Visibility ? $page->is_visible->value : $page->is_visible,
                'can_edit_role' => $page->can_edit_role instanceof \App\Enums\Visibility ? $page->can_edit_role->value : $page->can_edit_role,
                'in_menu' => $page->in_menu,
                'state' => $page->state instanceof \App\Enums\PageState ? $page->state->value : $page->state,
                'menu_order' => $page->menu_order,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Page modifiée',
        ]);
    }

    /**
     * Test : Un game_master peut modifier une page si can_edit_role le permet
     */
    public function test_game_master_can_update_page_if_can_edit_role_allows(): void
    {
        $gameMaster = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $page = Page::factory()->create([
            'can_edit_role' => Visibility::GAME_MASTER->value,
        ]);

        $response = $this->actingAs($gameMaster)
            ->from(route('pages.edit', $page))
            ->patch(route('pages.update', $page), [
                'title' => 'Page modifiée par GM',
                'slug' => $page->slug,
                'is_visible' => $page->is_visible instanceof \App\Enums\Visibility ? $page->is_visible->value : $page->is_visible,
                'can_edit_role' => $page->can_edit_role instanceof \App\Enums\Visibility ? $page->can_edit_role->value : $page->can_edit_role,
                'in_menu' => $page->in_menu,
                'state' => $page->state instanceof \App\Enums\PageState ? $page->state->value : $page->state,
                'menu_order' => $page->menu_order,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Page modifiée par GM',
        ]);
    }

    /**
     * Test : Un game_master ne peut pas modifier une page si can_edit_role est admin
     */
    public function test_game_master_cannot_update_page_if_can_edit_role_is_admin(): void
    {
        $gameMaster = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $page = Page::factory()->create([
            'can_edit_role' => Visibility::ADMIN->value,
            'created_by' => User::factory()->create()->id, // Créée par un autre utilisateur
        ]);

        $response = $this->actingAs($gameMaster)
            ->patch(route('pages.update', $page), [
                'title' => 'Page modifiée par GM',
                'slug' => $page->slug,
                'is_visible' => $page->is_visible instanceof \App\Enums\Visibility ? $page->is_visible->value : $page->is_visible,
                'can_edit_role' => $page->can_edit_role instanceof \App\Enums\Visibility ? $page->can_edit_role->value : $page->can_edit_role,
                'in_menu' => $page->in_menu,
                'state' => $page->state instanceof \App\Enums\PageState ? $page->state->value : $page->state,
                'menu_order' => $page->menu_order,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test : Un admin peut supprimer une page
     */
    public function test_admin_can_delete_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('pages.delete', $page));

        $response->assertRedirect();
        $this->assertSoftDeleted('pages', [
            'id' => $page->id,
        ]);
    }

    /**
     * Test : La validation échoue si le titre est manquant
     */
    public function test_validation_fails_if_title_is_missing(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('pages.store'), [
                'slug' => 'page-de-test',
                'is_visible' => Visibility::GUEST->value,
                'can_edit_role' => Visibility::ADMIN->value,
                'in_menu' => true,
                'state' => PageState::DRAFT->value,
                'menu_order' => 0,
            ]);

        $response->assertSessionHasErrors('title');
    }

    /**
     * Test : La validation échoue si le slug est dupliqué
     */
    public function test_validation_fails_if_slug_is_duplicate(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $existingPage = Page::factory()->create(['slug' => 'page-existante']);

        $response = $this->actingAs($admin)
            ->post(route('pages.store'), [
                'title' => 'Nouvelle page',
                'slug' => 'page-existante', // Slug déjà utilisé
                'is_visible' => Visibility::GUEST->value,
                'can_edit_role' => Visibility::ADMIN->value,
                'in_menu' => true,
                'state' => PageState::DRAFT->value,
                'menu_order' => 0,
            ]);

        $response->assertSessionHasErrors('slug');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas créer une page
     */
    public function test_guest_cannot_create_page(): void
    {
        $response = $this->post(route('pages.store'), [
            'title' => 'Page de test',
            'slug' => 'page-de-test',
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'in_menu' => true,
            'state' => PageState::DRAFT->value,
            'menu_order' => 0,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page index charge les pages
     */
    public function test_index_page_loads_pages(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->get(route('pages.index'));

        if ($response->status() !== 200) {
            dump($response->getContent());
        }

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/page/Index')
            ->has('pages.data', 5)
        );
    }

    /**
     * Test : Un super_admin peut toujours modifier n'importe quelle page
     */
    public function test_super_admin_can_always_update_any_page(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $otherUser = User::factory()->create();
        $page = Page::factory()->create([
            'created_by' => $otherUser->id,
            'can_edit_role' => Visibility::ADMIN->value,
        ]);

        $response = $this->actingAs($superAdmin)
            ->from(route('pages.edit', $page))
            ->patch(route('pages.update', $page), [
                'title' => 'Page modifiée par super admin',
                'slug' => $page->slug,
                'is_visible' => $page->is_visible instanceof \App\Enums\Visibility ? $page->is_visible->value : $page->is_visible,
                'can_edit_role' => $page->can_edit_role instanceof \App\Enums\Visibility ? $page->can_edit_role->value : $page->can_edit_role,
                'in_menu' => $page->in_menu,
                'state' => $page->state instanceof \App\Enums\PageState ? $page->state->value : $page->state,
                'menu_order' => $page->menu_order,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Page modifiée par super admin',
        ]);
    }
}


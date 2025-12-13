<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Models\Page;
use App\Enums\Visibility;
use App\Enums\PageState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de la PagePolicy (Autorisation)
 * 
 * Vérifie que les règles d'accès aux pages sont correctement appliquées :
 * - Visibilité (guest, user, game_master, admin)
 * - Création (admin uniquement)
 * - Modification (auteur, admin, users associés)
 * - Suppression (auteur, admin)
 */
class PagePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un invité (non connecté) peut voir une page publique (is_visible=guest)
     */
    public function test_guest_can_view_public_page(): void
    {
        $page = Page::factory()->create([
            'is_visible' => Visibility::GUEST->value,
            'state' => PageState::PUBLISHED->value,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->view(null, $page)
        );
    }

    /**
     * Un invité ne peut PAS voir une page admin (is_visible=admin)
     */
    public function test_guest_cannot_view_admin_page(): void
    {
        $page = Page::factory()->create([
            'is_visible' => Visibility::ADMIN->value,
            'state' => PageState::PUBLISHED->value,
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->view(null, $page)
        );
    }

    /**
     * Un user simple ne peut PAS voir une page game_master
     */
    public function test_user_cannot_view_game_master_page(): void
    {
        $user = User::factory()->create(['role' => 1]); // user
        $page = Page::factory()->create([
            'is_visible' => Visibility::GAME_MASTER->value,
            'state' => PageState::PUBLISHED->value,
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->view($user, $page)
        );
    }

    /**
     * Un admin peut créer une page
     */
    public function test_admin_can_create_page(): void
    {
        $admin = User::factory()->create(['role' => 4]); // admin

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->create($admin)
        );
    }

    /**
     * Un game_master ne peut PAS créer de page (réservé aux admins)
     */
    public function test_game_master_cannot_create_page(): void
    {
        $gm = User::factory()->create(['role' => 3]); // game_master

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->create($gm)
        );
    }

    /**
     * Un user simple ne peut PAS créer de page
     */
    public function test_user_cannot_create_page(): void
    {
        $user = User::factory()->create(['role' => 1]); // user

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->create($user)
        );
    }

    /**
     * L'auteur d'une page peut la modifier
     */
    public function test_author_can_update_own_page(): void
    {
        $author = User::factory()->create(['role' => 3]); // game_master
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'can_edit_role' => Visibility::GAME_MASTER->value,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->update($author, $page)
        );
    }

    /**
     * Un user ne peut PAS modifier la page d'un autre
     */
    public function test_user_cannot_update_others_page(): void
    {
        $author = User::factory()->create(['role' => 3]);
        $other = User::factory()->create(['role' => 3]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'can_edit_role' => Visibility::ADMIN->value, // Nécessite admin
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->update($other, $page)
        );
    }

    /**
     * Un admin peut modifier n'importe quelle page
     */
    public function test_admin_can_update_any_page(): void
    {
        $admin = User::factory()->create(['role' => 4]); // admin
        $author = User::factory()->create(['role' => 3]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'can_edit_role' => Visibility::ADMIN->value,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->update($admin, $page)
        );
    }

    /**
     * L'auteur peut supprimer sa propre page
     */
    public function test_author_can_delete_own_page(): void
    {
        $author = User::factory()->create(['role' => 3]); // game_master
        $page = Page::factory()->create([
            'created_by' => $author->id,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->delete($author, $page)
        );
    }

    /**
     * Un user ne peut PAS supprimer la page d'un autre
     */
    public function test_user_cannot_delete_others_page(): void
    {
        $author = User::factory()->create(['role' => 3]);
        $other = User::factory()->create(['role' => 3]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->delete($other, $page)
        );
    }

    /**
     * Un admin peut supprimer n'importe quelle page
     */
    public function test_admin_can_delete_any_page(): void
    {
        $admin = User::factory()->create(['role' => 4]); // admin
        $author = User::factory()->create(['role' => 3]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->delete($admin, $page)
        );
    }

    /**
     * Un admin peut forceDelete une page
     */
    public function test_admin_can_force_delete_page(): void
    {
        $admin = User::factory()->create(['role' => 4]); // admin
        $user = User::factory()->create(['role' => 1]); // user
        
        $page = Page::factory()->create();

        $this->assertFalse(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->forceDelete($user, $page)
        );

        $this->assertTrue(
            $this->app->make(\App\Policies\PagePolicy::class)
                ->forceDelete($admin, $page)
        );
    }
}


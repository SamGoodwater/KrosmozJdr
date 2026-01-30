<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Models\Page;
use App\Models\Section;
use App\Enums\SectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de la SectionPolicy (Autorisation)
 * 
 * Vérifie que les règles d'accès aux sections sont correctement appliquées :
 * - Création : nécessite droit 'update' sur la page parente
 * - Modification : respecte write_level de la section ET de la page
 * - Suppression : nécessite droit 'update' sur la page parente
 */
class SectionPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Créer une section nécessite le droit 'update' sur la page parente
     */
    public function test_create_section_requires_page_update_permission(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $page = Page::factory()->create([
            'created_by' => $user->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->create($user, $page)
        );
    }

    /**
     * Un user sans droit sur la page ne peut PAS créer de section
     */
    public function test_user_cannot_create_section_without_page_permission(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_ADMIN, // Nécessite admin
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->create($user, $page)
        );
    }

    /**
     * Un user connecté mais sans droit ne peut pas créer de section
     */
    public function test_user_without_permission_cannot_create_section(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $page = Page::factory()->create([
            'write_level' => User::ROLE_ADMIN, // Nécessite admin
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->create($user, $page)
        );
    }

    /**
     * L'auteur d'une section peut la modifier
     */
    public function test_author_can_update_own_section(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->update($author, $section)
        );
    }

    /**
     * Un user ne peut PAS modifier une section sans droit sur la page parente
     */
    public function test_user_cannot_update_section_without_page_permission(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_ADMIN, // Nécessite admin
        ]);
        
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->update($user, $section)
        );
    }

    /**
     * Supprimer une section nécessite le droit 'update' sur la page parente
     */
    public function test_delete_section_requires_page_update_permission(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->delete($author, $section)
        );
    }

    /**
     * Un user sans droit sur la page ne peut PAS supprimer de section
     */
    public function test_user_cannot_delete_section_without_page_permission(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
        ]);

        $this->assertFalse(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->delete($user, $section)
        );
    }

    /**
     * Un admin peut modifier n'importe quelle section
     */
    public function test_admin_can_update_any_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        
        $page = Page::factory()->create([
            'created_by' => $author->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $this->assertTrue(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->update($admin, $section)
        );
    }

    /**
     * Un admin peut forceDelete une section
     */
    public function test_admin_can_force_delete_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        
        $page = Page::factory()->create();
        $section = Section::factory()->create(['page_id' => $page->id]);

        $this->assertFalse(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->forceDelete($user, $section)
        );

        $this->assertTrue(
            $this->app->make(\App\Policies\SectionPolicy::class)
                ->forceDelete($admin, $section)
        );
    }
}


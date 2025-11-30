<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ItemController
 * 
 * Vérifie que :
 * - Un utilisateur peut modifier un item qu'il a créé
 * - Un admin peut modifier n'importe quel item
 * - La méthode updateResources synchronise correctement les ressources avec quantités
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        // S'assurer que la session est configurée
        $this->withSession(['_token' => 'test-token']);
    }

    /**
     * Test : Un admin peut modifier un item
     */
    public function test_admin_can_update_item(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Item Test',
            'description' => 'Description originale',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.update', $item), [
                '_method' => 'PATCH',
                'name' => 'Item Modifié',
                'description' => 'Nouvelle description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Item Modifié',
            'description' => 'Nouvelle description',
        ]);
    }

    /**
     * Test : Un admin peut ajouter des ressources à un item avec quantités
     */
    public function test_admin_can_add_resources_to_item_with_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 5],
                    $resource2->id => ['quantity' => 10],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $item->fresh()->resources);
        $this->assertEquals(5, $item->fresh()->resources->find($resource1->id)->pivot->quantity);
        $this->assertEquals(10, $item->fresh()->resources->find($resource2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut modifier les quantités des ressources
     */
    public function test_admin_can_update_resource_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();
        
        // Ajouter initialement avec des quantités
        $item->resources()->attach([
            $resource1->id => ['quantity' => 3],
            $resource2->id => ['quantity' => 7],
        ]);

        // Modifier les quantités
        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 15],
                    $resource2->id => ['quantity' => 20],
                ],
            ]);

        $response->assertRedirect();
        $this->assertEquals(15, $item->fresh()->resources->find($resource1->id)->pivot->quantity);
        $this->assertEquals(20, $item->fresh()->resources->find($resource2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut retirer des ressources
     */
    public function test_admin_can_remove_resources_from_item(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();
        $resource3 = Resource::factory()->create();
        
        // Ajouter initialement 3 ressources
        $item->resources()->attach([
            $resource1->id => ['quantity' => 5],
            $resource2->id => ['quantity' => 10],
            $resource3->id => ['quantity' => 15],
        ]);

        // Retirer resource2 et resource3, garder seulement resource1
        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 5],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $item->fresh()->resources);
        $this->assertTrue($item->fresh()->resources->contains($resource1));
        $this->assertFalse($item->fresh()->resources->contains($resource2));
        $this->assertFalse($item->fresh()->resources->contains($resource3));
    }

    /**
     * Test : Un admin peut vider toutes les ressources d'un item
     */
    public function test_admin_can_clear_all_resources_from_item(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();
        
        // Ajouter des ressources initialement
        $item->resources()->attach([
            $resource1->id => ['quantity' => 5],
            $resource2->id => ['quantity' => 10],
        ]);

        // Vider toutes les ressources
        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $item->fresh()->resources);
    }

    /**
     * Test : Un admin peut modifier les ressources de n'importe quel item
     */
    public function test_admin_can_update_resources_of_any_item(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $item = Item::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 5],
                    $resource2->id => ['quantity' => 10],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $item->fresh()->resources);
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas modifier les ressources d'un item
     */
    public function test_user_cannot_update_resources_of_item(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 5],
                ],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $item->fresh()->resources);
    }

    /**
     * Test : La validation échoue si resources n'est pas un array
     */
    public function test_update_resources_fails_if_resources_is_not_array(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('resources');
    }

    /**
     * Test : La validation échoue si une ressource n'existe pas
     */
    public function test_update_resources_fails_if_resource_does_not_exist(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource = Resource::factory()->create();
        
        // Supprimer la ressource pour qu'elle n'existe plus
        $resource->delete();

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource->id => ['quantity' => 5],
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test : Un tableau vide vide toutes les ressources (pas d'erreur de validation)
     */
    public function test_update_resources_with_empty_array_clears_all(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource = Resource::factory()->create();
        $item->resources()->attach($resource->id, ['quantity' => 5]);

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $item->fresh()->resources);
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les ressources
     */
    public function test_guest_cannot_update_resources(): void
    {
        $item = Item::factory()->create();
        $resource = Resource::factory()->create();

        $response = $this->post(route('entities.items.updateResources', $item), [
            '_method' => 'PATCH',
            'resources' => [
                $resource->id => ['quantity' => 5],
            ],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les ressources disponibles
     */
    public function test_edit_page_loads_available_resources(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create(['name' => 'Resource 1']);
        $resource2 = Resource::factory()->create(['name' => 'Resource 2']);
        $item->resources()->attach($resource1->id, ['quantity' => 5]);

        $response = $this->actingAs($admin)
            ->get(route('entities.items.edit', $item));

        $response->assertOk();
        
        // Vérifier la structure de base
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/item/Edit')
            ->has('item')
            ->has('availableResources')
        );
        
        // Vérifier que les ressources sont présentes
        // Note: Inertia enveloppe les ressources uniques dans une clé 'data'
        $response->assertInertia(fn ($page) => $page
            ->has('item.data.resources')
            ->where('item.data.resources.0.id', $resource1->id)
        );
    }

    /**
     * Test : Les quantités zéro ou négatives sont ignorées
     */
    public function test_zero_or_negative_quantities_are_ignored(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.items.edit', $item))
            ->post(route('entities.items.updateResources', $item), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 0],
                    $resource2->id => ['quantity' => -5],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $item->fresh()->resources);
    }
}


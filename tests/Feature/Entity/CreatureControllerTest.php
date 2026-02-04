<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Consumable;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour CreatureController
 * 
 * Vérifie que :
 * - Un utilisateur peut modifier une créature qu'il a créée
 * - Un admin peut modifier n'importe quelle créature
 * - Les méthodes update* synchronisent correctement les relations avec quantités
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class CreatureControllerTest extends TestCase
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
     * Test : Un admin peut ajouter des items à une créature avec quantités
     */
    public function test_admin_can_add_items_to_creature_with_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 3],
                    $item2->id => ['quantity' => 7],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $creature->fresh()->items);
        $this->assertEquals(3, $creature->fresh()->items->find($item1->id)->pivot->quantity);
        $this->assertEquals(7, $creature->fresh()->items->find($item2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut ajouter des ressources à une créature avec quantités
     */
    public function test_admin_can_add_resources_to_creature_with_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateResources', $creature), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => ['quantity' => 5],
                    $resource2->id => ['quantity' => 10],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $creature->fresh()->resources);
        $this->assertEquals(5, $creature->fresh()->resources->find($resource1->id)->pivot->quantity);
        $this->assertEquals(10, $creature->fresh()->resources->find($resource2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut ajouter des consommables à une créature avec quantités
     */
    public function test_admin_can_add_consumables_to_creature_with_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $consumable1 = Consumable::factory()->create();
        $consumable2 = Consumable::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateConsumables', $creature), [
                '_method' => 'PATCH',
                'consumables' => [
                    $consumable1->id => ['quantity' => 2],
                    $consumable2->id => ['quantity' => 4],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $creature->fresh()->consumables);
        $this->assertEquals(2, $creature->fresh()->consumables->find($consumable1->id)->pivot->quantity);
        $this->assertEquals(4, $creature->fresh()->consumables->find($consumable2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut ajouter des sorts à une créature (sans quantité)
     */
    public function test_admin_can_add_spells_to_creature(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $spell1 = Spell::factory()->create();
        $spell2 = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateSpells', $creature), [
                '_method' => 'PATCH',
                'spells' => [$spell1->id, $spell2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $creature->fresh()->spells);
        $this->assertTrue($creature->fresh()->spells->contains($spell1));
        $this->assertTrue($creature->fresh()->spells->contains($spell2));
    }

    /**
     * Test : Un admin peut modifier les quantités des items
     */
    public function test_admin_can_update_item_quantities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        
        // Ajouter initialement avec des quantités
        $creature->items()->attach([
            $item1->id => ['quantity' => 3],
            $item2->id => ['quantity' => 7],
        ]);

        // Modifier les quantités
        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 15],
                    $item2->id => ['quantity' => 20],
                ],
            ]);

        $response->assertRedirect();
        $this->assertEquals(15, $creature->fresh()->items->find($item1->id)->pivot->quantity);
        $this->assertEquals(20, $creature->fresh()->items->find($item2->id)->pivot->quantity);
    }

    /**
     * Test : Un admin peut retirer des items
     */
    public function test_admin_can_remove_items_from_creature(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        $item3 = Item::factory()->create();
        
        // Ajouter initialement 3 items
        $creature->items()->attach([
            $item1->id => ['quantity' => 5],
            $item2->id => ['quantity' => 10],
            $item3->id => ['quantity' => 15],
        ]);

        // Retirer item2 et item3, garder seulement item1
        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 5],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $creature->fresh()->items);
        $this->assertTrue($creature->fresh()->items->contains($item1));
        $this->assertFalse($creature->fresh()->items->contains($item2));
        $this->assertFalse($creature->fresh()->items->contains($item3));
    }

    /**
     * Test : Un admin peut vider toutes les relations
     */
    public function test_admin_can_clear_all_relations(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item = Item::factory()->create();
        $resource = Resource::factory()->create();
        $consumable = Consumable::factory()->create();
        $spell = Spell::factory()->create();
        
        // Ajouter des relations initialement
        $creature->items()->attach($item->id, ['quantity' => 5]);
        $creature->resources()->attach($resource->id, ['quantity' => 10]);
        $creature->consumables()->attach($consumable->id, ['quantity' => 3]);
        $creature->spells()->attach($spell->id);

        // Vider toutes les relations
        $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [],
            ]);
        
        $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateResources', $creature), [
                '_method' => 'PATCH',
                'resources' => [],
            ]);
        
        $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateConsumables', $creature), [
                '_method' => 'PATCH',
                'consumables' => [],
            ]);
        
        $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateSpells', $creature), [
                '_method' => 'PATCH',
                'spells' => [],
            ]);

        $this->assertCount(0, $creature->fresh()->items);
        $this->assertCount(0, $creature->fresh()->resources);
        $this->assertCount(0, $creature->fresh()->consumables);
        $this->assertCount(0, $creature->fresh()->spells);
    }

    /**
     * Test : Un admin peut modifier les relations de n'importe quelle créature
     */
    public function test_admin_can_update_relations_of_any_creature(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $item = Item::factory()->create();
        $resource = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $creature->fresh()->items);

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateResources', $creature), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource->id => ['quantity' => 10],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $creature->fresh()->resources);
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas modifier les relations d'une créature
     */
    public function test_user_cannot_update_relations_of_creature(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $creature = Creature::factory()->create([
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5],
                ],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $creature->fresh()->items);
    }

    /**
     * Test : La validation échoue si items n'est pas un array
     */
    public function test_update_items_fails_if_items_is_not_array(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('items');
    }

    /**
     * Test : La validation échoue si un item n'existe pas
     */
    public function test_update_items_fails_if_item_does_not_exist(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item = Item::factory()->create();
        
        // Supprimer l'item pour qu'il n'existe plus
        $item->delete();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5],
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les relations
     */
    public function test_guest_cannot_update_relations(): void
    {
        $creature = Creature::factory()->create();
        $item = Item::factory()->create();

        $response = $this->post(route('entities.creatures.updateItems', $creature), [
            '_method' => 'PATCH',
            'items' => [
                $item->id => ['quantity' => 5],
            ],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les entités disponibles
     */
    public function test_edit_page_loads_available_entities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item = Item::factory()->create(['name' => 'Item 1']);
        $resource = Resource::factory()->create(['name' => 'Resource 1']);
        $creature->items()->attach($item->id, ['quantity' => 5]);

        $response = $this->actingAs($admin)
            ->get(route('entities.creatures.edit', $creature));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/creature/Edit')
            ->has('creature')
            ->has('availableItems')
            ->has('availableResources')
            ->has('availableConsumables')
            ->has('availableSpells')
            ->where('creature.data.items.0.id', $item->id)
        );
    }

    /**
     * Test : Les quantités zéro ou négatives sont ignorées
     */
    public function test_zero_or_negative_quantities_are_ignored(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.creatures.edit', $creature))
            ->post(route('entities.creatures.updateItems', $creature), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 0],
                    $item2->id => ['quantity' => -5],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $creature->fresh()->items);
    }
}


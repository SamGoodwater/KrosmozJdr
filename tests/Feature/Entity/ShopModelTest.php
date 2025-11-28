<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Shop;
use App\Models\Entity\Npc;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Shop
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ShopModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une boutique via factory
     */
    public function test_shop_factory_creates_valid_shop(): void
    {
        $user = User::factory()->create();
        
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($shop);
        $this->assertNotNull($shop->id);
        $this->assertNotNull($shop->name);
        $this->assertEquals($user->id, $shop->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_shop_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($shop->createdBy);
        $this->assertEquals($user->id, $shop->createdBy->id);
    }

    /**
     * Test de la relation npc
     */
    public function test_shop_has_npc_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
        ]);
        
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
            'npc_id' => $npc->id,
        ]);

        $this->assertNotNull($shop->npc);
        $this->assertEquals($npc->id, $shop->npc->id);
    }

    /**
     * Test de la relation items (many-to-many avec pivot)
     */
    public function test_shop_has_items_relation(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $shop->items()->sync([
            $item1->id => ['quantity' => '5', 'price' => '100', 'comment' => 'Test'],
            $item2->id => ['quantity' => '10', 'price' => '200'],
        ]);

        $shop->refresh();
        $this->assertCount(2, $shop->items);
        
        $pivot1 = $shop->items->where('id', $item1->id)->first()->pivot;
        $this->assertEquals('5', $pivot1->quantity);
        $this->assertEquals('100', $pivot1->price);
    }

    /**
     * Test de la relation consumables (many-to-many avec pivot)
     */
    public function test_shop_has_consumables_relation(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
        ]);

        $consumable1 = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);
        $consumable2 = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $shop->consumables()->sync([
            $consumable1->id => ['quantity' => '3', 'price' => '50'],
            $consumable2->id => ['quantity' => '7', 'price' => '75'],
        ]);

        $shop->refresh();
        $this->assertCount(2, $shop->consumables);
    }

    /**
     * Test de la relation resources (many-to-many avec pivot)
     */
    public function test_shop_has_resources_relation(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource1 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);
        $resource2 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $shop->resources()->sync([
            $resource1->id => ['quantity' => '2', 'price' => '25'],
            $resource2->id => ['quantity' => '4', 'price' => '30'],
        ]);

        $shop->refresh();
        $this->assertCount(2, $shop->resources);
    }
}


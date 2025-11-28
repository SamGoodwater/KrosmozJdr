<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Resource;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Resource
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ResourceModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une ressource via factory
     */
    public function test_resource_factory_creates_valid_resource(): void
    {
        $user = User::factory()->create();
        
        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($resource);
        $this->assertNotNull($resource->id);
        $this->assertNotNull($resource->name);
        $this->assertEquals($user->id, $resource->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_resource_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($resource->createdBy);
        $this->assertEquals($user->id, $resource->createdBy->id);
    }

    /**
     * Test de la relation items (many-to-many avec pivot quantity)
     */
    public function test_resource_has_items_relation(): void
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource->items()->sync([
            $item1->id => ['quantity' => '2'],
            $item2->id => ['quantity' => '3'],
        ]);

        $resource->refresh();
        $this->assertCount(2, $resource->items);
        
        $pivot1 = $resource->items->where('id', $item1->id)->first()->pivot;
        $this->assertEquals('2', $pivot1->quantity);
    }

    /**
     * Test de la relation consumables (many-to-many avec pivot quantity)
     */
    public function test_resource_has_consumables_relation(): void
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $consumable1 = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);
        $consumable2 = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource->consumables()->sync([
            $consumable1->id => ['quantity' => '1'],
            $consumable2->id => ['quantity' => '2'],
        ]);

        $resource->refresh();
        $this->assertCount(2, $resource->consumables);
    }

    /**
     * Test de la relation creatures (many-to-many avec pivot quantity)
     */
    public function test_resource_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource->creatures()->sync([
            $creature1->id => ['quantity' => '1'],
            $creature2->id => ['quantity' => '2'],
        ]);

        $resource->refresh();
        $this->assertCount(2, $resource->creatures);
    }
}


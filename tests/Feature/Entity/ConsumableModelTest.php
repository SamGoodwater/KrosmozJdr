<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Consumable
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ConsumableModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un consommable via factory
     */
    public function test_consumable_factory_creates_valid_consumable(): void
    {
        $user = User::factory()->create();
        
        $consumable = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($consumable);
        $this->assertNotNull($consumable->id);
        $this->assertNotNull($consumable->name);
        $this->assertEquals($user->id, $consumable->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_consumable_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $consumable = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($consumable->createdBy);
        $this->assertEquals($user->id, $consumable->createdBy->id);
    }

    /**
     * Test de la relation resources (many-to-many avec pivot quantity)
     */
    public function test_consumable_has_resources_relation(): void
    {
        $user = User::factory()->create();
        $consumable = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource1 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);
        $resource2 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $consumable->resources()->sync([
            $resource1->id => ['quantity' => '2'],
            $resource2->id => ['quantity' => '3'],
        ]);

        $consumable->refresh();
        $this->assertCount(2, $consumable->resources);
        
        $pivot1 = $consumable->resources->where('id', $resource1->id)->first()->pivot;
        $this->assertEquals('2', $pivot1->quantity);
        
        $pivot2 = $consumable->resources->where('id', $resource2->id)->first()->pivot;
        $this->assertEquals('3', $pivot2->quantity);
    }

    /**
     * Test de la relation creatures (many-to-many avec pivot quantity)
     */
    public function test_consumable_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $consumable = Consumable::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $consumable->creatures()->sync([
            $creature1->id => ['quantity' => '1'],
            $creature2->id => ['quantity' => '2'],
        ]);

        $consumable->refresh();
        $this->assertCount(2, $consumable->creatures);
    }
}


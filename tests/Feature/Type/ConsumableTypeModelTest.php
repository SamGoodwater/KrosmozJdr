<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\ConsumableType;
use App\Models\Entity\Consumable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle ConsumableType
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class ConsumableTypeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un type de consommable via factory
     */
    public function test_consumable_type_factory_creates_valid_consumable_type(): void
    {
        $user = User::factory()->create();
        
        $consumableType = ConsumableType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($consumableType);
        $this->assertNotNull($consumableType->id);
        $this->assertNotNull($consumableType->name);
        $this->assertEquals($user->id, $consumableType->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_consumable_type_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $consumableType = ConsumableType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($consumableType->createdBy);
        $this->assertEquals($user->id, $consumableType->createdBy->id);
    }

    /**
     * Test de la relation consumables (hasMany)
     */
    public function test_consumable_type_has_consumables_relation(): void
    {
        $user = User::factory()->create();
        $consumableType = ConsumableType::factory()->create([
            'created_by' => $user->id,
        ]);

        $consumable1 = Consumable::factory()->create([
            'created_by' => $user->id,
            'consumable_type_id' => $consumableType->id,
        ]);
        $consumable2 = Consumable::factory()->create([
            'created_by' => $user->id,
            'consumable_type_id' => $consumableType->id,
        ]);

        $consumableType->refresh();
        $this->assertCount(2, $consumableType->consumables);
        $this->assertTrue($consumableType->consumables->contains($consumable1));
        $this->assertTrue($consumableType->consumables->contains($consumable2));
    }
}


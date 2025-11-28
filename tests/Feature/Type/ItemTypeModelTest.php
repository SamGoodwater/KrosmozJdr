<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\ItemType;
use App\Models\Entity\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle ItemType
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class ItemTypeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un type d'item via factory
     */
    public function test_item_type_factory_creates_valid_item_type(): void
    {
        $user = User::factory()->create();
        
        $itemType = ItemType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($itemType);
        $this->assertNotNull($itemType->id);
        $this->assertNotNull($itemType->name);
        $this->assertEquals($user->id, $itemType->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_item_type_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $itemType = ItemType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($itemType->createdBy);
        $this->assertEquals($user->id, $itemType->createdBy->id);
    }

    /**
     * Test de la relation items (hasMany)
     */
    public function test_item_type_has_items_relation(): void
    {
        $user = User::factory()->create();
        $itemType = ItemType::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
            'item_type_id' => $itemType->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
            'item_type_id' => $itemType->id,
        ]);

        $itemType->refresh();
        $this->assertCount(2, $itemType->items);
        $this->assertTrue($itemType->items->contains($item1));
        $this->assertTrue($itemType->items->contains($item2));
    }
}


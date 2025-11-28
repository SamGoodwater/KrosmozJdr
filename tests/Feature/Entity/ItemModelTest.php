<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Panoply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Item
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ItemModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un item via factory
     */
    public function test_item_factory_creates_valid_item(): void
    {
        $user = User::factory()->create();
        
        $item = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($item);
        $this->assertNotNull($item->id);
        $this->assertNotNull($item->name);
        $this->assertEquals($user->id, $item->created_by);
    }

    /**
     * Test de la relation avec les ressources (recette)
     */
    public function test_item_has_resources_relation(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource1 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);
        $resource2 = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $item->resources()->sync([
            $resource1->id => ['quantity' => '2'],
            $resource2->id => ['quantity' => '3'],
        ]);

        $item->refresh();
        $this->assertCount(2, $item->resources);
        
        $pivot1 = $item->resources->where('id', $resource1->id)->first()->pivot;
        $this->assertEquals('2', $pivot1->quantity);
        
        $pivot2 = $item->resources->where('id', $resource2->id)->first()->pivot;
        $this->assertEquals('3', $pivot2->quantity);
    }

    /**
     * Test de la relation avec les panoplies
     */
    public function test_item_has_panoplies_relation(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $panoply1 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $panoply2 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $item->panoplies()->sync([$panoply1->id, $panoply2->id]);

        $item->refresh();
        $this->assertCount(2, $item->panoplies);
        $this->assertTrue($item->panoplies->contains($panoply1));
        $this->assertTrue($item->panoplies->contains($panoply2));
    }
}


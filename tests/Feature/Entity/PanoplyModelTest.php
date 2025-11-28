<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Panoply;
use App\Models\Entity\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Panoply
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class PanoplyModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une panoplie via factory
     */
    public function test_panoply_factory_creates_valid_panoply(): void
    {
        $user = User::factory()->create();
        
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($panoply);
        $this->assertNotNull($panoply->id);
        $this->assertNotNull($panoply->name);
        $this->assertEquals($user->id, $panoply->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_panoply_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($panoply->createdBy);
        $this->assertEquals($user->id, $panoply->createdBy->id);
    }

    /**
     * Test de la relation items (many-to-many via item_panoply)
     */
    public function test_panoply_has_items_relation(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item3 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $panoply->items()->sync([$item1->id, $item2->id, $item3->id]);

        $panoply->refresh();
        $this->assertCount(3, $panoply->items);
        $this->assertTrue($panoply->items->contains($item1));
        $this->assertTrue($panoply->items->contains($item2));
        $this->assertTrue($panoply->items->contains($item3));
    }

    /**
     * Test de la relation inverse items->panoplies
     */
    public function test_item_can_belong_to_panoplies(): void
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

    /**
     * Test de suppression en cascade (panoply supprimée, relations supprimées)
     * 
     * Note: Panoply utilise SoftDeletes, mais la migration item_panoply a cascadeOnDelete,
     * donc la suppression définitive devrait supprimer les relations dans la table pivot.
     */
    public function test_panoply_deletion_cascades_to_pivot_table(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $panoply->items()->sync([$item1->id, $item2->id]);

        $this->assertDatabaseHas('item_panoply', [
            'panoply_id' => $panoply->id,
            'item_id' => $item1->id,
        ]);
        $this->assertDatabaseHas('item_panoply', [
            'panoply_id' => $panoply->id,
            'item_id' => $item2->id,
        ]);

        // Supprimer définitivement (forceDelete) pour déclencher la cascade
        $panoplyId = $panoply->id;
        $panoply->forceDelete();

        // Avec cascadeOnDelete, les relations dans la table pivot devraient être supprimées
        $this->assertDatabaseMissing('item_panoply', [
            'panoply_id' => $panoplyId,
            'item_id' => $item1->id,
        ]);
        $this->assertDatabaseMissing('item_panoply', [
            'panoply_id' => $panoplyId,
            'item_id' => $item2->id,
        ]);

        // Les items eux-mêmes ne doivent pas être supprimés
        $this->assertDatabaseHas('items', [
            'id' => $item1->id,
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $item2->id,
        ]);
    }

    /**
     * Test de recherche par dofusdb_id
     */
    public function test_panoply_can_be_found_by_dofusdb_id(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
            'dofusdb_id' => '123',
        ]);

        $found = Panoply::where('dofusdb_id', '123')->first();

        $this->assertNotNull($found);
        $this->assertEquals($panoply->id, $found->id);
    }
}


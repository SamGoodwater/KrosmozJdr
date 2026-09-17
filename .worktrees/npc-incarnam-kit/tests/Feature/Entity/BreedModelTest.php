<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Breed
 *
 * Vérifie que le modèle fonctionne correctement avec ses relations
 */
class BreedModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un breed via factory
     */
    public function test_breed_factory_creates_valid_breed(): void
    {
        $user = User::factory()->create();

        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($breed);
        $this->assertNotNull($breed->id);
        $this->assertNotNull($breed->name);
        $this->assertEquals($user->id, $breed->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_breed_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($breed->createdBy);
        $this->assertEquals($user->id, $breed->createdBy->id);
    }

    /**
     * Test de la relation spells (many-to-many)
     */
    public function test_breed_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        // Attacher les sorts au breed
        $breed->spells()->attach([$spell1->id, $spell2->id]);

        // Rafraîchir la relation
        $breed->refresh();

        $this->assertSame(2, $breed->spells->count());
        $this->assertTrue($breed->spells->contains($spell1));
        $this->assertTrue($breed->spells->contains($spell2));
    }

    /**
     * Test de synchronisation des sorts
     */
    public function test_breed_can_sync_spells(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell3 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        // Synchroniser les sorts
        $breed->spells()->sync([$spell1->id, $spell2->id]);

        $breed->refresh();
        $this->assertSame(2, $breed->spells->count());

        // Synchroniser avec d'autres sorts
        $breed->spells()->sync([$spell2->id, $spell3->id]);

        $breed->refresh();
        $this->assertSame(2, $breed->spells->count());
        $this->assertFalse($breed->spells->contains($spell1));
        $this->assertTrue($breed->spells->contains($spell2));
        $this->assertTrue($breed->spells->contains($spell3));
    }

    /**
     * Regroupement par (character_level, slot_index), tri par choice_order puis nom.
     */
    public function test_get_spell_slots_grouped_orders_spells(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create(['created_by' => $user->id]);
        $sA = Spell::factory()->create(['created_by' => $user->id, 'name' => 'Alpha']);
        $sB = Spell::factory()->create(['created_by' => $user->id, 'name' => 'Bravo']);

        $breed->spells()->sync([
            $sA->id => [
                'character_level' => 1,
                'slot_index' => 1,
                'choice_order' => 1,
            ],
            $sB->id => [
                'character_level' => 1,
                'slot_index' => 1,
                'choice_order' => 0,
            ],
        ]);

        $breed->load('spells');
        $grouped = $breed->getSpellSlotsGrouped();

        $this->assertCount(1, $grouped);
        $this->assertSame(1, $grouped[0]['character_level']);
        $this->assertSame(1, $grouped[0]['slot_index']);
        $names = $grouped[0]['spells']->pluck('name')->all();
        $this->assertSame(['Bravo', 'Alpha'], $names);
    }

    /**
     * Test de suppression en cascade
     *
     * Note: Breed utilise SoftDeletes, donc la suppression est un soft delete.
     * La relation dans la table pivot devrait être supprimée en cascade.
     */
    public function test_breed_deletion_cascades_to_spells_relation(): void
    {
        $user = User::factory()->create();
        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $breed->spells()->attach($spell->id);

        // Vérifier que la relation existe
        $this->assertTrue($breed->spells->contains($spell));
        $this->assertDatabaseHas('breed_spell', [
            'breed_id' => $breed->id,
            'spell_id' => $spell->id,
        ]);

        // Supprimer le breed (soft delete)
        $breedId = $breed->id;
        Breed::destroy($breedId);

        // Vérifier que le breed est bien soft deleted
        $this->assertSoftDeleted('breeds', [
            'id' => $breedId,
        ]);

        // Le sort lui-même ne doit pas être supprimé
        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
        ]);
    }
}

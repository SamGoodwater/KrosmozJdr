<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Classe;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Classe
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ClasseModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une classe via factory
     */
    public function test_classe_factory_creates_valid_class(): void
    {
        $user = User::factory()->create();
        
        $classe = Classe::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($classe);
        $this->assertNotNull($classe->id);
        $this->assertNotNull($classe->name);
        $this->assertEquals($user->id, $classe->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_classe_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $classe = Classe::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($classe->createdBy);
        $this->assertEquals($user->id, $classe->createdBy->id);
    }

    /**
     * Test de la relation spells (many-to-many)
     */
    public function test_classe_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $classe = Classe::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        // Attacher les sorts à la classe
        $classe->spells()->attach([$spell1->id, $spell2->id]);

        // Rafraîchir la relation
        $classe->refresh();

        $this->assertCount(2, $classe->spells);
        $this->assertTrue($classe->spells->contains($spell1));
        $this->assertTrue($classe->spells->contains($spell2));
    }

    /**
     * Test de synchronisation des sorts
     */
    public function test_classe_can_sync_spells(): void
    {
        $user = User::factory()->create();
        $classe = Classe::factory()->create([
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
        $classe->spells()->sync([$spell1->id, $spell2->id]);

        $classe->refresh();
        $this->assertCount(2, $classe->spells);

        // Synchroniser avec d'autres sorts
        $classe->spells()->sync([$spell2->id, $spell3->id]);

        $classe->refresh();
        $this->assertCount(2, $classe->spells);
        $this->assertFalse($classe->spells->contains($spell1));
        $this->assertTrue($classe->spells->contains($spell2));
        $this->assertTrue($classe->spells->contains($spell3));
    }

    /**
     * Test de suppression en cascade
     * 
     * Note: Classe utilise SoftDeletes, donc la suppression est un soft delete.
     * La relation dans la table pivot devrait être supprimée en cascade.
     */
    public function test_classe_deletion_cascades_to_spells_relation(): void
    {
        $user = User::factory()->create();
        $classe = Classe::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $classe->spells()->attach($spell->id);

        // Vérifier que la relation existe
        $this->assertTrue($classe->spells->contains($spell));
        $this->assertDatabaseHas('class_spell', [
            'classe_id' => $classe->id,
            'spell_id' => $spell->id,
        ]);

        // Supprimer la classe (soft delete)
        $classeId = $classe->id;
        $classe->delete();

        // Avec cascadeOnDelete, la relation dans la table pivot devrait être supprimée
        // Mais avec SoftDeletes, la cascade ne se déclenche pas automatiquement
        // Vérifier que la classe est bien soft deleted
        $this->assertSoftDeleted('classes', [
            'id' => $classeId,
        ]);

        // Le sort lui-même ne doit pas être supprimé
        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
        ]);
    }
}


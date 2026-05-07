<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle CreatureTrait
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class CreatureTraitModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un trait via factory
     */
    public function test_creature_trait_factory_creates_valid_creature_trait(): void
    {
        $user = User::factory()->create();
        
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($creatureTrait);
        $this->assertNotNull($creatureTrait->id);
        $this->assertNotNull($creatureTrait->name);
        $this->assertEquals($user->id, $creatureTrait->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_creature_trait_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($creatureTrait->createdBy);
        $this->assertEquals($user->id, $creatureTrait->createdBy->id);
    }

    /**
     * Test de la relation creatures (many-to-many)
     */
    public function test_creature_trait_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $creatureTrait->creatures()->sync([$creature1->id, $creature2->id]);

        $creatureTrait->refresh();
        $this->assertCount(2, $creatureTrait->creatures);
        $this->assertTrue($creatureTrait->creatures->contains($creature1));
        $this->assertTrue($creatureTrait->creatures->contains($creature2));
    }

    /**
     * Test de synchronisation des créatures
     */
    public function test_creature_trait_can_sync_creatures(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature3 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $creatureTrait->creatures()->sync([$creature1->id, $creature2->id]);

        $creatureTrait->refresh();
        $this->assertCount(2, $creatureTrait->creatures);

        $creatureTrait->creatures()->sync([$creature2->id, $creature3->id]);

        $creatureTrait->refresh();
        $this->assertCount(2, $creatureTrait->creatures);
        $this->assertFalse($creatureTrait->creatures->contains($creature1));
        $this->assertTrue($creatureTrait->creatures->contains($creature2));
        $this->assertTrue($creatureTrait->creatures->contains($creature3));
    }

    /**
     * Test de suppression en cascade
     */
    public function test_creature_trait_deletion_cascades_to_creatures_relation(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $creatureTrait->creatures()->attach($creature->id);

        $this->assertTrue($creatureTrait->creatures->contains($creature));
        $this->assertDatabaseHas('creature_creature_trait', [
            'creature_trait_id' => $creatureTrait->id,
            'creature_id' => $creature->id,
        ]);

        $creatureTraitId = $creatureTrait->id;
        $creatureTrait->forceDelete();

        $this->assertDatabaseMissing('creature_creature_trait', [
            'creature_trait_id' => $creatureTraitId,
            'creature_id' => $creature->id,
        ]);

        $this->assertDatabaseHas('creatures', [
            'id' => $creature->id,
        ]);
    }
}


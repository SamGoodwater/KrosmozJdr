<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Condition;
use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Condition
 *
 * Vérifie que le modèle fonctionne correctement avec ses relations
 */
class ConditionModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un attribut via factory
     */
    public function test_condition_factory_creates_valid_condition(): void
    {
        $user = User::factory()->create();

        $condition = Condition::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($condition);
        $this->assertNotNull($condition->id);
        $this->assertNotNull($condition->name);
        $this->assertEquals($user->id, $condition->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_condition_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($condition->createdBy);
        $this->assertEquals($user->id, $condition->createdBy->id);
    }

    /**
     * Test de la relation creatures (many-to-many)
     */
    public function test_condition_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $condition->creatures()->sync([$creature1->id, $creature2->id]);

        $condition->refresh();
        $this->assertCount(2, $condition->creatures);
        $this->assertTrue($condition->creatures->contains($creature1));
        $this->assertTrue($condition->creatures->contains($creature2));
    }

    /**
     * Test de synchronisation des créatures
     */
    public function test_condition_can_sync_creatures(): void
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create([
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

        $condition->creatures()->sync([$creature1->id, $creature2->id]);

        $condition->refresh();
        $this->assertCount(2, $condition->creatures);

        $condition->creatures()->sync([$creature2->id, $creature3->id]);

        $condition->refresh();
        $this->assertCount(2, $condition->creatures);
        $this->assertFalse($condition->creatures->contains($creature1));
        $this->assertTrue($condition->creatures->contains($creature2));
        $this->assertTrue($condition->creatures->contains($creature3));
    }

    /**
     * Test de suppression en cascade
     */
    public function test_condition_deletion_cascades_to_creatures_relation(): void
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $condition->creatures()->attach($creature->id);

        $this->assertTrue($condition->creatures->contains($creature));
        $this->assertDatabaseHas('condition_creature', [
            'condition_id' => $condition->id,
            'creature_id' => $creature->id,
        ]);

        $conditionId = $condition->id;
        $condition->forceDelete();

        $this->assertDatabaseMissing('condition_creature', [
            'condition_id' => $conditionId,
            'creature_id' => $creature->id,
        ]);

        $this->assertDatabaseHas('creatures', [
            'id' => $creature->id,
        ]);
    }

    public function test_active_mechanical_flags_lists_only_true_values(): void
    {
        $condition = Condition::factory()->create([
            'cant_be_moved' => true,
            'invulnerable' => false,
            'prevents_spell_cast' => true,
        ]);

        $this->assertSame(
            [
                ['key' => 'prevents_spell_cast', 'label' => 'Empêche de lancer des sorts'],
                ['key' => 'cant_be_moved', 'label' => 'Ne peut pas être déplacé'],
            ],
            $condition->activeMechanicalFlags()
        );
        $this->assertTrue($condition->mechanicalFlagValues()['cant_be_moved']);
        $this->assertFalse($condition->mechanicalFlagValues()['invulnerable']);
    }
}

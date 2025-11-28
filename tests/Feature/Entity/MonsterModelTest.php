<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Monster;
use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Monster
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class MonsterModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un monstre via factory
     */
    public function test_monster_factory_creates_valid_monster(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $this->assertNotNull($monster);
        $this->assertNotNull($monster->id);
        $this->assertEquals($creature->id, $monster->creature_id);
    }

    /**
     * Test de la relation creature
     */
    public function test_monster_has_creature_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $this->assertNotNull($monster->creature);
        $this->assertEquals($creature->id, $monster->creature->id);
    }

    /**
     * Test de la relation spellInvocations (many-to-many)
     */
    public function test_monster_has_spell_invocations_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $monster->spellInvocations()->sync([$spell1->id, $spell2->id]);

        $monster->refresh();
        $this->assertCount(2, $monster->spellInvocations);
        $this->assertTrue($monster->spellInvocations->contains($spell1));
        $this->assertTrue($monster->spellInvocations->contains($spell2));
    }
}


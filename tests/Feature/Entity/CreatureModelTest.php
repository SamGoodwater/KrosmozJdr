<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Entity\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Creature
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class CreatureModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une créature via factory
     */
    public function test_creature_factory_creates_valid_creature(): void
    {
        $user = User::factory()->create();
        
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($creature);
        $this->assertNotNull($creature->id);
        $this->assertNotNull($creature->name);
        $this->assertEquals($user->id, $creature->created_by);
    }

    /**
     * Test de la relation avec Monster
     */
    public function test_creature_has_monster_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $this->assertNotNull($creature->monster);
        $this->assertEquals($monster->id, $creature->monster->id);
    }

    /**
     * Test de la relation avec les sorts
     */
    public function test_creature_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature->spells()->sync([$spell1->id, $spell2->id]);

        $creature->refresh();
        $this->assertCount(2, $creature->spells);
    }

    /**
     * Test de la relation avec les ressources
     */
    public function test_creature_has_resources_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $resource = Resource::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature->resources()->sync([
            $resource->id => ['quantity' => '5']
        ]);

        $creature->refresh();
        $this->assertCount(1, $creature->resources);
        $this->assertEquals('5', $creature->resources->first()->pivot->quantity);
    }
}


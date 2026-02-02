<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Npc;
use App\Models\Entity\Creature;
use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Entity\Panoply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Npc
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class NpcModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un NPC via factory
     */
    public function test_npc_factory_creates_valid_npc(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $this->assertNotNull($npc);
        $this->assertNotNull($npc->id);
        $this->assertEquals($creature->id, $npc->creature_id);
    }

    /**
     * Test de la relation creature
     */
    public function test_npc_has_creature_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $this->assertNotNull($npc->creature);
        $this->assertEquals($creature->id, $npc->creature->id);
    }

    /**
     * Test de la relation breed (classe jouable)
     */
    public function test_npc_has_breed_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'breed_id' => $breed->id,
        ]);

        $this->assertNotNull($npc->breed);
        $this->assertEquals($breed->id, $npc->breed->id);
    }

    /**
     * Test de la relation specialization
     */
    public function test_npc_has_specialization_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'specialization_id' => $specialization->id,
        ]);

        $this->assertNotNull($npc->specialization);
        $this->assertEquals($specialization->id, $npc->specialization->id);
    }

    /**
     * Test de la relation panoplies (many-to-many)
     */
    public function test_npc_has_panoplies_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $panoply1 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $panoply2 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $npc->panoplies()->sync([$panoply1->id, $panoply2->id]);

        $npc->refresh();
        $this->assertCount(2, $npc->panoplies);
        $this->assertTrue($npc->panoplies->contains($panoply1));
        $this->assertTrue($npc->panoplies->contains($panoply2));
    }
}


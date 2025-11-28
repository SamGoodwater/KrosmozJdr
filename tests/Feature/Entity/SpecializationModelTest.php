<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Specialization;
use App\Models\Entity\Capability;
use App\Models\Entity\Npc;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Specialization
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class SpecializationModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une spécialisation via factory
     */
    public function test_specialization_factory_creates_valid_specialization(): void
    {
        $user = User::factory()->create();
        
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($specialization);
        $this->assertNotNull($specialization->id);
        $this->assertNotNull($specialization->name);
        $this->assertEquals($user->id, $specialization->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_specialization_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($specialization->createdBy);
        $this->assertEquals($user->id, $specialization->createdBy->id);
    }

    /**
     * Test de la relation capabilities (many-to-many)
     */
    public function test_specialization_has_capabilities_relation(): void
    {
        $user = User::factory()->create();
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $capability1 = Capability::factory()->create([
            'created_by' => $user->id,
        ]);
        $capability2 = Capability::factory()->create([
            'created_by' => $user->id,
        ]);

        $specialization->capabilities()->sync([$capability1->id, $capability2->id]);

        $specialization->refresh();
        $this->assertCount(2, $specialization->capabilities);
        $this->assertTrue($specialization->capabilities->contains($capability1));
        $this->assertTrue($specialization->capabilities->contains($capability2));
    }

    /**
     * Test de la relation npcs (hasMany)
     */
    public function test_specialization_has_npcs_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $specialization = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $npc1 = Npc::factory()->create([
            'creature_id' => $creature->id,
            'specialization_id' => $specialization->id,
        ]);
        $npc2 = Npc::factory()->create([
            'creature_id' => $creature->id,
            'specialization_id' => $specialization->id,
        ]);

        $specialization->refresh();
        $this->assertCount(2, $specialization->npcs);
        $this->assertTrue($specialization->npcs->contains($npc1));
        $this->assertTrue($specialization->npcs->contains($npc2));
    }
}


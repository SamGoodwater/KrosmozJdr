<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Capability
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class CapabilityModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une capacité via factory
     */
    public function test_capability_factory_creates_valid_capability(): void
    {
        $user = User::factory()->create();
        
        $capability = Capability::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($capability);
        $this->assertNotNull($capability->id);
        $this->assertNotNull($capability->name);
        $this->assertEquals($user->id, $capability->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_capability_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $capability = Capability::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($capability->createdBy);
        $this->assertEquals($user->id, $capability->createdBy->id);
    }

    /**
     * Test de la relation specializations (many-to-many)
     */
    public function test_capability_has_specializations_relation(): void
    {
        $user = User::factory()->create();
        $capability = Capability::factory()->create([
            'created_by' => $user->id,
        ]);

        $specialization1 = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);
        $specialization2 = Specialization::factory()->create([
            'created_by' => $user->id,
        ]);

        $capability->specializations()->sync([$specialization1->id, $specialization2->id]);

        $capability->refresh();
        $this->assertCount(2, $capability->specializations);
        $this->assertTrue($capability->specializations->contains($specialization1));
        $this->assertTrue($capability->specializations->contains($specialization2));
    }

    /**
     * Test de la relation creatures (many-to-many)
     */
    public function test_capability_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $capability = Capability::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $capability->creatures()->sync([$creature1->id, $creature2->id]);

        $capability->refresh();
        $this->assertCount(2, $capability->creatures);
        $this->assertTrue($capability->creatures->contains($creature1));
        $this->assertTrue($capability->creatures->contains($creature2));
    }
}


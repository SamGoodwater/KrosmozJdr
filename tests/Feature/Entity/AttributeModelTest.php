<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Attribute;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Attribute
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class AttributeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un attribut via factory
     */
    public function test_attribute_factory_creates_valid_attribute(): void
    {
        $user = User::factory()->create();
        
        $attribute = Attribute::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($attribute);
        $this->assertNotNull($attribute->id);
        $this->assertNotNull($attribute->name);
        $this->assertEquals($user->id, $attribute->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_attribute_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($attribute->createdBy);
        $this->assertEquals($user->id, $attribute->createdBy->id);
    }

    /**
     * Test de la relation creatures (many-to-many)
     */
    public function test_attribute_has_creatures_relation(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature1 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $creature2 = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $attribute->creatures()->sync([$creature1->id, $creature2->id]);

        $attribute->refresh();
        $this->assertCount(2, $attribute->creatures);
        $this->assertTrue($attribute->creatures->contains($creature1));
        $this->assertTrue($attribute->creatures->contains($creature2));
    }

    /**
     * Test de synchronisation des créatures
     */
    public function test_attribute_can_sync_creatures(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create([
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

        $attribute->creatures()->sync([$creature1->id, $creature2->id]);

        $attribute->refresh();
        $this->assertCount(2, $attribute->creatures);

        $attribute->creatures()->sync([$creature2->id, $creature3->id]);

        $attribute->refresh();
        $this->assertCount(2, $attribute->creatures);
        $this->assertFalse($attribute->creatures->contains($creature1));
        $this->assertTrue($attribute->creatures->contains($creature2));
        $this->assertTrue($attribute->creatures->contains($creature3));
    }

    /**
     * Test de suppression en cascade
     */
    public function test_attribute_deletion_cascades_to_creatures_relation(): void
    {
        $user = User::factory()->create();
        $attribute = Attribute::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        $attribute->creatures()->attach($creature->id);

        $this->assertTrue($attribute->creatures->contains($creature));
        $this->assertDatabaseHas('attribute_creature', [
            'attribute_id' => $attribute->id,
            'creature_id' => $creature->id,
        ]);

        $attributeId = $attribute->id;
        $attribute->forceDelete();

        $this->assertDatabaseMissing('attribute_creature', [
            'attribute_id' => $attributeId,
            'creature_id' => $creature->id,
        ]);

        $this->assertDatabaseHas('creatures', [
            'id' => $creature->id,
        ]);
    }
}


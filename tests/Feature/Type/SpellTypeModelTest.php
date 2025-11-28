<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\SpellType;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle SpellType
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class SpellTypeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un type de sort via factory
     */
    public function test_spell_type_factory_creates_valid_spell_type(): void
    {
        $user = User::factory()->create();
        
        $spellType = SpellType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($spellType);
        $this->assertNotNull($spellType->id);
        $this->assertNotNull($spellType->name);
        $this->assertEquals($user->id, $spellType->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_spell_type_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $spellType = SpellType::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($spellType->createdBy);
        $this->assertEquals($user->id, $spellType->createdBy->id);
    }

    /**
     * Test de la relation spells (many-to-many via spell_type)
     */
    public function test_spell_type_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $spellType = SpellType::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $spellType->spells()->sync([$spell1->id, $spell2->id]);

        $spellType->refresh();
        $this->assertCount(2, $spellType->spells);
        $this->assertTrue($spellType->spells->contains($spell1));
        $this->assertTrue($spellType->spells->contains($spell2));
    }
}


<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Spell;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Spell
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class SpellModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un sort via factory
     */
    public function test_spell_factory_creates_valid_spell(): void
    {
        $user = User::factory()->create();
        
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($spell);
        $this->assertNotNull($spell->id);
        $this->assertNotNull($spell->name);
        $this->assertEquals($user->id, $spell->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_spell_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($spell->createdBy);
        $this->assertEquals($user->id, $spell->createdBy->id);
    }

    /**
     * Test de la relation avec les breeds (many-to-many via breed_spell)
     */
    public function test_spell_can_belong_to_breeds(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $breed1 = Breed::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed2 = Breed::factory()->create([
            'created_by' => $user->id,
        ]);

        // Attacher le sort aux breeds
        $breed1->spells()->attach($spell->id);
        $breed2->spells()->attach($spell->id);

        // Vérifier depuis le sort (relation inverse)
        $spell->refresh();
        $this->assertCount(2, $spell->breeds);
        $this->assertTrue($spell->breeds->contains($breed1));
        $this->assertTrue($spell->breeds->contains($breed2));
    }

    /**
     * Test de la relation avec les créatures (many-to-many via creature_spell)
     */
    public function test_spell_can_belong_to_creatures(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);

        // Attacher le sort à la créature
        $creature->spells()->attach($spell->id);

        $spell->refresh();

        $this->assertCount(1, $spell->creatures);
        $this->assertTrue($spell->creatures->contains($creature));
    }

    /**
     * Test de la relation avec les monstres invoqués (many-to-many via spell_invocation)
     */
    public function test_spell_can_have_invoked_monsters(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        // Attacher le monstre invoqué au sort
        $spell->monsters()->attach($monster->id);

        $spell->refresh();

        $this->assertCount(1, $spell->monsters);
        $this->assertTrue($spell->monsters->contains($monster));
    }
}


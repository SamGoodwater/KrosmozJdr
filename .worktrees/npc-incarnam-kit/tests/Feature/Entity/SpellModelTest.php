<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Spell
 *
 * Vérifie que le modèle fonctionne correctement avec ses relations
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
     * Portée affichable (po_display) : pas d’imputation max=min, tiret seulement si deux bornes distinctes.
     */
    public function test_po_display_formats_min_max_range(): void
    {
        $user = User::factory()->create();

        $bothEqual = Spell::factory()->create([
            'created_by' => $user->id,
            'po_min' => '2',
            'po_max' => '2',
        ]);
        $this->assertSame('2', $bothEqual->po_display);

        $range = Spell::factory()->create([
            'created_by' => $user->id,
            'po_min' => '2',
            'po_max' => '6',
        ]);
        $this->assertSame('2 - 6', $range->po_display);

        $minOnly = Spell::factory()->create([
            'created_by' => $user->id,
            'po_min' => '3',
            'po_max' => '',
        ]);
        $this->assertSame('3', $minOnly->fresh()->po_display);

        $maxOnly = Spell::factory()->create([
            'created_by' => $user->id,
            'po_min' => '',
            'po_max' => '5',
        ]);
        $this->assertSame('5', $maxOnly->fresh()->po_display);
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

<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Scenario;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Scenario
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class ScenarioModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un scénario via factory
     */
    public function test_scenario_factory_creates_valid_scenario(): void
    {
        $user = User::factory()->create();
        
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($scenario);
        $this->assertNotNull($scenario->id);
        $this->assertNotNull($scenario->name);
        $this->assertEquals($user->id, $scenario->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_scenario_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($scenario->createdBy);
        $this->assertEquals($user->id, $scenario->createdBy->id);
    }

    /**
     * Test de la relation items (many-to-many)
     */
    public function test_scenario_has_items_relation(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $scenario->items()->sync([$item1->id, $item2->id]);

        $scenario->refresh();
        $this->assertCount(2, $scenario->items);
        $this->assertTrue($scenario->items->contains($item1));
        $this->assertTrue($scenario->items->contains($item2));
    }

    /**
     * Test de la relation monsters (many-to-many)
     */
    public function test_scenario_has_monsters_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $monster1 = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);
        $monster2 = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $scenario->monsters()->sync([$monster1->id, $monster2->id]);

        $scenario->refresh();
        $this->assertCount(2, $scenario->monsters);
    }

    /**
     * Test de la relation spells (many-to-many)
     */
    public function test_scenario_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $scenario->spells()->sync([$spell1->id, $spell2->id]);

        $scenario->refresh();
        $this->assertCount(2, $scenario->spells);
    }
}


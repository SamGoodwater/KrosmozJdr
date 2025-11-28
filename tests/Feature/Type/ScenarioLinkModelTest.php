<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\ScenarioLink;
use App\Models\Entity\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle ScenarioLink
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class ScenarioLinkModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'un lien de scénario
     * 
     * Note: ScenarioLink n'a pas de factory, on crée directement
     */
    public function test_scenario_link_can_be_created(): void
    {
        $user = User::factory()->create();
        $scenario1 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        $scenario2 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $scenarioLink = ScenarioLink::create([
            'scenario_id' => $scenario1->id,
            'next_scenario_id' => $scenario2->id,
            'condition' => 'Si le joueur a terminé la quête',
        ]);

        $this->assertNotNull($scenarioLink);
        $this->assertNotNull($scenarioLink->id);
        $this->assertEquals($scenario1->id, $scenarioLink->scenario_id);
        $this->assertEquals($scenario2->id, $scenarioLink->next_scenario_id);
    }

    /**
     * Test de la relation scenario (belongsTo)
     */
    public function test_scenario_link_has_scenario_relation(): void
    {
        $user = User::factory()->create();
        $scenario1 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        $scenario2 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $scenarioLink = ScenarioLink::create([
            'scenario_id' => $scenario1->id,
            'next_scenario_id' => $scenario2->id,
        ]);

        $this->assertNotNull($scenarioLink->scenario);
        $this->assertEquals($scenario1->id, $scenarioLink->scenario->id);
    }

    /**
     * Test de la relation nextScenario (belongsTo)
     */
    public function test_scenario_link_has_next_scenario_relation(): void
    {
        $user = User::factory()->create();
        $scenario1 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        $scenario2 = Scenario::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $scenarioLink = ScenarioLink::create([
            'scenario_id' => $scenario1->id,
            'next_scenario_id' => $scenario2->id,
        ]);

        $this->assertNotNull($scenarioLink->nextScenario);
        $this->assertEquals($scenario2->id, $scenarioLink->nextScenario->id);
    }
}


<?php

namespace Tests\Feature\Api;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour l'endpoint GET /api/characteristics/{key}/norms/{entity?}
 */
class CharacteristicNormsControllerTest extends TestCase
{
    use RefreshDatabase;

    private function seedCharacteristic(string $key = 'strength_creature', array $overrides = []): Characteristic
    {
        $char = Characteristic::create(array_merge([
            'key' => $key,
            'name' => 'Force',
            'type' => 'int',
            'sort_order' => 1,
        ], $overrides));

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '1',
            'max' => '20',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 5),
                'neutral' => array_fill(0, 20, 10),
                'strong' => array_fill(0, 20, 15),
                'very_strong' => array_fill(0, 20, 20),
            ],
            'norms_conditions' => [
                [
                    'characteristic_key' => 'level_creature',
                    'operator' => '>=',
                    'value' => 10,
                    'target' => 'power',
                    'modifier' => 1,
                    'comment' => 'Haut niveau',
                ],
            ],
            'norms_description' => 'Norme de test pour la force.',
        ]);

        return $char;
    }

    public function test_returns_norms_for_valid_characteristic(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms');

        $response->assertOk();
        $response->assertJsonStructure([
            'characteristic' => ['key', 'name', 'icon', 'color'],
            'norms' => ['grid', 'conditions', 'description', 'limits' => ['min', 'max']],
            'power_levels',
            'max_level',
        ]);

        $data = $response->json();
        $this->assertEquals('strength_creature', $data['characteristic']['key']);
        $this->assertCount(5, $data['norms']['grid']);
        $this->assertCount(1, $data['norms']['conditions']);
        $this->assertEquals('Norme de test pour la force.', $data['norms']['description']);
        $this->assertSame('1', $data['norms']['limits']['min']);
        $this->assertSame('20', $data['norms']['limits']['max']);
        $this->assertEquals(20, $data['max_level']);
    }

    public function test_returns_404_for_unknown_characteristic(): void
    {
        $response = $this->getJson('/api/characteristics/unknown_key/norms');
        $response->assertNotFound();
    }

    public function test_returns_null_norms_when_no_grid(): void
    {
        $char = Characteristic::create([
            'key' => 'empty_creature',
            'name' => 'Vide',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
        ]);

        $response = $this->getJson('/api/characteristics/empty_creature/norms');
        $response->assertOk();
        $this->assertNull($response->json('norms'));
    }

    public function test_norms_with_specific_entity(): void
    {
        $char = Characteristic::create([
            'key' => 'str_creature',
            'name' => 'Force',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => 'monster',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 2),
                'weak' => array_fill(0, 20, 4),
                'neutral' => array_fill(0, 20, 8),
                'strong' => array_fill(0, 20, 12),
                'very_strong' => array_fill(0, 20, 16),
            ],
            'norms_description' => 'Normes monstres',
        ]);

        $response = $this->getJson('/api/characteristics/str_creature/norms/monster');
        $response->assertOk();
        $this->assertEquals('Normes monstres', $response->json('norms.description'));
    }

    public function test_power_levels_are_returned(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms');
        $response->assertOk();

        $powerLevels = $response->json('power_levels');
        $this->assertCount(5, $powerLevels);
        $this->assertArrayHasKey('very_weak', $powerLevels);
        $this->assertArrayHasKey('neutral', $powerLevels);
        $this->assertArrayHasKey('very_strong', $powerLevels);
    }

    public function test_rejects_invalid_group_query_parameter(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms?group=invalid');
        $response->assertStatus(422);
        $this->assertStringContainsString('Groupe invalide', (string) $response->json('error'));
    }

    public function test_group_query_parameter_filters_lookup_to_requested_group(): void
    {
        $this->seedCharacteristic();

        // La caractéristique existe en creature ; en forçant group=spell, on ne doit rien trouver.
        $response = $this->getJson('/api/characteristics/strength_creature/norms?group=spell');
        $response->assertOk();
        $this->assertNull($response->json('norms'));
    }
}

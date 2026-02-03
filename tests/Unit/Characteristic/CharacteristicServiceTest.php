<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\EntityCharacteristic;
use App\Services\Characteristic\CharacteristicService;
use Database\Seeders\EntityCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicService.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_MIGRATION_CHARACTERISTICS_DB.md
 */
class CharacteristicServiceTest extends TestCase
{
    use RefreshDatabase;

    private CharacteristicService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(CharacteristicService::class);
        $this->seedEntityCharacteristics();
        $this->service->clearCache();
    }

    private function seedEntityCharacteristics(): void
    {
        $path = base_path('database/seeders/data/entity_characteristics.php');
        if (is_file($path)) {
            (new EntityCharacteristicSeeder)->run();
            return;
        }
        EntityCharacteristic::insert([
            ['entity' => 'class', 'characteristic_key' => 'life', 'name' => 'Points de vie', 'short_name' => 'PV', 'type' => 'int', 'min' => 1, 'max' => 500, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['entity' => 'monster', 'characteristic_key' => 'life', 'name' => 'Points de vie', 'short_name' => 'PV', 'type' => 'int', 'min' => 1, 'max' => 10000, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_get_full_config_returns_characteristics_and_competences(): void
    {
        $full = $this->service->getFullConfig();

        $this->assertArrayHasKey('characteristics', $full);
        $this->assertArrayHasKey('competences', $full);
        $this->assertIsArray($full['characteristics']);
        $this->assertIsArray($full['competences']);
    }

    public function test_get_characteristics_returns_same_as_full_config_characteristics(): void
    {
        $full = $this->service->getFullConfig();
        $characteristics = $this->service->getCharacteristics();

        $this->assertEquals($full['characteristics'], $characteristics);
    }

    public function test_characteristic_has_entities_with_min_max(): void
    {
        $characteristics = $this->service->getCharacteristics();
        $key = array_key_exists('life', $characteristics) ? 'life' : 'rarity';
        $this->assertArrayHasKey($key, $characteristics);

        $def = $characteristics[$key];
        $this->assertArrayHasKey('entities', $def);
        $entities = $def['entities'];
        $this->assertNotEmpty($entities);
        $firstEntity = array_key_first($entities);
        $this->assertArrayHasKey('min', $def['entities'][$firstEntity]);
        $this->assertArrayHasKey('max', $def['entities'][$firstEntity]);
    }

    public function test_get_competences_returns_only_competences(): void
    {
        $competences = $this->service->getCompetences();

        foreach ($competences as $def) {
            $this->assertTrue($def['is_competence'] ?? false);
        }
    }

    public function test_get_characteristic_returns_null_for_unknown_id(): void
    {
        $this->assertNull($this->service->getCharacteristic('unknown_id'));
    }

    public function test_get_characteristic_returns_definition_for_known_id(): void
    {
        $characteristics = $this->service->getCharacteristics();
        $key = array_key_exists('life', $characteristics) ? 'life' : 'rarity';
        $def = $this->service->getCharacteristic($key);
        $this->assertNotNull($def);
        $this->assertArrayHasKey('name', $def);
        $this->assertArrayHasKey('entities', $def);
    }

    public function test_get_limits_returns_min_max_for_entity(): void
    {
        $characteristics = $this->service->getCharacteristics();
        if (array_key_exists('life', $characteristics)) {
            $limits = $this->service->getLimits('life', 'class');
        } else {
            $limits = $this->service->getLimits('rarity', 'resource');
        }
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
        $this->assertIsInt($limits['min']);
        $this->assertIsInt($limits['max']);
    }

    public function test_get_rarity_by_level_uses_computation_when_defined(): void
    {
        $this->assertEquals(0, $this->service->getRarityByLevel(1, 'resource'));
        $this->assertEquals(1, $this->service->getRarityByLevel(5, 'resource'));
        $this->assertEquals(2, $this->service->getRarityByLevel(8, 'resource'));
        $this->assertEquals(4, $this->service->getRarityByLevel(20, 'resource'));
    }

    public function test_get_rarity_by_level_fallback_config_for_unknown_entity(): void
    {
        $this->assertEquals(0, $this->service->getRarityByLevel(1, 'monster'));
    }

    public function test_get_limits_returns_null_for_unknown_characteristic(): void
    {
        $this->assertNull($this->service->getLimits('unknown', 'class'));
    }

    public function test_full_config_is_cached(): void
    {
        $this->service->getFullConfig();
        $this->assertTrue(Cache::has('characteristics.full'));
    }

    public function test_clear_cache_removes_cache(): void
    {
        $this->service->getFullConfig();
        $this->service->clearCache();
        $this->assertFalse(Cache::has('characteristics.full'));
    }
}

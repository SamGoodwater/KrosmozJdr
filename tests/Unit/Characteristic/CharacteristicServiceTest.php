<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\CharacteristicService;
use Database\Seeders\CharacteristicConfigSeeder;
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
        (new CharacteristicConfigSeeder)->run();
        $this->service->clearCache();
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
        $this->assertArrayHasKey('life', $characteristics);

        $life = $characteristics['life'];
        $this->assertArrayHasKey('entities', $life);
        $this->assertArrayHasKey('class', $life['entities']);
        $this->assertArrayHasKey('min', $life['entities']['class']);
        $this->assertArrayHasKey('max', $life['entities']['class']);
    }

    public function test_get_competences_returns_only_competences(): void
    {
        $competences = $this->service->getCompetences();

        $this->assertGreaterThan(0, count($competences));
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
        $def = $this->service->getCharacteristic('life');
        $this->assertNotNull($def);
        $this->assertArrayHasKey('name', $def);
        $this->assertArrayHasKey('entities', $def);
    }

    public function test_get_limits_returns_min_max_for_entity(): void
    {
        $limits = $this->service->getLimits('life', 'class');
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
        $this->assertIsInt($limits['min']);
        $this->assertIsInt($limits['max']);
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

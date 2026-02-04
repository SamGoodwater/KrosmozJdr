<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicGetterService.
 *
 * @see App\Services\Characteristic\Getter\CharacteristicGetterService
 */
class CharacteristicGetterServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private CharacteristicGetterService $getter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->getter = $this->app->make(CharacteristicGetterService::class);
    }

    public function test_get_definition_returns_null_for_unknown_key(): void
    {
        $this->assertNull($this->getter->getDefinition('unknown_key', 'monster'));
    }

    public function test_get_definition_returns_null_for_unknown_entity(): void
    {
        $this->assertNull($this->getter->getDefinition('life_creature', 'unknown_entity'));
    }

    public function test_get_definition_returns_merged_definition_for_creature(): void
    {
        $def = $this->getter->getDefinition('life_creature', 'monster');
        $this->assertNotNull($def);
        $this->assertSame('life_creature', $def['key']);
        $this->assertArrayHasKey('name', $def);
        $this->assertArrayHasKey('min', $def);
        $this->assertArrayHasKey('max', $def);
        $this->assertArrayHasKey('db_column', $def);
        $this->assertArrayHasKey('conversion_formula', $def);
    }

    public function test_get_limits_returns_null_for_unknown_key(): void
    {
        $this->assertNull($this->getter->getLimits('unknown', 'monster'));
    }

    public function test_get_limits_returns_min_max_for_known_characteristic(): void
    {
        $limits = $this->getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
        $this->assertIsInt($limits['min']);
        $this->assertIsInt($limits['max']);
    }

    public function test_get_limits_by_field_resolves_key(): void
    {
        $limits = $this->getter->getLimitsByField('life', 'monster');
        $this->assertNotNull($limits);
        $this->assertArrayHasKey('min', $limits);
        $this->assertArrayHasKey('max', $limits);
    }

    public function test_clear_cache_does_not_throw(): void
    {
        $this->getter->clearCache();
        $this->expectNotToPerformAssertions();
    }
}

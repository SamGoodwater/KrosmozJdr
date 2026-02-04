<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Conversion\DofusConversionService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour DofusConversionService.
 *
 * @see App\Services\Characteristic\Conversion\DofusConversionService
 */
class DofusConversionServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private DofusConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->service = $this->app->make(DofusConversionService::class);
    }

    public function test_convert_level_creature_returns_integer(): void
    {
        $result = $this->service->convertLevel(100, 'monster');
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_level_handles_null(): void
    {
        $result = $this->service->convertLevel(null, 'monster');
        $this->assertIsInt($result);
    }

    public function test_convert_level_object(): void
    {
        $result = $this->service->convertLevel(50, 'item');
        $this->assertIsInt($result);
    }

    public function test_get_rarity_by_level_returns_zero_for_creature_entity(): void
    {
        $this->assertSame(0, $this->service->getRarityByLevel(10, 'monster'));
    }

    public function test_get_rarity_by_level_returns_integer_for_object_entity(): void
    {
        $result = $this->service->getRarityByLevel(10, 'item');
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_life_returns_integer(): void
    {
        $result = $this->service->convertLife(1000, 10, 'monster');
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_life_handles_null_dofus_value(): void
    {
        $result = $this->service->convertLife(null, 5, 'monster');
        $this->assertIsInt($result);
    }

    public function test_convert_attribute_returns_integer(): void
    {
        $result = $this->service->convertAttribute('vitality', 100, 'monster');
        $this->assertIsInt($result);
    }

    public function test_convert_initiative_returns_integer(): void
    {
        $result = $this->service->convertInitiative(50, 'monster');
        $this->assertIsInt($result);
    }

    public function test_clamp_to_limits_returns_value_in_limits(): void
    {
        $getter = $this->app->make(\App\Services\Characteristic\Getter\CharacteristicGetterService::class);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $mid = (int) (($limits['min'] + $limits['max']) / 2);
        $result = $this->service->clampToLimits('life_creature', $mid, 'monster');
        $this->assertSame($mid, $result);
    }

    public function test_convert_resistances_batch_returns_map(): void
    {
        $raw = [
            'grades' => [
                0 => [
                    'neutralResistance' => 5,
                    'earthResistance' => 10,
                    'fireResistance' => 0,
                    'airResistance' => -5,
                    'waterResistance' => null,
                ],
            ],
        ];
        $result = $this->service->convertResistancesBatch($raw, 'monster');
        $this->assertArrayHasKey('res_neutre', $result);
        $this->assertArrayHasKey('res_terre', $result);
        $this->assertArrayHasKey('res_feu', $result);
        $this->assertArrayHasKey('res_air', $result);
        $this->assertArrayHasKey('res_eau', $result);
        $this->assertSame(5, $result['res_neutre']);
        $this->assertSame(10, $result['res_terre']);
    }

    public function test_convert_resistances_batch_returns_empty_when_no_grades(): void
    {
        $result = $this->service->convertResistancesBatch([], 'monster');
        $this->assertSame([], $result);
    }
}

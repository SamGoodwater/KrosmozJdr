<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Characteristic\Limit\ValidationResult;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour CharacteristicLimitService et ValidationResult.
 *
 * @see App\Services\Characteristic\Limit\CharacteristicLimitService
 * @see App\Services\Characteristic\Limit\ValidationResult
 */
class CharacteristicLimitServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private CharacteristicLimitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->service = $this->app->make(CharacteristicLimitService::class);
    }

    public function test_validation_result_ok(): void
    {
        $result = ValidationResult::ok();
        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getErrors());
    }

    public function test_validation_result_fail(): void
    {
        $errors = [
            ['path' => 'life', 'message' => 'life=0 hors limites [1, 100]'],
        ];
        $result = ValidationResult::fail($errors);
        $this->assertFalse($result->isValid());
        $this->assertSame($errors, $result->getErrors());
    }

    public function test_validate_returns_ok_when_data_empty(): void
    {
        $result = $this->service->validate([], 'monster');
        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getErrors());
    }

    public function test_validate_returns_ok_when_values_in_limits(): void
    {
        $getter = $this->app->make(\App\Services\Characteristic\Getter\CharacteristicGetterService::class);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $value = (int) (($limits['min'] + $limits['max']) / 2);
        $result = $this->service->validate(['creatures' => ['life' => $value]], 'monster');
        $this->assertTrue($result->isValid());
    }

    public function test_validate_returns_fail_when_value_below_min(): void
    {
        $result = $this->service->validate(['creatures' => ['life' => -10]], 'monster');
        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('hors limites', $errors[0]['message']);
    }

    public function test_clamp_returns_value_when_in_limits(): void
    {
        $getter = $this->app->make(\App\Services\Characteristic\Getter\CharacteristicGetterService::class);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $mid = (int) (($limits['min'] + $limits['max']) / 2);
        $this->assertSame($mid, $this->service->clamp('life_creature', $mid, 'monster'));
    }

    public function test_clamp_returns_min_when_below(): void
    {
        $getter = $this->app->make(\App\Services\Characteristic\Getter\CharacteristicGetterService::class);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $this->assertSame($limits['min'], $this->service->clamp('life_creature', $limits['min'] - 100, 'monster'));
    }

    public function test_clamp_returns_max_when_above(): void
    {
        $getter = $this->app->make(\App\Services\Characteristic\Getter\CharacteristicGetterService::class);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $this->assertSame($limits['max'], $this->service->clamp('life_creature', $limits['max'] + 100, 'monster'));
    }

    public function test_clamp_returns_value_when_no_limits(): void
    {
        $this->assertSame(42, $this->service->clamp('unknown_key', 42, 'monster'));
    }
}

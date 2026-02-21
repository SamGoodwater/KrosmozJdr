<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour FormatterApplicator (registry, apply, supports).
 *
 * @see App\Services\Scrapping\Core\Conversion\FormatterApplicator
 */
class FormatterApplicatorTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
    }

    public function test_apply_unknown_formatter_returns_value_unchanged(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $result = $applicator->apply('unknown_formatter', 42, [], [], []);

        $this->assertSame(42, $result);
    }

    public function test_supports_returns_false_for_unknown(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $this->assertFalse($applicator->supports('unknown_formatter'));
    }

    public function test_supports_returns_true_for_registered_formatters(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $this->assertTrue($applicator->supports('toString'));
        $this->assertTrue($applicator->supports('toInt'));
        $this->assertTrue($applicator->supports('pickLang'));
        $this->assertTrue($applicator->supports('truncate'));
        $this->assertTrue($applicator->supports('toJson'));
    }

    public function test_apply_toInt_returns_integer(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $this->assertSame(42, $applicator->apply('toInt', 42, [], [], []));
        $this->assertSame(10, $applicator->apply('toInt', '10', [], [], []));
        $this->assertSame(0, $applicator->apply('toInt', 'invalid', [], [], []));
    }

    public function test_apply_toString_returns_string(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $this->assertSame('hello', $applicator->apply('toString', 'hello', [], [], []));
        $this->assertSame('', $applicator->apply('toString', null, [], [], []));
    }

    public function test_apply_clampToCharacteristic_uses_getter_limits(): void
    {
        $getter = $this->app->make(CharacteristicGetterService::class);
        $getter->clearCache();
        $applicator = new FormatterApplicator(null, $getter);

        $result = $applicator->apply('clampToCharacteristic', 500, ['characteristicId' => 'life_creature'], [], ['entityType' => 'monster']);

        $this->assertIsInt($result);
        $limits = $getter->getLimits('life_creature', 'monster');
        $this->assertNotNull($limits);
        $this->assertLessThanOrEqual($limits['max'], $result);
        $this->assertGreaterThanOrEqual($limits['min'], $result);
    }

    public function test_apply_dofusdb_formatters_require_conversion_service(): void
    {
        $applicator = new FormatterApplicator(null, null);

        $this->assertFalse($applicator->supports('dofusdb_level'));
        $this->assertSame(50, $applicator->apply('unknown', 50, [], [], []));
    }
}

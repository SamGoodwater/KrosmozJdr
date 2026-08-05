<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\ConversionDiagnosticBag;
use App\Services\Scrapping\Core\Conversion\ItemEffectsToBonusConverter;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\DofusdbCharacteristicIdSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour ItemEffectsToBonusConverter (intégration : getter réel + BDD seedée).
 *
 * @see App\Services\Scrapping\Core\Conversion\ItemEffectsToBonusConverter
 */
class ItemEffectsToBonusConverterTest extends TestCase
{
    use RefreshDatabase;

    private ItemEffectsToBonusConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CharacteristicSeeder::class, ObjectCharacteristicSeeder::class, DofusdbCharacteristicIdSeeder::class]);
        $getter = $this->app->make(CharacteristicGetterService::class);
        $getter->clearCache();
        $this->converter = new ItemEffectsToBonusConverter($getter, null);
    }

    public function test_convert_returns_null_for_empty_effects(): void
    {
        $result = $this->converter->convert([], [], []);

        $this->assertNull($result);
    }

    public function test_convert_returns_null_for_non_array_value(): void
    {
        $result = $this->converter->convert(null, [], []);

        $this->assertNull($result);
    }

    public function test_convert_resolves_characteristic_and_aggregates(): void
    {
        $effects = [
            ['characteristic' => 10, 'value' => 5],
            ['characteristic' => 15, 'value' => 3],
            ['characteristic' => 10, 'from' => 0, 'to' => 10],
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'item']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame(10, $decoded['strength'] ?? null);
        $this->assertSame(3, $decoded['intelligence'] ?? null);
    }

    public function test_convert_ignores_effects_with_unknown_characteristic_id(): void
    {
        $diagnostics = new ConversionDiagnosticBag;
        $effects = [
            ['characteristic' => 99999, 'value' => 5],
        ];

        $result = $this->converter->convert($effects, [], ['diagnostics' => $diagnostics]);

        $this->assertNull($result);
        $this->assertTrue($diagnostics->requiresManualReview());
    }

    public function test_convert_uses_from_to_when_value_missing(): void
    {
        $effects = [
            ['characteristic' => 10, 'from' => 4, 'to' => 8],
        ];

        $result = $this->converter->convert($effects, [], []);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertSame(6, $decoded['strength'] ?? null);
    }

    public function test_convert_uses_from_when_to_is_zero_for_fixed_value(): void
    {
        $effects = [
            ['characteristic' => 10, 'from' => 5, 'to' => 0],
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertSame(5, $decoded['strength'] ?? null);
    }

    public function test_convert_supports_tiered_panoply_effects(): void
    {
        $effectsByTier = [
            [],
            [
                ['characteristic' => 10, 'from' => 5, 'to' => 0], // force +5
                ['characteristic' => 15, 'from' => 5, 'to' => 0], // intel +5
            ],
            [
                ['characteristic' => 10, 'from' => 10, 'to' => 0], // force +10
            ],
        ];

        $result = $this->converter->convert($effectsByTier, [], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame(5, $decoded['2']['strength'] ?? null);
        $this->assertSame(5, $decoded['2']['intelligence'] ?? null);
        $this->assertSame(10, $decoded['3']['strength'] ?? null);
    }

    public function test_convert_supports_heal_bonus_characteristic(): void
    {
        $effects = [
            ['characteristic' => 49, 'value' => 20], // healBonus (KrosmozJDR)
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('heal_bonus', $decoded);
        $this->assertSame(20, $decoded['heal_bonus']);
    }

    public function test_convert_ignores_percent_resistance_on_individual_items(): void
    {
        $getter = $this->app->make(CharacteristicGetterService::class);
        $conversion = $this->app->make(DofusConversionService::class);
        $converter = new ItemEffectsToBonusConverter($getter, $conversion);

        $result = $converter->convert(
            [['characteristic' => 33, 'value' => 13]],
            [],
            ['entityType' => 'item']
        );

        $this->assertNull($result);
    }

    public function test_convert_maps_percent_resistance_bands_on_panoplies(): void
    {
        $getter = $this->app->make(CharacteristicGetterService::class);
        $conversion = $this->app->make(DofusConversionService::class);
        $converter = new ItemEffectsToBonusConverter($getter, $conversion);

        $effects = [
            ['characteristic' => 33, 'value' => 8],
            ['characteristic' => 34, 'value' => 13],
            ['characteristic' => 35, 'value' => -20],
            ['characteristic' => 36, 'value' => -51],
        ];

        $result = $converter->convert($effects, [], ['entityType' => 'panoply']);
        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['resistance_percent_tier_earth'] ?? null);
        $this->assertSame(2, $decoded['resistance_percent_tier_fire'] ?? null);
        $this->assertSame(-1, $decoded['resistance_percent_tier_water'] ?? null);
        $this->assertSame(-2, $decoded['resistance_percent_tier_air'] ?? null);
    }

    public function test_convert_ignores_ambiguous_characteristic_zero(): void
    {
        $result = $this->converter->convert(
            [['characteristic' => 0, 'effectId' => 1179, 'value' => 20]],
            [],
            ['entityType' => 'item']
        );

        $this->assertNull($result);
    }

    public function test_convert_maps_all_damage_to_multiple_fixed_damage(): void
    {
        $effects = [['characteristic' => 16, 'effectId' => 112, 'value' => 10]];

        $result = $this->converter->convert($effects, [], ['entityType' => 'item']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertSame(10, $decoded['fixed_damage_multiple'] ?? null);
    }

    public function test_convert_ignores_deprecated_characteristics_push(): void
    {
        $effects = [
            ['characteristic' => 84, 'value' => 25], // pushDamageBonus (non utilisé KrosmozJDR)
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'panoply']);

        $this->assertNull($result);
    }

    public function test_convert_logs_id_38_as_unknown_without_polluting_bonus(): void
    {
        $diagnostics = new ConversionDiagnosticBag;
        $effects = [
            ['characteristic' => 10, 'value' => 5],
            ['characteristic' => 38, 'value' => 20],
        ];

        $result = $this->converter->convert($effects, ['id' => 1234], [
            'entityType' => 'panoply',
            'diagnostics' => $diagnostics,
        ]);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame(5, $decoded['strength'] ?? null);
        $this->assertArrayNotHasKey('38', $decoded);
        $this->assertArrayNotHasKey('unknown', $decoded);
        $this->assertTrue($diagnostics->requiresManualReview());
        $this->assertSame(38, $diagnostics->all()[0]['context']['dofusdb_characteristic_id'] ?? null);
    }
}

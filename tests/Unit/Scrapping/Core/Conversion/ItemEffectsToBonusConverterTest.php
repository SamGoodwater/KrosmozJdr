<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\ItemEffectsToBonusConverter;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\DofusdbCharacteristicIdSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
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
        Log::spy();
        $effects = [
            ['characteristic' => 99999, 'value' => 5],
        ];

        $result = $this->converter->convert($effects, [], []);

        $this->assertNull($result);
        Log::shouldHaveReceived('warning')->once();
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

    public function test_convert_supports_newly_mapped_heal_and_power_characteristics(): void
    {
        $effects = [
            ['characteristic' => 49, 'value' => 20], // healBonus
            ['characteristic' => 25, 'value' => 30], // damagePercent
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('heal_bonus', $decoded);
        $this->assertArrayHasKey('power', $decoded);
    }

    public function test_convert_supports_push_and_ap_reduction_characteristics(): void
    {
        $effects = [
            ['characteristic' => 84, 'value' => 25], // pushDamageBonus
            ['characteristic' => 82, 'value' => 12], // apReduction
        ];

        $result = $this->converter->convert($effects, [], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('push_damage_bonus', $decoded);
        $this->assertArrayHasKey('ap_reduction', $decoded);
    }

    public function test_convert_logs_id_38_as_unknown_without_polluting_bonus(): void
    {
        Log::spy();
        $effects = [
            ['characteristic' => 10, 'value' => 5],
            ['characteristic' => 38, 'value' => 20],
        ];

        $result = $this->converter->convert($effects, ['id' => 1234], ['entityType' => 'panoply']);

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertSame(5, $decoded['strength'] ?? null);
        $this->assertArrayNotHasKey('38', $decoded);
        $this->assertArrayNotHasKey('unknown', $decoded);

        Log::shouldHaveReceived('warning')
            ->withArgs(function (string $message, array $context): bool {
                return str_contains($message, 'non mappés ignorés')
                    && (($context['contains_id_38'] ?? false) === true)
                    && ($context['source_id'] ?? null) === 1234;
            })
            ->once();
    }
}

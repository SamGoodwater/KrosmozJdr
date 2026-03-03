<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
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
        $effects = [
            ['characteristic' => 99999, 'value' => 5],
        ];

        $result = $this->converter->convert($effects, [], []);

        $this->assertNull($result);
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
}

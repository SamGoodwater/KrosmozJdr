<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Models\Type\ItemType;
use App\Services\Characteristic\Reference\ForgemagieRuneTableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgemagieRuneTableServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_keeps_only_forgemageable_characteristics(): void
    {
        $amulet = ItemType::factory()->create(['name' => 'Amulette']);

        $this->makeRow('action_points_object', "Bonus de points d'action", [
            'forgemagie_max' => 1,
            'base_price_per_unit' => 1300,
            'rune_price_per_unit' => 2600,
        ], [$amulet->id]);

        // Forgemageable mais sans prix de rune : pas une rune achetable.
        $this->makeRow('hit_bonus_object', 'Bonus de touche', [
            'forgemagie_max' => 2,
            'base_price_per_unit' => 1200,
            'rune_price_per_unit' => null,
        ]);

        // Non forgemageable.
        $this->makeRow('armor_class_object', "Bonus de classe d'armure", [
            'forgemagie_max' => 0,
            'base_price_per_unit' => 1100,
            'rune_price_per_unit' => 2200,
        ]);

        $payload = (new ForgemagieRuneTableService)->build();

        $this->assertSame(1, $payload['meta']['total']);
        $this->assertSame(['action_points_object'], array_column($payload['rows'], 'key'));

        $row = $payload['rows'][0];
        $this->assertSame(1, $row['max_bonus']);
        $this->assertSame(2600.0, $row['rune_price']);
        $this->assertSame(1300.0, $row['base_price']);
        $this->assertSame(['Amulette'], $row['item_types']);
        $this->assertTrue($row['restricted']);
    }

    public function test_row_without_item_type_is_not_restricted(): void
    {
        $this->makeRow('initiative_object', "Bonus d'initiative", [
            'forgemagie_max' => 3,
            'base_price_per_unit' => 100,
            'rune_price_per_unit' => 200,
        ]);

        $row = (new ForgemagieRuneTableService)->build()['rows'][0];

        $this->assertSame([], $row['item_types']);
        $this->assertFalse($row['restricted']);
    }

    public function test_rows_are_deduplicated_by_characteristic_and_sortable(): void
    {
        $this->makeRow('vitality_object', 'Bonus de vitalité', [
            'forgemagie_max' => 2,
            'rune_price_per_unit' => 1200,
        ]);
        $this->makeRow('agility_object', "Bonus d'agilité", [
            'forgemagie_max' => 2,
            'rune_price_per_unit' => 1000,
        ]);

        // Même caractéristique déclinée sur une autre entité : une seule rune attendue.
        $panoply = Characteristic::where('key', 'vitality_object')->firstOrFail();
        CharacteristicObject::create([
            'characteristic_id' => $panoply->id,
            'entity' => 'panoply',
            'forgemagie_max' => 2,
            'rune_price_per_unit' => 1200,
        ]);

        $service = new ForgemagieRuneTableService;

        $byName = $service->build(['sort_by' => 'name']);
        $this->assertSame(2, $byName['meta']['total']);
        $this->assertSame(['agility_object', 'vitality_object'], array_column($byName['rows'], 'key'));

        $byPriceDesc = $service->build(['sort_by' => 'rune_price', 'sort_dir' => 'desc']);
        $this->assertSame(['vitality_object', 'agility_object'], array_column($byPriceDesc['rows'], 'key'));
    }

    /**
     * @param  array<string, mixed>  $pivot
     * @param  list<int>  $itemTypeIds
     */
    private function makeRow(string $key, string $name, array $pivot, array $itemTypeIds = []): CharacteristicObject
    {
        $characteristic = Characteristic::create([
            'key' => $key,
            'name' => $name,
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'object',
        ]);

        $row = CharacteristicObject::create(array_merge([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicObject::ENTITY_ALL,
        ], $pivot));

        if ($itemTypeIds !== []) {
            $row->allowedItemTypes()->attach($itemTypeIds);
        }

        return $row;
    }
}

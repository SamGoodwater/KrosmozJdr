<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicObject;
use App\Models\Type\ItemType;
use App\Services\Characteristic\Reference\EquipmentBonusTableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentBonusTableServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_band_value_uses_greatest_formula_threshold_at_or_below_band_start(): void
    {
        $service = new EquipmentBonusTableService;
        $formula = '{"1":"0","3":"1","5":"2","7":"3","20":"6","characteristic":"level_object"}';

        $this->assertNull($service->bandValueForFormula($formula, 1));
        $this->assertSame(1, $service->bandValueForFormula($formula, 3));
        $this->assertSame(2, $service->bandValueForFormula($formula, 5));
        $this->assertSame(3, $service->bandValueForFormula($formula, 19));
        $this->assertNull($service->bandValueForFormula('0', 5));
        $this->assertNull($service->bandValueForFormula('-', 5));
        $this->assertNull($service->bandValueForFormula(null, 5));
    }

    public function test_build_groups_rows_by_item_type_and_skips_meta_keys(): void
    {
        $cape = ItemType::factory()->create(['name' => 'Cape']);
        $hat = ItemType::factory()->create(['name' => 'Chapeau']);

        $intelligence = Characteristic::create([
            'key' => 'intelligence_object',
            'name' => 'Bonus d\'intelligence',
            'type' => 'int',
            'sort_order' => 1,
            'group' => 'object',
        ]);
        $vitality = Characteristic::create([
            'key' => 'vitality_object',
            'name' => 'Bonus de vitalité',
            'type' => 'int',
            'sort_order' => 2,
            'group' => 'object',
        ]);
        $name = Characteristic::create([
            'key' => 'name_object',
            'name' => 'Nom',
            'type' => 'string',
            'sort_order' => 0,
            'group' => 'object',
        ]);

        $intRow = CharacteristicObject::create([
            'characteristic_id' => $intelligence->id,
            'entity' => CharacteristicObject::ENTITY_ITEM,
            'formula' => '{"1":"0","3":"1","5":"2","characteristic":"level_object"}',
            'base_price_per_unit' => 100,
            'forgemagie_max' => 2,
            'rune_price_per_unit' => 50,
        ]);
        $intRow->allowedItemTypes()->attach($cape->id);

        CharacteristicObject::create([
            'characteristic_id' => $vitality->id,
            'entity' => CharacteristicObject::ENTITY_ALL,
            'formula' => '{"1":"1","characteristic":"level_object"}',
            'base_price_per_unit' => 10,
            'forgemagie_max' => 1,
            'rune_price_per_unit' => 5,
        ]);

        $nameRow = CharacteristicObject::create([
            'characteristic_id' => $name->id,
            'entity' => CharacteristicObject::ENTITY_ITEM,
            'formula' => '{"1":"1","characteristic":"level_object"}',
        ]);
        $nameRow->allowedItemTypes()->attach($hat->id);

        $payload = (new EquipmentBonusTableService)->build();

        $this->assertCount(10, $payload['bands']);
        $this->assertSame('1–2', $payload['bands'][0]['label']);
        $this->assertSame('19–20', $payload['bands'][9]['label']);

        $groupNames = array_column($payload['groups'], 'item_type_name');
        $this->assertSame(['Cape', 'Tous types'], $groupNames);

        $capeGroup = collect($payload['groups'])->firstWhere('item_type_name', 'Cape');
        $this->assertCount(1, $capeGroup['rows']);
        $this->assertSame('intelligence_object', $capeGroup['rows'][0]['key']);
        $this->assertNull($capeGroup['rows'][0]['bands'][0]);
        $this->assertSame(1, $capeGroup['rows'][0]['bands'][1]);
        $this->assertSame(2, $capeGroup['rows'][0]['bands'][2]);
        $this->assertSame(100.0, $capeGroup['rows'][0]['price_per_unit']);
        $this->assertSame(2, $capeGroup['rows'][0]['forgemagie_max']);

        $allTypes = collect($payload['groups'])->firstWhere('item_type_name', 'Tous types');
        $this->assertSame(['vitality_object'], array_column($allTypes['rows'], 'key'));
    }
}

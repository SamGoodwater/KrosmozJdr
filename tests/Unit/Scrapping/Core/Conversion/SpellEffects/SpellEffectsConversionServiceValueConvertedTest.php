<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Models\DofusdbEffectMapping;
use App\Models\Entity\Spell;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Scrapping\Config\DofusDbConditionCatalog;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectsConversionService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vérifie que la conversion des effets de sort remplit value_converted (Phase 3).
 *
 * @see docs/features/effects/README.md
 */
class SpellEffectsConversionServiceValueConvertedTest extends TestCase
{
    use RefreshDatabase;

    private SpellEffectsConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn(['elementId' => 4]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $resolver = new SpellEffectConversionFormulaResolver;
        $dofusConversion = $this->app->make(DofusConversionService::class);
        $characteristicGetter = $this->app->make(CharacteristicGetterService::class);
        $diceNotationService = $this->app->make(DiceNotationService::class);

        $this->service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            $resolver,
            $dofusConversion,
            $characteristicGetter,
            $diceNotationService
        );
    }

    public function test_convert_treats_dofus_dice_fields_as_minimum_and_maximum(): void
    {
        $spellRaw = ['id' => 1, 'name' => ['fr' => 'Test']];
        $levels = [
            [
                'grade' => 1,
                'effects' => [
                    [
                        'effectId' => 96,
                        'order' => 0,
                        'diceNum' => 2,
                        'diceSide' => 6,
                    ],
                ],
                'criticalEffect' => [],
            ],
        ];

        $result = $this->service->convert($spellRaw, $levels, ['lang' => 'fr']);

        $this->assertTrue($result->hasEffects());
        $effects = $result->getEffects();
        $this->assertCount(1, $effects);
        $subEffects = $effects[0]['sub_effects'] ?? [];
        $this->assertNotEmpty($subEffects);
        $params = $subEffects[0]['params'] ?? [];
        $this->assertArrayHasKey('value_formula', $params);
        $this->assertArrayHasKey('value_converted', $params);
        $this->assertSame('(2 + 6) / 2', $params['dofus_value_formula']);
        $this->assertSame(2, $params['value_converted']);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
        $this->assertNotSame('2d6', $params['value_formula']);
        $this->assertMatchesRegularExpression('/^(\d+d\d+(?:\+\d+)?|\d+)$/', (string) $params['dice_formula']);
    }

    public function test_convert_realistic_dofus_damage_range_does_not_multiply_its_bounds(): void
    {
        $result = $this->service->convert(
            ['id' => 1, 'name' => ['fr' => 'Plage DofusDB']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 98,
                    'order' => 0,
                    'effectElement' => 4,
                    'diceNum' => 13,
                    'diceSide' => 18,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('(13 + 18) / 2', $params['dofus_value_formula']);
        $this->assertSame(4, $params['value_converted']);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
    }

    public function test_damage_lines_share_the_pv_based_budget_for_one_cast(): void
    {
        $result = $this->service->convert(
            ['id' => 1, 'name' => ['fr' => 'Budget dégâts']],
            [[
                'grade' => 1,
                'minPlayerLevel' => 200,
                'apCost' => 3,
                'range' => 3,
                'effects' => [
                    [
                        'effectId' => 96,
                        'order' => 0,
                        'effectElement' => 2,
                        'diceNum' => 10,
                        'diceSide' => 14,
                        'zoneDescr' => ['shape' => 80, 'param1' => 0, 'param2' => 0],
                    ],
                    [
                        'effectId' => 98,
                        'order' => 1,
                        'effectElement' => 4,
                        'diceNum' => 20,
                        'diceSide' => 28,
                        'zoneDescr' => ['shape' => 80, 'param1' => 0, 'param2' => 0],
                    ],
                ],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $subEffects = $result->getEffects()[0]['sub_effects'];
        $values = array_map(
            static fn (array $subEffect): int => (int) $subEffect['params']['value_converted'],
            $subEffects
        );

        $this->assertSame(21, array_sum($values));
        $this->assertLessThan($values[1], $values[0]);
        $this->assertSame('neutral', $subEffects[0]['params']['action_budget']['power']);
        $this->assertSame(83, $subEffects[0]['params']['action_budget']['turn_budget']);
        $this->assertSame(21, $subEffects[0]['params']['action_budget']['cast_budget']);
    }

    public function test_convert_fills_value_converted_for_fixed_value(): void
    {
        $spellRaw = ['id' => 2, 'name' => ['fr' => 'Soin']];
        $levels = [
            [
                'grade' => 1,
                'effects' => [
                    [
                        'effectId' => 96,
                        'order' => 0,
                        'value' => 42,
                    ],
                ],
                'criticalEffect' => [],
            ],
        ];

        $result = $this->service->convert($spellRaw, $levels, ['lang' => 'fr']);

        $this->assertTrue($result->hasEffects());
        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertArrayHasKey('value_converted', $params);
        $this->assertSame('42', $params['dofus_value_formula'] ?? null);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
        $this->assertNotSame('42', $params['value_formula']);
        $this->assertIsInt($params['value_converted']);
    }

    public function test_convert_uses_converted_krosmoz_formula_for_critical_effect(): void
    {
        $result = $this->service->convert(
            ['id' => 3, 'name' => ['fr' => 'Critique']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 96,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 10,
                    'diceSide' => 14,
                ]],
                'criticalEffect' => [[
                    'effectId' => 96,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 15,
                    'diceSide' => 20,
                ]],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('(15 + 20) / 2', $params['dofus_value_formula_crit']);
        $this->assertSame($params['dice_formula_crit'], $params['value_formula_crit']);
        $this->assertNotSame($params['dofus_value_formula_crit'], $params['value_formula_crit']);
        $this->assertGreaterThanOrEqual($params['value_converted'], $params['value_converted_crit']);
    }

    public function test_convert_uses_characteristic_key_from_mapping_when_present(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn(['characteristic' => 19]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 116],
            ['sub_effect_slug' => 'booster', 'characteristic_source' => 'characteristic', 'characteristic_key' => 'po']
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 3, 'name' => ['fr' => 'Buff PO']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 116,
                    'order' => 0,
                    'diceNum' => 2,
                    'diceSide' => 0,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('po', $params['characteristic'] ?? null);
        $this->assertArrayHasKey('value_converted', $params);
        $this->assertIsInt($params['value_converted']);
    }

    public function test_convert_resolves_characteristic_key_from_definition_when_missing_in_mapping(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn(['characteristic' => 19]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 116],
            ['sub_effect_slug' => 'booster', 'characteristic_source' => 'characteristic', 'characteristic_key' => null]
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 4, 'name' => ['fr' => 'Debuff PO']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 116,
                    'order' => 0,
                    'diceNum' => 1,
                    'diceSide' => 0,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('range_spell', $params['characteristic'] ?? null);
        $this->assertArrayHasKey('value_converted', $params);
        $this->assertIsInt($params['value_converted']);
    }

    public function test_relative_resistance_removal_keeps_a_valid_krosmoz_tier(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn(['characteristic' => 33]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 215],
            ['sub_effect_slug' => 'retirer', 'characteristic_source' => 'characteristic', 'characteristic_key' => 'res_terre']
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );
        $result = $service->convert(
            ['id' => 4, 'name' => ['fr' => 'Faiblesse']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 215,
                    'order' => 0,
                    'diceNum' => 25,
                    'diceSide' => 0,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('malus', $params['effect_direction']);
        $this->assertSame(50, $params['value_converted']);
        $this->assertSame('50', $params['value_formula']);
        $this->assertContains($params['value_converted'], [-100, -50, 0, 50, 100]);
    }

    public function test_life_steal_uses_its_reduced_action_characteristic(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => '#1 à #2 vol de vie Feu'],
            'elementId' => 1,
        ]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 94],
            ['sub_effect_slug' => 'frapper', 'characteristic_source' => 'element', 'characteristic_key' => null]
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );
        $result = $service->convert(
            ['id' => 5, 'name' => ['fr' => 'Vol de vie']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 94,
                    'order' => 0,
                    'effectElement' => 1,
                    'diceNum' => 10,
                    'diceSide' => 14,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('[dgt]', $params['life_steal_formula']);
        $this->assertSame(2, $params['value_converted']);
        $this->assertSame(2, $params['life_steal_value_converted']);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
    }

    public function test_convert_maps_state_effect_to_state_sub_effect_with_catalog_data(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => 'Etat #3'],
        ]);

        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->with(97, 'fr')->willReturn([
            'id' => 97,
            'name' => ['fr' => 'Indéplaçable'],
            'icon' => 'stateUnshift',
            'img' => 'https://api.dofusdb.fr/img/states/stateUnshift.png',
            'cantBeMoved' => true,
        ]);

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $this->app->make(DofusdbEffectMappingService::class),
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 5, 'name' => ['fr' => 'Etat test']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 950,
                    'order' => 0,
                    'value' => 97,
                    'duration' => 2,
                    'dispellable' => true,
                    'targetMask' => 'C',
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $subEffect = $result->getEffects()[0]['sub_effects'][0] ?? [];
        $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];

        $this->assertSame('s-appliquer-etat', $subEffect['sub_effect_slug'] ?? null);
        $this->assertSame(97, $params['condition_dofusdb_id'] ?? null);
        $this->assertSame('Indéplaçable', $params['condition_name'] ?? null);
        $this->assertSame(2, $params['duration'] ?? null);
        $this->assertSame('2', $params['duration_formula'] ?? null);
        $this->assertTrue((bool) ($params['dispellable'] ?? false));
    }

    public function test_reimport_updates_existing_state_duration_formula(): void
    {
        // Couvert côté IntegrationService : ce test vérifie surtout que la conversion
        // expose duration_formula pour les états hostiles.
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => 'Etat #3'],
        ]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->with(7, 'fr')->willReturn([
            'id' => 7,
            'name' => ['fr' => 'Pesanteur'],
            'cantSwitchPosition' => true,
        ]);

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $this->app->make(DofusdbEffectMappingService::class),
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 7, 'name' => ['fr' => 'Pesanteur sort']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 950,
                    'order' => 0,
                    'value' => 7,
                    'duration' => 3,
                    'targetMask' => 'A',
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $subEffect = $result->getEffects()[0]['sub_effects'][0] ?? [];
        $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
        $this->assertSame('appliquer-etat', $subEffect['sub_effect_slug'] ?? null);
        $this->assertSame(3, $params['duration'] ?? null);
        $this->assertSame('3', $params['duration_formula'] ?? null);
        $this->assertTrue((bool) ($params['condition_flags']['cant_switch_position'] ?? false));
        $this->assertSame(
            Spell::RESOLUTION_SAVING_THROW,
            $result->getSpellResolution()['resolution_mode'] ?? null
        );
        $this->assertSame('strong', $result->getSpellResolution()['save_characteristic_key'] ?? null);
    }

    public function test_shield_effect_uses_protect_action_and_pv_budget(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => '#1 à #2 Bouclier'],
            'characteristic' => 0,
            'boost' => true,
        ]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 1040],
            ['sub_effect_slug' => 'protéger', 'characteristic_source' => 'none', 'characteristic_key' => null]
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 6, 'name' => ['fr' => 'Bouclier']],
            [[
                'grade' => 1,
                'minPlayerLevel' => 100,
                'apCost' => 3,
                'range' => 0,
                'effects' => [[
                    'effectId' => 1040,
                    'order' => 0,
                    'diceNum' => 40,
                    'diceSide' => 60,
                    'zoneDescr' => ['shape' => 80, 'param1' => 0, 'param2' => 0],
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $subEffect = $result->getEffects()[0]['sub_effects'][0] ?? [];
        $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];

        $this->assertSame('protéger', $subEffect['sub_effect_slug'] ?? null);
        $this->assertSame('shield', $params['action_budget']['action'] ?? null);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
        $this->assertGreaterThanOrEqual(1, $params['value_converted']);
    }

    public function test_temporary_hp_effect_uses_dedicated_action_and_heal_budget(): void
    {
        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => 'Gain de #1 points de vie temporaires'],
            'characteristic' => 95,
            'boost' => true,
        ]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 9095],
            ['sub_effect_slug' => 'donner-pv-temporaires', 'characteristic_source' => 'none', 'characteristic_key' => null]
        );
        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $mappingService->clearCache();

        $service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $mappingService,
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );

        $result = $service->convert(
            ['id' => 7, 'name' => ['fr' => 'PV temporaires']],
            [[
                'grade' => 1,
                'minPlayerLevel' => 100,
                'apCost' => 3,
                'range' => 0,
                'effects' => [[
                    'effectId' => 9095,
                    'order' => 0,
                    'diceNum' => 40,
                    'diceSide' => 60,
                    'zoneDescr' => ['shape' => 80, 'param1' => 0, 'param2' => 0],
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $subEffect = $result->getEffects()[0]['sub_effects'][0] ?? [];
        $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];

        $this->assertSame('donner-pv-temporaires', $subEffect['sub_effect_slug'] ?? null);
        $this->assertSame('temp_hp', $params['action_budget']['action'] ?? null);
        $this->assertSame($params['dice_formula'], $params['value_formula']);
        $this->assertGreaterThanOrEqual(1, $params['value_converted']);
        $this->assertSame(
            Spell::RESOLUTION_AUTO_SUCCESS,
            $result->getSpellResolution()['resolution_mode'] ?? null
        );
    }
}

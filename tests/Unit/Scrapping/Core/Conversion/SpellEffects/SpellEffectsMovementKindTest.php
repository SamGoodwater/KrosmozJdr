<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Models\DofusdbEffectMapping;
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
 * Vérifie la classification push/pull/téléport des sous-effets déplacer.
 */
final class SpellEffectsMovementKindTest extends TestCase
{
    use RefreshDatabase;

    public function test_known_dofus_effect_ids_select_the_right_movement_kind_and_cap(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => 5],
            ['sub_effect_slug' => 'déplacer', 'characteristic_source' => 'none', 'characteristic_key' => null]
        );

        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn([
            'description' => ['fr' => 'Repousse de #1 case'],
        ]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);
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
            ['id' => 1, 'name' => ['fr' => 'Repousse']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => 5,
                    'order' => 0,
                    'diceNum' => 8,
                    'diceSide' => 0,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('push', $params['movement_kind'] ?? null);
        $this->assertSame(5, $params['value_converted'] ?? null);
    }
}

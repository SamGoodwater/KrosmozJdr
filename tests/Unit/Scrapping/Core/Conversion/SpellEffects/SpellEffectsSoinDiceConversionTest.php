<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Models\DofusdbEffectMapping;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use App\Services\Scrapping\Config\DofusDbConditionCatalog;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectsConversionService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verrouille la conversion soin (sous-effet soigner, clé soin_spell) : valeur réduite + notation dés
 * à partir des bornes min/max Dofus (diceNum × diceSide), comme pour les dommages.
 *
 * Les sorts de soin DofusDB utilisent les mêmes champs {@see diceNum} / {@see diceSide} que les dégâts
 * sur les instances d’effet (ex. effet « Rend des PV » id 110). Plages typiques : faible 2d4–3d5,
 * moyen 5d6–8d6, fort 10d8+.
 *
 * @see SpellEffectsConversionService
 */
class SpellEffectsSoinDiceConversionTest extends TestCase
{
    use RefreshDatabase;

    private const HEAL_EFFECT_ID = 110;

    private SpellEffectsConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);

        DofusdbEffectMapping::query()->updateOrCreate(
            ['dofusdb_effect_id' => self::HEAL_EFFECT_ID],
            [
                'sub_effect_slug' => 'soigner',
                'characteristic_source' => 'element',
                'characteristic_key' => null,
            ]
        );
        $this->app->make(DofusdbEffectMappingService::class)->clearCache();

        $catalog = $this->createMock(DofusDbEffectCatalog::class);
        $catalog->method('get')->willReturn(['elementId' => 2]);
        $stateCatalog = $this->createMock(DofusDbConditionCatalog::class);
        $stateCatalog->method('get')->willReturn([]);

        $this->service = new SpellEffectsConversionService(
            $catalog,
            $stateCatalog,
            $this->app->make(DofusdbEffectMappingService::class),
            new SpellEffectConversionFormulaResolver,
            $this->app->make(DofusConversionService::class),
            $this->app->make(CharacteristicGetterService::class),
            $this->app->make(DiceNotationService::class)
        );
    }

    /**
     * Faible soin : plage étroite (ex. 2d4 → 2–8 PV en Dofus), notation proche d’un fixe ou petit ndX.
     */
    public function test_soin_faible_plage_etroite_produit_valeur_reduite_et_dice_formula(): void
    {
        $result = $this->service->convert(
            ['id' => 1, 'name' => ['fr' => 'Soin test']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => self::HEAL_EFFECT_ID,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 2,
                    'diceSide' => 4,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertSame('soigner', $result->getEffects()[0]['sub_effects'][0]['sub_effect_slug'] ?? null);
        $this->assertArrayHasKey('value_converted', $params);
        $this->assertLessThanOrEqual(96, $params['value_converted']);
        $this->assertGreaterThanOrEqual(1, $params['value_converted']);
        $this->assertArrayHasKey('dice_formula', $params);
        $this->assertMatchesRegularExpression('/^(\d+d\d+(?:\+\d+)?|\d+)$/', (string) $params['dice_formula']);
    }

    /**
     * Soin moyen : écart relatif modéré → souvent ndX sans modificateur énorme (courbe « medium spread »).
     */
    public function test_soin_moyen_5d6_produit_notation_sans_gros_modificateur(): void
    {
        $result = $this->service->convert(
            ['id' => 2, 'name' => ['fr' => 'Soin moyen']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => self::HEAL_EFFECT_ID,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 5,
                    'diceSide' => 6,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $dice = (string) ($result->getEffects()[0]['sub_effects'][0]['params']['dice_formula'] ?? '');
        $this->assertStringNotContainsString('+26', $dice);
        $this->assertStringNotContainsString('+30', $dice);
        $this->assertMatchesRegularExpression('/^\d+d\d+$/', $dice);
    }

    /**
     * Gros soin : min/max très éloignés → petit n, grand X (forte variance), sans « +y » massif.
     */
    public function test_soin_fort_large_plage_produit_nd_x_seul(): void
    {
        $result = $this->service->convert(
            ['id' => 3, 'name' => ['fr' => 'Gros soin']],
            [[
                'grade' => 6,
                'effects' => [[
                    'effectId' => self::HEAL_EFFECT_ID,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 10,
                    'diceSide' => 8,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $dice = (string) ($result->getEffects()[0]['sub_effects'][0]['params']['dice_formula'] ?? '');
        $this->assertMatchesRegularExpression('/^\d+d\d+$/', $dice);
        $this->assertStringNotContainsString('+', $dice);
    }

    /**
     * Valeur fixe Dofus (diceSide 0) : une seule borne → affichage fixe ou 1d4+… léger.
     */
    public function test_soin_valeur_fixe_dice_side_zero(): void
    {
        $result = $this->service->convert(
            ['id' => 4, 'name' => ['fr' => 'Soin fixe']],
            [[
                'grade' => 1,
                'effects' => [[
                    'effectId' => self::HEAL_EFFECT_ID,
                    'order' => 0,
                    'effectElement' => 2,
                    'diceNum' => 42,
                    'diceSide' => 0,
                ]],
                'criticalEffect' => [],
            ]],
            ['lang' => 'fr']
        );

        $params = $result->getEffects()[0]['sub_effects'][0]['params'] ?? [];
        $this->assertArrayHasKey('dice_formula', $params);
        $this->assertMatchesRegularExpression('/^(\d+d\d+(?:\+\d+)?|\d+)$/', (string) $params['dice_formula']);
    }
}

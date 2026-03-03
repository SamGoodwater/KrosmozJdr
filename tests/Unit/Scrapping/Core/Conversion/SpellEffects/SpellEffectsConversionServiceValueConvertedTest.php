<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Jdr\DiceNotationService;
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
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md
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

        $mappingService = $this->app->make(DofusdbEffectMappingService::class);
        $resolver = new SpellEffectConversionFormulaResolver();
        $dofusConversion = $this->app->make(DofusConversionService::class);
        $characteristicGetter = $this->app->make(CharacteristicGetterService::class);
        $diceNotationService = $this->app->make(DiceNotationService::class);

        $this->service = new SpellEffectsConversionService(
            $catalog,
            $mappingService,
            $resolver,
            $dofusConversion,
            $characteristicGetter,
            $diceNotationService
        );
    }

    public function test_convert_fills_value_converted_for_frapper_with_dice(): void
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
        $this->assertSame('2d6', $params['value_formula']);
        $this->assertIsInt($params['value_converted']);
        $this->assertGreaterThanOrEqual(0, $params['value_converted']);
        // Selon le mapping actif, la formule de dés peut être soit explicitée (dice_formula),
        // soit déjà résolue dans value_converted uniquement.
        if (array_key_exists('dice_formula', $params)) {
            $this->assertMatchesRegularExpression('/^\d+d\d+(\+\d+)?$/', (string) $params['dice_formula']);
        }
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
        $this->assertSame('42', $params['value_formula'] ?? null);
        $this->assertIsInt($params['value_converted']);
    }
}

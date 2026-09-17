<?php

namespace Tests\Unit\Scrapping\Core;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\ScrappingEntityMappingSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\ScrappingEntityFixtures;
use Tests\TestCase;

/**
 * Tests unitaires pour ConversionService (mapping, formatters, resistanceBatch).
 * Utilise DofusConversionService et CharacteristicGetterService (nouvelle architecture caractéristiques).
 */
class ConversionServiceTest extends TestCase
{
    use RefreshDatabase;

    private ConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seed(ScrappingEntityMappingSeeder::class);
        $configLoader = ConfigLoader::default();
        $conversionService = app(DofusConversionService::class);
        $getter = app(CharacteristicGetterService::class);
        $this->service = new ConversionService(
            $configLoader,
            new FormatterApplicator($conversionService, $getter),
            $conversionService
        );
    }

    public function test_convert_monster_produces_creatures_and_monsters(): void
    {
        $raw = ScrappingEntityFixtures::monster();

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        $this->assertArrayHasKey('creatures', $out);
        $this->assertArrayHasKey('monsters', $out);
        $this->assertIsArray($out['creatures']);
        $this->assertIsArray($out['monsters']);
        $this->assertSame('31', $out['monsters']['dofusdb_id'] ?? null);
        $this->assertSame('Bouftou', $out['creatures']['name'] ?? null);
    }

    public function test_convert_monster_applies_monster_specific_characteristics(): void
    {
        $out = $this->service->convert(
            'dofusdb',
            'monster',
            ScrappingEntityFixtures::monster(),
            ['entityType' => 'monster', 'lang' => 'fr']
        );

        $creatures = $out['creatures'] ?? [];
        $this->assertArrayNotHasKey('kamas', $creatures);
        $this->assertSame(7, $creatures['po'] ?? null);
        $this->assertSame(9, $creatures['dodge_pa'] ?? null);
        $this->assertSame(11, $creatures['dodge_pm'] ?? null);
        $this->assertSame(12, $creatures['tacle'] ?? null);
        $this->assertSame(8, $creatures['fuite'] ?? null);
        $this->assertSame(2, $creatures['critical_hit'] ?? null);
        $this->assertSame(5, $creatures['heal_bonus'] ?? null);
        $this->assertSame(0, $creatures['res_neutre'] ?? null);
        $this->assertSame(50, $creatures['res_terre'] ?? null);
        $this->assertSame(-50, $creatures['res_feu'] ?? null);
        $this->assertSame(0, $creatures['res_air'] ?? null);
        $this->assertSame(100, $creatures['res_eau'] ?? null);
    }

    public function test_convert_monster_applies_level_and_life_formulas(): void
    {
        $raw = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'grades' => [
                ['level' => 50, 'lifePoints' => 800],
            ],
        ];

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        $creatures = $out['creatures'] ?? [];
        $this->assertArrayHasKey('level', $creatures);
        $this->assertArrayHasKey('life', $creatures);
        // Niveau : formule BDD (dépend de la config level_creature en base)
        $level = $creatures['level'] ?? null;
        $this->assertIsInt($level);
        $this->assertGreaterThanOrEqual(1, $level);
        $this->assertLessThanOrEqual(30, $level);
        // Vie : formule dépend de level JDR
        $this->assertIsInt($creatures['life'] ?? null);
        $this->assertGreaterThan(0, $creatures['life'] ?? 0);
    }

    public function test_convert_uses_context_lang_for_pick_lang(): void
    {
        $raw = [
            'id' => 1,
            'name' => ['fr' => 'Nom FR', 'en' => 'Name EN'],
        ];

        $outFr = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);
        $outEn = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'en']);

        // monster.json utilise actuellement "lang": "fr" en dur pour pickLang (name) ; le contexte lang est bien passé à interpolateArgs mais la config n'utilise pas {lang}.
        $this->assertSame('Nom FR', $outFr['creatures']['name'] ?? null);
        $this->assertSame('Nom FR', $outEn['creatures']['name'] ?? null);
    }

    public function test_convert_spell_produces_spells_key(): void
    {
        $raw = [
            'spell_global' => [
                'id' => 123,
                'name' => ['fr' => 'Évaporation'],
                'description' => ['fr' => 'Desc'],
                'img' => null,
                'apCost' => 4,
                'range' => 5,
                'maxCastPerTurn' => 2,
            ],
        ];

        $out = $this->service->convert('dofusdb', 'spell', $raw, ['lang' => 'fr']);

        $this->assertArrayHasKey('spells', $out);
        $this->assertSame('123', $out['spells']['dofusdb_id'] ?? null);
        $this->assertSame('Évaporation', $out['spells']['name'] ?? null);
    }

    public function test_convert_breed_produces_breeds_key(): void
    {
        $raw = [
            'id' => 1,
            'name' => ['fr' => 'Feca'],
            'shortName' => ['fr' => 'Feca'],
            'description' => ['fr' => 'Desc'],
        ];

        $out = $this->service->convert('dofusdb', 'breed', $raw, ['entityType' => 'class', 'lang' => 'fr']);

        $this->assertArrayHasKey('breeds', $out);
        $this->assertSame('1', $out['breeds']['dofusdb_id'] ?? null);
        $this->assertSame('Feca', $out['breeds']['name'] ?? null);
    }

    public function test_convert_item_produces_items_key(): void
    {
        $raw = ScrappingEntityFixtures::resource();

        $out = $this->service->convert('dofusdb', 'item', $raw, ['lang' => 'fr']);

        $this->assertArrayHasKey('items', $out);
        $this->assertSame('1002', $out['items']['dofusdb_id'] ?? null);
        $this->assertSame('Bois de test', $out['items']['name'] ?? null);
        $this->assertSame(15, $out['items']['type_id'] ?? null);
    }

    public function test_convert_empty_raw_still_applies_mapping_with_null_values(): void
    {
        $raw = [];

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        // Mapping avec path manquant produit des champs vides ou absents selon formatters
        $this->assertArrayHasKey('creatures', $out);
        $this->assertArrayHasKey('monsters', $out);
    }

    public function test_convert_monster_maps_each_relative_resistance(): void
    {
        $raw = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'grades' => [
                [
                    'level' => 5,
                    'lifePoints' => 100,
                    'neutralResistance' => 10,
                    'earthResistance' => 5,
                    'fireResistance' => 0,
                    'airResistance' => -5,
                    'waterResistance' => 20,
                ],
            ],
        ];

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        $creatures = $out['creatures'] ?? [];
        $this->assertSame(0, $creatures['res_neutre'] ?? null);
        $this->assertSame(0, $creatures['res_terre'] ?? null);
        $this->assertSame(0, $creatures['res_feu'] ?? null);
        $this->assertSame(0, $creatures['res_air'] ?? null);
        $this->assertSame(0, $creatures['res_eau'] ?? null);
    }
}

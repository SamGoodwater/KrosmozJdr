<?php

namespace Tests\Unit\Scrapping\Core;

use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Characteristic\DofusConversion\DofusDbConversionFormulas;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use Tests\TestCase;

/**
 * Tests unitaires pour ConversionService (mapping, formatters, resistanceBatch).
 */
class ConversionServiceTest extends TestCase
{
    private ConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $configLoader = ConfigLoader::default();
        $conversionFormulas = app(DofusDbConversionFormulas::class);
        $this->service = new ConversionService(
            $configLoader,
            new FormatterApplicator($conversionFormulas),
            $conversionFormulas
        );
    }

    public function test_convert_monster_produces_creatures_and_monsters(): void
    {
        $raw = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'raceId' => 1,
            'grades' => [
                ['level' => 5, 'lifePoints' => 100, 'strength' => 10, 'intelligence' => 5, 'agility' => 8, 'chance' => 6],
            ],
        ];

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        $this->assertIsArray($out);
        $this->assertArrayHasKey('creatures', $out);
        $this->assertArrayHasKey('monsters', $out);
        $this->assertIsArray($out['creatures']);
        $this->assertIsArray($out['monsters']);
        $this->assertSame('31', $out['monsters']['dofusdb_id'] ?? null);
        $this->assertSame('Bouftou', $out['creatures']['name'] ?? null);
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
        // Niveau Dofus 50 → JDR 5 (formule BDD ou config k = d/10)
        $this->assertSame(5, $creatures['level'] ?? null);
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

        $this->assertSame('Nom FR', $outFr['creatures']['name'] ?? null);
        $this->assertSame('Name EN', $outEn['creatures']['name'] ?? null);
    }

    public function test_convert_spell_produces_spells_key(): void
    {
        $raw = [
            'id' => 123,
            'name' => ['fr' => 'Évaporation'],
            'description' => ['fr' => 'Desc'],
            'img' => null,
            'levels' => [['apCost' => 4, 'range' => 5, 'maxCastPerTurn' => 2]],
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
        $raw = [
            'id' => 100,
            'name' => ['fr' => 'Bois de frêne'],
            'typeId' => 15,
            'level' => 1,
        ];

        $out = $this->service->convert('dofusdb', 'item', $raw, ['lang' => 'fr']);

        $this->assertArrayHasKey('items', $out);
        $this->assertSame('100', $out['items']['dofusdb_id'] ?? null);
        $this->assertSame('Bois de frêne', $out['items']['name'] ?? null);
        $this->assertSame(15, $out['items']['type_id'] ?? null);
    }

    public function test_convert_empty_raw_still_applies_mapping_with_null_values(): void
    {
        $raw = [];

        $out = $this->service->convert('dofusdb', 'monster', $raw, ['entityType' => 'monster', 'lang' => 'fr']);

        $this->assertIsArray($out);
        // Mapping avec path manquant produit des champs vides ou absents selon formatters
        $this->assertArrayHasKey('creatures', $out);
        $this->assertArrayHasKey('monsters', $out);
    }

    public function test_convert_monster_includes_resistance_batch_when_configured(): void
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
        // resistanceBatch dans monster.json : résistances fusionnées dans creatures
        $this->assertArrayHasKey('creatures', $out);
        // Au moins une résistance mappée (selon handler ou fallback DofusDbConversionFormulas)
        $resKeys = ['res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau'];
        $hasAny = false;
        foreach ($resKeys as $k) {
            if (array_key_exists($k, $creatures)) {
                $hasAny = true;
                break;
            }
        }
        $this->assertTrue($hasAny, 'Au moins une clé res_* doit être présente après resistanceBatch');
    }
}

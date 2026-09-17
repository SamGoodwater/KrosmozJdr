<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi\EquipmentGrid;

use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridAnalyzer;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridCell;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridDefinition;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridHoleFiller;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridItem;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentVoieClassifier;
use PHPUnit\Framework\TestCase;

final class EquipmentGridAnalyzerTest extends TestCase
{
    public function test_committed_config_maps_ring_and_sword_types(): void
    {
        $grid = EquipmentGridDefinition::loadFromFile(
            dirname(__DIR__, 4).'/resources/ia/equipment-grid.json',
            dirname(__DIR__, 4).'/resources/scrapping/config/sources/dofusdb/item-types.json',
        );

        $this->assertSame('ring', $grid->slotKeyForDofusTypeId(9));
        $this->assertSame('weapon', $grid->slotKeyForDofusTypeId(6));
        $this->assertSame('cape', $grid->slotKeyForDofusTypeId(17));
        $this->assertNull($grid->slotKeyForDofusTypeId(12));
        $this->assertSame(2, $grid->bandValue(8));
        $this->assertSame(560, $grid->cellCount());
        $this->assertSame('ia-grid:ring:terre:8', $grid->officialId('ring', 'terre', 8));
        $this->assertNotContains('neutre', $grid->activeVoieKeys());
    }

    public function test_classifier_picks_dominant_elemental_stat(): void
    {
        $grid = $this->tinyGrid();
        $classifier = new EquipmentVoieClassifier($grid);

        $this->assertSame('terre', $classifier->classify(['strength' => 40, 'intelligence' => 10]));
        $this->assertSame('feu', $classifier->classify(['intelligence' => 12, 'vitality' => 80]));
        $this->assertNull($classifier->classify(['vitality' => 6, 'wisdom' => 1]));
    }

    public function test_analyzer_picks_dofus_playable_over_generated_draft(): void
    {
        $grid = $this->tinyGrid();
        $analyzer = new EquipmentGridAnalyzer;
        $report = $analyzer->analyze($grid, [
            new EquipmentGridItem(2, 'Anneau grille', 8, 'draft', 1, 9, null, 'ia-grid:ring:terre:8', ['strength' => 2]),
            new EquipmentGridItem(1, 'Anneau du Bouftou', 8, 'playable', 1, 9, '441', null, ['strength' => 30]),
            new EquipmentGridItem(3, 'Cape hors voie', 8, 'draft', 2, 17, '99', null, ['vitality' => 6]),
        ]);

        $this->assertSame(3, $report->scanned);
        $this->assertSame(1, $report->filled);
        $this->assertSame(3, $report->holes);
        $this->assertSame(1, $report->duplicateCount);
        $this->assertSame(1, $report->unclassified);

        $cell = $this->cell($report->cells, 'ring', 'terre', 8);
        $this->assertFalse($cell->isHole);
        $this->assertSame('Anneau du Bouftou', $cell->representative['name'] ?? null);
        $this->assertSame('Anneau grille', $cell->duplicates[0]['name'] ?? null);

        $hole = $this->cell($report->cells, 'ring', 'feu', 8);
        $this->assertTrue($hole->isHole);
    }

    public function test_hole_template_uses_band_and_slot_category(): void
    {
        $grid = $this->tinyGrid();
        $filler = new EquipmentGridHoleFiller;
        $terre = $grid->voie('terre');
        $this->assertNotNull($terre);
        $this->assertSame(['strength' => 2], $filler->templateBonus($grid, 'accessory', $terre, 8));
        $this->assertSame(['fixed_damage_earth' => 2], $filler->templateBonus($grid, 'weapon', $terre, 8));
    }

    /**
     * @param  list<EquipmentGridCell>  $cells
     */
    private function cell(array $cells, string $slot, string $voie, int $level): EquipmentGridCell
    {
        foreach ($cells as $cell) {
            if ($cell->slotKey === $slot && $cell->voieKey === $voie && $cell->level === $level) {
                return $cell;
            }
        }
        $this->fail("Case {$slot}/{$voie}/{$level} introuvable");
    }

    private function tinyGrid(): EquipmentGridDefinition
    {
        return EquipmentGridDefinition::fromArray([
            'official_id_prefix' => 'ia-grid',
            'name_prefix' => '[Grille]',
            'include_neutral' => false,
            'levels' => ['min' => 8, 'max' => 8],
            'voies' => [
                'terre' => [
                    'label' => 'Terre',
                    'keys' => ['strength', 'fixed_damage_earth'],
                    'primary_key' => 'strength',
                    'damage_key' => 'fixed_damage_earth',
                ],
                'feu' => [
                    'label' => 'Feu',
                    'keys' => ['intelligence', 'fixed_damage_fire'],
                    'primary_key' => 'intelligence',
                    'damage_key' => 'fixed_damage_fire',
                ],
            ],
            'slots' => [
                ['key' => 'ring', 'label' => 'Anneau', 'category' => 'accessory', 'dofusdb_type_ids' => [9]],
                ['key' => 'cape', 'label' => 'Cape', 'category' => 'armor', 'dofusdb_type_ids' => [17]],
            ],
            'bands' => [
                ['from' => 6, 'to' => 10, 'value' => 2],
            ],
        ]);
    }
}

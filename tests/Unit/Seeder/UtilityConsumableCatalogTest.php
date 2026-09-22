<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Consumable\UtilityConsumableCatalog;
use Tests\TestCase;

final class UtilityConsumableCatalogTest extends TestCase
{
    public function test_catalog_lists_ten_priced_entries(): void
    {
        $catalog = UtilityConsumableCatalog::load();
        $entries = $catalog->entries();

        $this->assertCount(5, $catalog->types());
        $this->assertCount(10, $entries);

        $byName = [];
        foreach ($entries as $entry) {
            $byName[$entry['name']] = $entry;
            $this->assertGreaterThan(0, $entry['price']);
            $this->assertNotSame('', $entry['effect']);
        }

        $this->assertSame(800, $byName['Potion de Rappel']['price']);
        $this->assertSame('548', $byName['Potion de Rappel']['dofusdb_id']);
        $this->assertSame(1500, $byName['Antidote']['price']);
        $this->assertSame('jdr:antidote', $byName['Antidote']['official_id']);
        $this->assertSame(200, $byName['Bière d\'Amakna']['price']);
        $this->assertSame(250, $byName['Café']['price']);
        $this->assertSame(3500, $byName['Élixir de réserve de Wakfu']['price']);
        $this->assertSame(10000, $byName['Bonbon de Renaissance du Chanceux']['price']);
        $this->assertSame('12', $byName['Bonbon de Renaissance du Chanceux']['level']);
        $this->assertSame(3, $byName['Bonbon de Renaissance du Chanceux']['rarity']);
        $this->assertSame(800, $byName['Potion de bouclier']['price']);
        $this->assertSame('{"shield_points":5}', $byName['Potion de bouclier']['bonus']);
        $this->assertSame(2500, $byName['Potion de grand bouclier']['price']);
        $this->assertSame('{"shield_points":10}', $byName['Potion de grand bouclier']['bonus']);
        $this->assertSame(800, $byName['Friandise de vitalité']['price']);
        $this->assertSame('{"temporary_life_points":5}', $byName['Friandise de vitalité']['bonus']);
        $this->assertSame(2500, $byName['Élixir de vie temporaire']['price']);
        $this->assertSame('{"temporary_life_points":10}', $byName['Élixir de vie temporaire']['bonus']);
        $this->assertSame('{"deception":1}', $byName['Bière d\'Amakna']['bonus']);
        $this->assertSame('{"investigation":1}', $byName['Café']['bonus']);
        $this->assertSame('{"wakfu_recharge":2}', $byName['Élixir de réserve de Wakfu']['bonus']);
        $this->assertNull($byName['Potion de Rappel']['bonus']);
    }
}

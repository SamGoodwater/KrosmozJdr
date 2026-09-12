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
        $this->assertSame(2500, $byName['Potion de grand bouclier']['price']);
        $this->assertSame(800, $byName['Friandise de vitalité']['price']);
        $this->assertSame(2500, $byName['Élixir de vie temporaire']['price']);
    }
}

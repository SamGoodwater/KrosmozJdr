<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use Tests\TestCase;

final class ClassLevel1SpellCatalogTest extends TestCase
{
    public function test_iop_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load();

        $this->assertSame('Iop', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots));
        foreach ($slots as $orders) {
            sort($orders);
            $this->assertSame([0, 1], $orders);
        }

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Pression', 'Attaque Naturelle', 'Fendoir', 'Intimidation', 'Bond', 'Concentration'],
            $names
        );
        $this->assertSame('jdr:attaque-naturelle', $entries[1]['official_id']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
    }

    public function test_cra_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::craPath());

        $this->assertSame('Crâ', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots));
        foreach ($slots as $orders) {
            sort($orders);
            $this->assertSame([0, 1], $orders);
        }

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Flèche Cinglante', 'Flèche Glacée', 'Flèche Explosive', 'Tir Perforant', 'Œil de Lynx', 'Tir de Recul'],
            $names
        );
        $this->assertSame('jdr:oeil-de-lynx', $entries[4]['official_id']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('2', $entries[0]['po_min']);
        $this->assertTrue($entries[0]['po_editable']);
    }

    public function test_load_all_finds_iop_and_cra(): void
    {
        $catalogs = ClassLevel1SpellCatalog::loadAllInDirectory();
        $breeds = array_map(static fn (ClassLevel1SpellCatalog $c): string => $c->breedName(), $catalogs);
        sort($breeds);
        $this->assertSame(['Crâ', 'Iop'], $breeds);
    }
}

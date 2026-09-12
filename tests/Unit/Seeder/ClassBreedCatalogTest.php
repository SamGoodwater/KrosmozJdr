<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Breed\ClassBreedCatalog;
use Tests\TestCase;

final class ClassBreedCatalogTest extends TestCase
{
    public function test_load_all_returns_sacrieur_then_pandawa(): void
    {
        $catalogs = ClassBreedCatalog::loadAllInDirectory();
        $names = array_map(
            static fn (ClassBreedCatalog $catalog): string => (string) ($catalog->entry()['name'] ?? ''),
            $catalogs
        );

        $this->assertSame(['Sacrieur', 'Pandawa'], $names);
    }

    public function test_sacrieur_catalog_has_tank_and_protection_voices(): void
    {
        $entry = ClassBreedCatalog::load(ClassBreedCatalog::sacrieurPath())->entry();

        $this->assertNotNull($entry);
        $this->assertSame('Sacrieur', $entry['name']);
        $this->assertSame('11', $entry['dofusdb_id']);
        $this->assertSame('Berserker', $entry['description_fast']);
        $this->assertSame('draft', $entry['state']);
        $this->assertLessThanOrEqual(ClassBreedCatalog::DESCRIPTION_MAX, mb_strlen((string) $entry['description']));
        $this->assertStringStartsWith('Les Sacrieurs sont des berserkers', (string) $entry['description']);
        $this->assertSame([
            'earth' => 'tank',
            'air' => 'tank',
            'water' => 'protection',
        ], $entry['element_orientations']);
    }

    public function test_pandawa_catalog_has_tank_placement_and_damage_voices(): void
    {
        $entry = ClassBreedCatalog::load(ClassBreedCatalog::pandawaPath())->entry();

        $this->assertNotNull($entry);
        $this->assertSame('Pandawa', $entry['name']);
        $this->assertSame('12', $entry['dofusdb_id']);
        $this->assertSame('Bagarreur assoiffé', $entry['description_fast']);
        $this->assertSame([
            'earth' => 'tank',
            'air' => 'placement',
            'fire' => 'degats',
        ], $entry['element_orientations']);
    }
}

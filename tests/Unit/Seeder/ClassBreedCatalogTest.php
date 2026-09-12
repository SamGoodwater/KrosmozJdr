<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Breed\ClassBreedCatalog;
use Tests\TestCase;

final class ClassBreedCatalogTest extends TestCase
{
    public function test_load_all_returns_twelve_base_classes_in_dofus_order(): void
    {
        $catalogs = ClassBreedCatalog::loadAllInDirectory();
        $names = array_map(
            static fn (ClassBreedCatalog $catalog): string => (string) ($catalog->entry()['name'] ?? ''),
            $catalogs
        );

        $this->assertSame(ClassBreedCatalog::BASE_CLASS_NAMES, $names);
        $this->assertCount(12, ClassBreedCatalog::PREFERRED_FILES);
    }

    public function test_each_base_class_has_three_voices_from_rules(): void
    {
        $expected = [
            'Féca' => ['fire' => 'protection', 'earth' => 'tank', 'water' => 'amelioration'],
            'Osamodas' => ['fire' => 'invocation', 'earth' => 'protection', 'air' => 'degats'],
            'Enutrof' => ['water' => 'entrave', 'fire' => 'degats', 'earth' => 'placement'],
            'Sram' => ['earth' => 'entrave', 'air' => 'placement', 'water' => 'degats'],
            'Xélor' => ['earth' => 'entrave', 'water' => 'entrave', 'fire' => 'degats'],
            'Ecaflip' => ['fire' => 'degats', 'water' => 'degats', 'air' => 'degats'],
            'Eniripsa' => ['water' => 'soin', 'air' => 'soin', 'fire' => 'soin'],
            'Iop' => ['earth' => 'degats', 'fire' => 'degats', 'air' => 'degats'],
            'Crâ' => ['air' => 'degats', 'water' => 'degats', 'fire' => 'degats'],
            'Sadida' => ['water' => 'invocation', 'fire' => 'degats', 'earth' => 'entrave'],
            'Sacrieur' => ['earth' => 'tank', 'air' => 'tank', 'water' => 'protection'],
            'Pandawa' => ['earth' => 'tank', 'air' => 'placement', 'fire' => 'degats'],
        ];

        foreach (ClassBreedCatalog::loadAllInDirectory() as $catalog) {
            $entry = $catalog->entry();
            $this->assertNotNull($entry);
            $name = $entry['name'];
            $this->assertArrayHasKey($name, $expected);
            $this->assertSame($expected[$name], $entry['element_orientations'], $name);
            $this->assertLessThanOrEqual(ClassBreedCatalog::DESCRIPTION_MAX, mb_strlen((string) $entry['description']));
        }
    }

    public function test_sacrieur_catalog_keeps_berserker_identity(): void
    {
        $entry = ClassBreedCatalog::load(ClassBreedCatalog::sacrieurPath())->entry();

        $this->assertNotNull($entry);
        $this->assertSame('Sacrieur', $entry['name']);
        $this->assertSame('11', $entry['dofusdb_id']);
        $this->assertSame('Berserker', $entry['description_fast']);
        $this->assertSame('draft', $entry['state']);
        $this->assertStringStartsWith('Les Sacrieurs sont des berserkers', (string) $entry['description']);
    }

    public function test_feca_and_iop_catalogs_match_dofus_ids(): void
    {
        $feca = ClassBreedCatalog::load(ClassBreedCatalog::fecaPath())->entry();
        $iop = ClassBreedCatalog::load(ClassBreedCatalog::iopPath())->entry();

        $this->assertSame('Féca', $feca['name'] ?? null);
        $this->assertSame('1', $feca['dofusdb_id'] ?? null);
        $this->assertSame('Protecteur', $feca['description_fast'] ?? null);
        $this->assertSame('Iop', $iop['name'] ?? null);
        $this->assertSame('8', $iop['dofusdb_id'] ?? null);
        $this->assertSame('Guerrier téméraire', $iop['description_fast'] ?? null);
    }
}

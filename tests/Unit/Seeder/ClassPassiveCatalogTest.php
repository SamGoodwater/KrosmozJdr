<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Breed\ClassBreedCatalog;
use App\Services\Seeder\Capability\ClassPassiveCatalog;
use Tests\TestCase;

final class ClassPassiveCatalogTest extends TestCase
{
    public function test_catalog_has_one_passive_per_base_class_in_order(): void
    {
        $entries = ClassPassiveCatalog::load()->entries();
        $breeds = array_column($entries, 'breed');
        $names = array_column($entries, 'name');

        $this->assertSame(ClassBreedCatalog::BASE_CLASS_NAMES, $breeds);
        $this->assertCount(19, $names);
        $this->assertCount(19, array_unique($names));
        $this->assertContains('Tête brûlée', $names);
        $this->assertContains('Contraste', $names);
    }

    public function test_validated_classes_have_capabilities_at_levels_3_9_14(): void
    {
        $byBreed = array_column(ClassPassiveCatalog::load()->entries(), null, 'breed');

        foreach (['Crâ', 'Iop', 'Ecaflip'] as $breed) {
            $levels = array_column($byBreed[$breed]['capabilities'], 'level');
            $this->assertSame([3, 9, 14], $levels, $breed);
        }
        $this->assertSame('Fureur', $byBreed['Iop']['capabilities'][0]['name']);
        $this->assertSame('La pièce', $byBreed['Ecaflip']['capabilities'][0]['previous_name']);
    }
}

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
        $this->assertContains('Fureur', $names);
        $this->assertContains('Contraste', $names);
    }
}

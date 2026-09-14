<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Monster\ClassSummonCatalog;
use Tests\TestCase;

final class ClassSummonCatalogTest extends TestCase
{
    public function test_catalog_has_nineteen_unique_summons(): void
    {
        $entries = ClassSummonCatalog::load()->entries();
        $keys = array_column($entries, 'key');
        $officialIds = array_column($entries, 'official_id');

        $this->assertCount(19, $entries);
        $this->assertCount(19, array_unique($keys));
        $this->assertCount(19, array_unique($officialIds));
        $this->assertContains('tofu', $keys);
        $this->assertContains('harponneuse', $keys);
        $this->assertContains('poupee-sadida', $keys);
        $this->assertSame('jdr:summon:tofu', $entries[0]['official_id']);
        $this->assertSame('jdr:summon:tofu:action', $entries[0]['action_official_id']);
        $this->assertSame('frapper', $entries[0]['action']['kind']);
        $this->assertSame('1d4', $entries[0]['action']['value']);
    }

    public function test_healers_use_soigner(): void
    {
        $byKey = [];
        foreach (ClassSummonCatalog::load()->entries() as $entry) {
            $byKey[$entry['key']] = $entry;
        }

        $this->assertSame('soigner', $byKey['gardienne']['action']['kind']);
        $this->assertSame('soigner', $byKey['sadida-la-gonflable']['action']['kind']);
        $this->assertSame('soigner', $byKey['steamer-tourelle-tactique-soin']['action']['kind']);
        $this->assertSame('1d4', $byKey['gardienne']['action']['value']);
        $this->assertSame('1d6', $byKey['osamodas-bouftou']['action']['value']);
        $this->assertSame('2d4', $byKey['osamodas-sulfenix']['action']['value']);
    }
}

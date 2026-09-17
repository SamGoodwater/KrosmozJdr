<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Monster\BestiaryCatalog;
use Tests\TestCase;

final class BestiaryCatalogTest extends TestCase
{
    public function test_loads_amakna_and_incarnam_catalogs(): void
    {
        $catalogs = BestiaryCatalog::loadAll();
        $this->assertCount(2, $catalogs);

        $keys = [];
        foreach ($catalogs as $catalog) {
            foreach ($catalog->entries() as $entry) {
                $keys[] = $entry['key'];
            }
        }

        $this->assertCount(28, $keys);
        $this->assertContains('tofu-chimerique', $keys);
        $this->assertContains('tofu', $keys);
        $this->assertContains('piou-rouge', $keys);
        $this->assertContains('gelee-bleuet', $keys);
        $this->assertContains('chafer', $keys);

        $amakna = BestiaryCatalog::load(BestiaryCatalog::filePath('amakna.json'));
        $this->assertCount(14, $amakna->entries());
        $tofu = collect($amakna->entries())->firstWhere('key', 'tofu');
        $this->assertSame('jdr:bestiary:tofu', $tofu['official_id']);
        $this->assertSame('Astrub', $tofu['location'] ?? null);
        $this->assertSame('4', $tofu['stats']['pa'] ?? null);
        $this->assertCount(1, $tofu['spells']);
        $this->assertSame('Béco-béco', $tofu['spells'][0]['name']);
        $this->assertSame('https://api.dofusdb.fr/img/monsters/9.png', $tofu['image'] ?? null);
    }
}

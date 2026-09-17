<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Monster\IncarnamBestiaryCatalog;
use Tests\TestCase;

final class IncarnamBestiaryCatalogTest extends TestCase
{
    public function test_loads_fourteen_incarnam_entries(): void
    {
        $entries = IncarnamBestiaryCatalog::load()->entries();

        $this->assertCount(14, $entries);
        $keys = array_column($entries, 'key');
        $this->assertContains('tofu-chimerique', $keys);
        $this->assertContains('piou-vert', $keys);
        $this->assertContains('moskito', $keys);

        $tofu = collect($entries)->firstWhere('key', 'tofu-chimerique');
        $this->assertSame('jdr:bestiary:tofu-chimerique', $tofu['official_id']);
        $this->assertSame(3, $tofu['hostility'] ?? null);
        $this->assertSame('4', $tofu['stats']['pa'] ?? null);
        $this->assertCount(1, $tofu['spells']);
        $this->assertSame('Béco du Tofu', $tofu['spells'][0]['name']);
    }
}

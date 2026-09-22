<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Consumable\HealingConsumableCatalog;
use Tests\TestCase;

final class HealingConsumableCatalogTest extends TestCase
{
    public function test_catalog_has_eleven_tiers_and_four_kinds(): void
    {
        $catalog = HealingConsumableCatalog::load();
        $tiers = $catalog->tiers();
        $entries = $catalog->entries();

        $this->assertCount(11, $tiers);
        $this->assertSame(
            [1, 3, 5, 7, 10, 12, 15, 17, 20, 25, 30],
            array_column($tiers, 'heal')
        );
        $this->assertSame(
            [20, 100, 300, 600, 1000, 1500, 2000, 3500, 5000, 7000, 10000],
            array_column($tiers, 'price')
        );
        $this->assertSame(10, $catalog->recipeQuantity());
        $this->assertCount(44, $entries);

        foreach (HealingConsumableCatalog::KINDS as $kind) {
            $ofKind = array_values(array_filter(
                $entries,
                static fn (array $entry): bool => $entry['kind'] === $kind
            ));
            $this->assertCount(11, $ofKind, $kind);
        }

        foreach ($entries as $entry) {
            $this->assertSame(
                intdiv($entry['price'], $catalog->recipeQuantity()),
                $entry['resource_price']
            );
            $this->assertSame(
                $entry['price'],
                $entry['resource_price'] * $catalog->recipeQuantity()
            );
            $this->assertNotSame('', $entry['resource_dofusdb_id']);
            $this->assertSame(
                HealingConsumableCatalog::effectText($entry['heal']),
                sprintf('Restaure %d PV. Hors combat uniquement.', $entry['heal'])
            );
            $this->assertSame(
                HealingConsumableCatalog::bonusJson($entry['heal']),
                sprintf('{"life_points_restore":%d}', $entry['heal'])
            );
        }
    }
}

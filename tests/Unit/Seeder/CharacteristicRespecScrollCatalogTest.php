<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Consumable\CharacteristicRespecScrollCatalog;
use Tests\TestCase;

final class CharacteristicRespecScrollCatalogTest extends TestCase
{
    public function test_catalog_has_four_tiers_and_twenty_four_scrolls(): void
    {
        $catalog = CharacteristicRespecScrollCatalog::load();
        $tiers = $catalog->tiers();
        $entries = $catalog->entries();

        $this->assertCount(4, $tiers);
        $this->assertSame([1, 2, 3, 4], array_column($tiers, 'points'));
        $this->assertSame([1000, 3000, 5000, 10000], array_column($tiers, 'price'));
        $this->assertCount(24, $entries);
        $this->assertCount(6, $catalog->characteristics());
        $this->assertSame(76, $catalog->consumableTypeDofusId());

        foreach ($catalog->characteristics() as $key => $meta) {
            $ofChar = array_values(array_filter(
                $entries,
                static fn (array $entry): bool => $entry['characteristic'] === $key
            ));
            $this->assertCount(4, $ofChar, $key);
        }

        $petitChance = array_values(array_filter(
            $entries,
            static fn (array $entry): bool => $entry['dofusdb_id'] === '809'
        ))[0] ?? null;
        $this->assertNotNull($petitChance);
        $this->assertSame(1, $petitChance['points']);
        $this->assertSame(1000, $petitChance['price']);
        $this->assertSame(
            CharacteristicRespecScrollCatalog::effectText(1, 'de Chance'),
            'Retire 1 point de Chance déjà réparti et place-le sur une autre caractéristique (plafonds habituels). Hors combat. Usage unique.'
        );
        $this->assertSame(
            CharacteristicRespecScrollCatalog::effectText(4, 'd\'Intelligence'),
            'Retire 4 points d\'Intelligence déjà répartis et place-les sur une ou plusieurs autres caractéristiques (plafonds habituels). Hors combat. Usage unique.'
        );
    }
}

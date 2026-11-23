<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Item\ApplyDraftItemDescriptions;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use App\Services\Seeder\Item\ItemSeederImporter;
use App\Support\Seeder\SeedMode;
use Illuminate\Database\Seeder;

/**
 * Équipements versionnés (étalons JDR relus à la main), lus depuis
 * database/seeders/data/entities/items. Le scrapping DofusDB reste la source du gros du catalogue ;
 * ces fichiers ne portent que les items validés qu'on veut pouvoir rejouer à l'identique.
 */
class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $files = app(ItemSeederFileRepository::class);
        if ($files->paths() === []) {
            return;
        }

        $result = app(ItemSeederImporter::class)->import(overwrite: SeedMode::overwrite());
        $described = 0;
        if (SeedMode::overwrite()) {
            $described = app(ApplyDraftItemDescriptions::class)->apply();
        }

        $this->command?->info(sprintf(
            '  ItemSeeder : %d création(s), %d mise(s) à jour, %d ignoré(s), %d description(s) de brouillon.',
            count($result['created']),
            count($result['updated']),
            count($result['skipped']),
            $described
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

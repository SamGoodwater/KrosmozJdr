<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Resource\EnsureMinimumPlayableResourcePrice;
use App\Services\Seeder\Resource\MarkPlayableItemRecipeResources;
use App\Support\Seeder\SeedMode;
use Illuminate\Database\Seeder;

/**
 * Marque jouables les ressources des recettes d’équipements déjà `playable`,
 * puis pose un plancher de 1 kama sur les fiches jouables encore à 0.
 *
 * Ne tourne qu’en mode overwrite : ces opérations modifient des fiches existantes.
 */
class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        if (! SeedMode::overwrite()) {
            $this->command?->info(
                '  ResourceSeeder : ignoré (création seule ; passez --overwrite pour marquer jouables / plancher kamas).'
            );

            return;
        }

        $playable = app(MarkPlayableItemRecipeResources::class)->mark();
        $floored = app(EnsureMinimumPlayableResourcePrice::class)->apply();

        $this->command?->info(sprintf(
            '  ResourceSeeder : %d ressource(s) passée(s) en jouable, %d prix porté(s) à 1 kama.',
            $playable,
            $floored
        ));
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Resource\MarkPlayableItemRecipeResources;
use Illuminate\Database\Seeder;

/**
 * Marque jouables les ressources des recettes d’équipements déjà `playable`.
 * Ne crée pas de fiches : le scrapping DofusDB reste la source des ressources.
 */
class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $count = app(MarkPlayableItemRecipeResources::class)->mark();

        $this->command?->info(sprintf(
            '  ResourceSeeder : %d ressource(s) passée(s) en jouable.',
            $count
        ));
    }
}

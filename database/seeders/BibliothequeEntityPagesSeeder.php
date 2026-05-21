<?php

namespace Database\Seeders;

use App\Console\Concerns\WritesArtisanCommandOutput;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Crée ou met à jour les sous-pages menu « Bibliothèques » par classe et spécialisation.
 *
 * Délègue à {@see SyncBibliothequeEntityPagesCommand}.
 */
class BibliothequeEntityPagesSeeder extends Seeder
{
    use WritesArtisanCommandOutput;

    public function run(): void
    {
        $code = Artisan::call('pages:sync-bibliotheque-entities');
        $this->writeArtisanCommandOutput();

        if ($code !== 0) {
            $this->command?->error('Échec de pages:sync-bibliotheque-entities.');
        }
    }
}

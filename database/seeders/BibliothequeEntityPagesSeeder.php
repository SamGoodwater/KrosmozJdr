<?php

namespace Database\Seeders;

use App\Services\BibliothequeEntityPageService;
use Illuminate\Database\Seeder;

/**
 * Crée ou met à jour les sous-pages menu « Bibliothèques » par classe et spécialisation.
 */
class BibliothequeEntityPagesSeeder extends Seeder
{
    public function run(): void
    {
        $stats = app(BibliothequeEntityPageService::class)->syncAll();

        $this->command?->info(sprintf(
            'Bibliothèque : %d classes, %d spécialisations (%d entrées menu retirées).',
            $stats['breeds'],
            $stats['specializations'],
            $stats['removed']
        ));
    }
}

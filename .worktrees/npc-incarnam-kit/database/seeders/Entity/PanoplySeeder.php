<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Panoply\PanoplySeederFileRepository;
use App\Services\Seeder\Panoply\PanoplySeederImporter;
use Illuminate\Database\Seeder;

/**
 * Panoplies versionnées, lues depuis database/seeders/data/entities/panoplies.
 * À lancer après ItemSeeder : les pièces sont reliées par `dofusdb_id`.
 */
class PanoplySeeder extends Seeder
{
    public function run(): void
    {
        $files = app(PanoplySeederFileRepository::class);
        if ($files->paths() === []) {
            return;
        }

        $result = app(PanoplySeederImporter::class)->import();

        $this->command?->info(sprintf(
            '  PanoplySeeder : %d création(s), %d mise(s) à jour, %d ignoré(s).',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

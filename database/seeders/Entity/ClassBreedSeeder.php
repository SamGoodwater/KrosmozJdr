<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Breed\ClassBreedSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Fiches des 19 classes (voix §2.3.1) avant les kits de sorts.
 */
class ClassBreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = app(ClassBreedSeederImporter::class)->import();
        $this->command?->info(sprintf(
            '  ClassBreedSeeder : %d création(s), %d mise(s) à jour, %d avertissement(s).',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

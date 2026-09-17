<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Capability\ClassPassiveSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Passifs des 19 classes (après les fiches breed, avant ou après les sorts).
 */
class CapabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = app(ClassPassiveSeederImporter::class)->import();
        $this->command?->info(sprintf(
            '  CapabilitySeeder (passifs de classe) : %d création(s), %d mise(s) à jour, %d avertissement(s).',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

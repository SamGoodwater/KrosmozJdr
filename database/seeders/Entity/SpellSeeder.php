<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Spell\ClassLevel1SpellSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Sorts de classe JDR (kit niveau 1 + progression jusqu’au niveau 12).
 */
class SpellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = app(ClassLevel1SpellSeederImporter::class)->import();
        $this->command?->info(sprintf(
            '  SpellSeeder (classe) : %d création(s), %d mise(s) à jour, %d avertissement(s).',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

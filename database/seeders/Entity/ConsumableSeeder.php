<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Consumable\HealingConsumableSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Échelle JDR de soins hors combat (pain, poisson, viande, potion).
 * Rejoue `database/seeders/data/entities/consumables/healing-out-of-combat.json`.
 */
class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = app(HealingConsumableSeederImporter::class)->import();

        $this->command?->info(sprintf(
            '  ConsumableSeeder : %d ressource(s), %d création(s), %d mise(s) à jour, %d avertissement(s).',
            $result['resources'],
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

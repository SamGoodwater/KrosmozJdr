<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Consumable\CharacteristicRespecScrollSeederImporter;
use App\Services\Seeder\Consumable\HealingConsumableSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Consommables JDR : soins hors combat, puis parchemins de caractéristique (respec).
 */
class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $healing = app(HealingConsumableSeederImporter::class)->import();
        $this->report('soins hors combat', $healing);

        $scrolls = app(CharacteristicRespecScrollSeederImporter::class)->import();
        $this->report('parchemins de caractéristique', $scrolls);
    }

    /**
     * @param  array{
     *     resources?: int,
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }  $result
     */
    private function report(string $label, array $result): void
    {
        $this->command?->info(sprintf(
            '  ConsumableSeeder (%s) : %s%d création(s), %d mise(s) à jour, %d avertissement(s).',
            $label,
            isset($result['resources']) ? $result['resources'].' ressource(s), ' : '',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Npc\NpcSeederImporter;
use Illuminate\Database\Seeder;

/**
 * PNJ JDR jouables (JSON `entities/npcs/incarnam.json`).
 *
 * À jouer **après** items, classes, sorts et spécialisations (`project:seed`).
 */
class NpcSeeder extends Seeder
{
    public function run(): void
    {
        $result = app(NpcSeederImporter::class)->import();
        $this->command?->info(sprintf(
            '  NpcSeeder : %d création(s), %d mise(s) à jour, %d retrait(s), %d avertissement(s).',
            count($result['created']),
            count($result['updated']),
            count($result['retired']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
        foreach ($result['retired'] as $name) {
            $this->command?->comment('    retiré : '.$name);
        }
    }
}

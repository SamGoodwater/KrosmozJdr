<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Monster\ClassSummonSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Invocations de classe (fiches monstres `playable` + 1 sort-créature).
 *
 * À jouer **avant** `SpellSeeder` pour que `invoquer` résolve `monster_id`.
 */
class MonsterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $result = app(ClassSummonSeederImporter::class)->import();
        $this->command?->info(sprintf(
            '  MonsterSeeder (invocations) : %d création(s), %d mise(s) à jour, %d avertissement(s).',
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

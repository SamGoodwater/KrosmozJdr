<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Services\Seeder\Monster\BestiarySeederImporter;
use App\Services\Seeder\Monster\ClassSummonSeederImporter;
use Illuminate\Database\Seeder;

/**
 * Invocations de classe + bestiaire JDR (`auto`).
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
        $summons = app(ClassSummonSeederImporter::class)->import();
        $this->report('invocations', $summons);

        $bestiary = app(BestiarySeederImporter::class)->import();
        $this->report('bestiaire', $bestiary);
    }

    /**
     * @param  array{created: list<string>, updated: list<string>, skipped: list<string>}  $result
     */
    private function report(string $label, array $result): void
    {
        $this->command?->info(sprintf(
            '  MonsterSeeder (%s) : %d création(s), %d mise(s) à jour, %d avertissement(s).',
            $label,
            count($result['created']),
            count($result['updated']),
            count($result['skipped'])
        ));
        foreach ($result['skipped'] as $reason) {
            $this->command?->warn('    '.$reason);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Spell;

use App\Services\Spell\SpellDegreeLegacyMigrator;
use Illuminate\Console\Command;

/**
 * Migre les effets legacy (effect_spell) vers spell_degrees.
 *
 * @example php artisan spells:migrate-degrees --dry-run
 */
final class SpellsMigrateDegreesCommand extends Command
{
    protected $signature = 'spells:migrate-degrees
                            {--dry-run : Simule sans écrire}
                            {--spell= : Limite à un spell_id}';

    protected $description = 'Migre Effect/EffectDegree liés aux sorts vers SpellDegree (idempotent).';

    public function handle(SpellDegreeLegacyMigrator $migrator): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $spellId = $this->option('spell');

        if ($spellId !== null && $spellId !== '') {
            $spell = \App\Models\Entity\Spell::query()->findOrFail((int) $spellId);
            $report = [
                'migrated' => 0,
                'skipped' => 0,
                'conflicts' => [],
                'shared_effects_duplicated' => 0,
            ];
            $one = $migrator->migrateSpell($spell, $dryRun);
            $report['migrated'] = $one['migrated'] ? 1 : 0;
            $report['skipped'] = $one['skipped'] ? 1 : 0;
            $report['shared_effects_duplicated'] = $one['shared_effects_duplicated'];
            $report['conflicts'] = $one['conflicts'];
        } else {
            $report = $migrator->migrateAll($dryRun);
        }

        $this->info(($dryRun ? '[dry-run] ' : '').sprintf(
            'Migrés : %d — Ignorés : %d — Effets partagés : %d — Conflits : %d',
            $report['migrated'],
            $report['skipped'],
            $report['shared_effects_duplicated'],
            count($report['conflicts'])
        ));

        foreach (array_slice($report['conflicts'], 0, 50) as $conflict) {
            $this->warn(json_encode($conflict, JSON_UNESCAPED_UNICODE) ?: '');
        }
        if (count($report['conflicts']) > 50) {
            $this->warn('… '.(count($report['conflicts']) - 50).' conflits supplémentaires non affichés.');
        }

        return self::SUCCESS;
    }
}

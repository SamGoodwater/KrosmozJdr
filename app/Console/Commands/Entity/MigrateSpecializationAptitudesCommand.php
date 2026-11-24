<?php

namespace App\Console\Commands\Entity;

use App\Models\Entity\Aptitude;
use App\Models\Entity\Capability;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Migre les aptitudes de spé stockées comme Capability is_passive vers Aptitude.
 *
 * Hors périmètre : passifs de classe (breed_capability).
 */
class MigrateSpecializationAptitudesCommand extends Command
{
    protected $signature = 'entities:migrate-specialization-aptitudes
                            {--dry-run : Affiche le plan sans écrire}
                            {--force : Exécute même si des aptitudes existent déjà}';

    protected $description = 'Capability (spé, is_passive) → Aptitude + pivot aptitude_specialization';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $candidates = Capability::query()
            ->where('is_passive', true)
            ->whereHas('specializations')
            ->whereDoesntHave('breeds')
            ->with(['specializations' => fn ($q) => $q->withPivot('level')])
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('Aucune capacité passive de spécialisation à migrer.');

            return self::SUCCESS;
        }

        $this->info(sprintf('%d capacité(s) candidate(s).', $candidates->count()));

        if ($dryRun) {
            foreach ($candidates as $cap) {
                $this->line(sprintf(
                    '- [%d] %s (%d spé)',
                    $cap->id,
                    $cap->name,
                    $cap->specializations->count(),
                ));
            }

            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped = 0;

        DB::transaction(function () use ($candidates, &$migrated, &$skipped): void {
            foreach ($candidates as $capability) {
                $existing = Aptitude::query()->where('name', $capability->name)->first();
                if ($existing && ! $this->option('force')) {
                    $this->warn("Skip « {$capability->name} » : aptitude déjà présente (#{$existing->id}).");
                    $skipped++;

                    continue;
                }

                $aptitude = $existing ?? new Aptitude;
                $aptitude->fill([
                    'name' => $capability->name,
                    'description' => $capability->description ?: 'Aptitude de spécialisation',
                    'effect' => $capability->effect,
                    'reuse' => 'Illimitée',
                    'duration' => 'Permanente',
                    'range' => 'Soi',
                    'state' => $capability->state ?: Aptitude::STATE_PLAYABLE,
                    'read_level' => (int) ($capability->read_level ?? User::ROLE_GUEST),
                    'write_level' => (int) ($capability->write_level ?? User::ROLE_ADMIN),
                    'image' => $capability->image,
                    'created_by' => $capability->created_by,
                ]);
                $aptitude->save();

                $sync = [];
                foreach ($capability->specializations as $spe) {
                    $sync[$spe->id] = ['level' => (int) ($spe->pivot->level ?? 1)];
                }
                if ($sync !== []) {
                    $aptitude->specializations()->syncWithoutDetaching($sync);
                }

                foreach ($capability->specializations as $spe) {
                    $spe->capabilities()->detach($capability->id);
                }

                $capability->delete();
                $migrated++;
            }
        });

        $this->info(sprintf('Migrées : %d — ignorées : %d', $migrated, $skipped));
        $this->comment('Reseed des sections CMS recommandé : php artisan db:seed --class=Database\\Seeders\\Entity\\SpecializationSeeder');

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Effects;

use App\Models\EffectDegree;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Illuminate\Console\Command;

/**
 * Recalcule config_signature pour tous les degrés d’effet.
 */
final class EffectsRebuildSignaturesCommand extends Command
{
    protected $signature = 'effects:rebuild-signatures
                            {--dry-run : N\'écrit pas en base, affiche seulement les changements}
                            {--ids= : IDs de effect_degrees séparés par des virgules (optionnel)}';

    protected $description = 'Recalcule config_signature des degrés d’effet (target_type + zone + sous-effets)';

    public function handle(IntegrationService $integrationService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $idsStr = (string) $this->option('ids');

        $query = EffectDegree::query()->with(['effectSubEffects', 'effect']);
        if ($idsStr !== '') {
            $ids = array_filter(array_map('intval', explode(',', $idsStr)));
            if ($ids === []) {
                $this->error('Liste d\'IDs invalide.');

                return self::FAILURE;
            }
            $query->whereIn('id', $ids);
        }

        $degrees = $query->orderBy('id')->get();

        if ($degrees->isEmpty()) {
            $this->info('Aucun degré à traiter.');

            return self::SUCCESS;
        }

        $updated = 0;
        $unchanged = 0;
        $empty = 0;

        $this->line('Degrés à traiter : '.$degrees->count());

        foreach ($degrees as $degree) {
            $newSignature = $integrationService->rebuildConfigSignatureForEffectDegree($degree);

            if ($newSignature === null) {
                $empty++;
                $this->line("  [{$degree->id}] effect_id={$degree->effect_id} — sous-effets vides, ignoré.");

                continue;
            }

            if ($degree->config_signature === $newSignature) {
                $unchanged++;

                continue;
            }

            if (! $dryRun) {
                $degree->update(['config_signature' => $newSignature]);
            }
            $updated++;
            $this->line("  [{$degree->id}] signature mise à jour.");
        }

        $this->newLine();
        $this->info("Résumé : {$updated} mis à jour, {$unchanged} inchangés, {$empty} ignorés (sans sous-effets).");
        if ($dryRun && $updated > 0) {
            $this->warn('Mode --dry-run : aucune modification en base. Relancez sans --dry-run pour appliquer.');
        }

        return self::SUCCESS;
    }
}

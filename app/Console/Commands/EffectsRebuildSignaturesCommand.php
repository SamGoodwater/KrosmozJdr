<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Effect;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Illuminate\Console\Command;

/**
 * Recalcule config_signature pour tous les effets (inclut target_type et area).
 *
 * À exécuter une fois après la modification de l'algorithme de déduplication
 * pour aligner les signatures existantes et éviter les doublons lors des ré-imports.
 */
final class EffectsRebuildSignaturesCommand extends Command
{
    protected $signature = 'effects:rebuild-signatures
                            {--dry-run : N\'écrit pas en base, affiche seulement les changements}
                            {--ids= : IDs d\'effets séparés par des virgules (optionnel)}';

    protected $description = 'Recalcule config_signature des effets (inclut target_type et area)';

    public function handle(IntegrationService $integrationService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $idsStr = (string) $this->option('ids');

        $query = Effect::query()->with('effectSubEffects');
        if ($idsStr !== '') {
            $ids = array_filter(array_map('intval', explode(',', $idsStr)));
            if ($ids === []) {
                $this->error('Liste d\'IDs invalide.');

                return self::FAILURE;
            }
            $query->whereIn('id', $ids);
        }

        /** @var \Illuminate\Support\Collection<int, Effect> $effects */
        $effects = $query->orderBy('id')->get();

        if ($effects->isEmpty()) {
            $this->info('Aucun effet à traiter.');

            return self::SUCCESS;
        }

        $updated = 0;
        $unchanged = 0;
        $empty = 0;

        $this->line('Effets à traiter : ' . $effects->count());

        foreach ($effects as $effect) {
            $newSignature = $integrationService->rebuildConfigSignatureForEffect($effect);

            if ($newSignature === null) {
                $empty++;
                $this->line("  [{$effect->id}] {$effect->name} — sous-effets vides, ignoré.");
                continue;
            }

            if ($effect->config_signature === $newSignature) {
                $unchanged++;

                continue;
            }

            if (!$dryRun) {
                $effect->update(['config_signature' => $newSignature]);
            }
            $updated++;
            $this->line("  [{$effect->id}] {$effect->name} — signature mise à jour.");
        }

        $this->newLine();
        $this->info("Résumé : {$updated} mis à jour, {$unchanged} inchangés, {$empty} ignorés (sans sous-effets).");
        if ($dryRun && $updated > 0) {
            $this->warn('Mode --dry-run : aucune modification en base. Relancez sans --dry-run pour appliquer.');
        }

        return self::SUCCESS;
    }
}

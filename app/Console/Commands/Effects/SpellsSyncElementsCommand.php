<?php

declare(strict_types=1);

namespace App\Console\Commands\Effects;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Spell;
use App\Services\Effect\SpellElementInferenceService;
use Illuminate\Console\Command;

/**
 * Recalcule {@see Spell::$element} depuis les sous-effets.
 */
final class SpellsSyncElementsCommand extends Command
{
    protected $signature = 'spells:sync-elements
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}';

    protected $description = 'Aligne l’élément des sorts sur les éléments présents dans leurs effets';

    public function handle(SpellElementInferenceService $inference): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $stats = $inference->syncAll($dryRun);

        $this->info(sprintf(
            'Sorts scannés : %d. Mis à jour : %d. Inchangés : %d. Sans élément (vidés) : %d.',
            $stats['scanned'],
            $stats['updated'],
            $stats['unchanged'],
            $stats['cleared']
        ));

        if ($dryRun) {
            $this->warn('Mode --dry-run : aucune écriture. Relancez sans --dry-run pour appliquer.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}

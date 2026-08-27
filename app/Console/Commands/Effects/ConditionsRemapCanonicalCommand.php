<?php

declare(strict_types=1);

namespace App\Console\Commands\Effects;

use App\Console\ArtisanExitCode;
use App\Services\Condition\ConditionSpellCanonicalRemapper;
use Illuminate\Console\Command;

/**
 * Recolle sorts et effets sur les états JDR de base (Pesanteur, Empoisonné, …).
 */
final class ConditionsRemapCanonicalCommand extends Command
{
    protected $signature = 'conditions:remap-canonical
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}';

    protected $description = 'Pointe les sorts vers les états JDR canoniques ; les jetons Dofus restent en Brut';

    public function handle(ConditionSpellCanonicalRemapper $remapper): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $stats = $remapper->remapAll($dryRun);

        $this->info('Alias canonical_condition_id : '.$stats['aliases']);
        $this->info('Liaisons condition_spell mises à jour : '.$stats['spell_links']);
        $this->info('Liaisons sans canon retirées : '.$stats['unlinked']);
        $this->info('Params d’effets mis à jour : '.$stats['effect_params']);

        if ($dryRun) {
            $this->warn('Mode --dry-run : aucune écriture. Relancez sans --dry-run pour appliquer.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}

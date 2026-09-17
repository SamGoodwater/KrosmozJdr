<?php

declare(strict_types=1);

namespace App\Console\Commands\Effects;

use App\Console\ArtisanExitCode;
use App\Models\EffectSubEffect;
use App\Models\Entity\Condition;
use App\Support\DofusHyperlinkText;
use Illuminate\Console\Command;

/**
 * Nettoie les hyperliens Dofus (`{{spell,…::Libellé}}`) dans les noms d’états
 * et les params.condition_name des pivots d’effets.
 */
final class ConditionsStripDofusHyperlinksCommand extends Command
{
    protected $signature = 'conditions:strip-dofus-hyperlinks
                            {--dry-run : N\'écrit pas en base, affiche seulement le volume}';

    protected $description = 'Remplace les hyperliens DofusDB dans les noms d’états / condition_name par le libellé affichable';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $conditionsUpdated = 0;
        $conditions = Condition::query()
            ->where('name', 'like', '%{{%')
            ->orderBy('id')
            ->get(['id', 'name']);

        foreach ($conditions as $condition) {
            $clean = DofusHyperlinkText::toDisplayLabel($condition->name);
            if ($clean === '' || $clean === $condition->name) {
                continue;
            }
            $conditionsUpdated++;
            if (! $dryRun) {
                // Bypass mutator path is fine; mutator would also clean.
                $condition->name = $clean;
                $condition->save();
            }
        }

        $pivotsUpdated = 0;
        $pivots = EffectSubEffect::query()
            ->where('params', 'like', '%{{%')
            ->orderBy('id')
            ->get(['id', 'params']);

        foreach ($pivots as $pivot) {
            $params = is_array($pivot->params) ? $pivot->params : [];
            $raw = isset($params['condition_name']) && is_string($params['condition_name'])
                ? $params['condition_name']
                : null;
            if ($raw === null || ! str_contains($raw, '{{')) {
                continue;
            }
            $clean = DofusHyperlinkText::toDisplayLabel($raw);
            if ($clean === '' || $clean === $raw) {
                continue;
            }
            $params['condition_name'] = $clean;
            $pivotsUpdated++;
            if (! $dryRun) {
                $pivot->params = $params;
                $pivot->save();
            }
        }

        $this->info("États (conditions) : {$conditionsUpdated} / {$conditions->count()} à nettoyer.");
        $this->info("Pivots effect_sub_effect : {$pivotsUpdated} / {$pivots->count()} à nettoyer.");

        if ($dryRun && ($conditionsUpdated > 0 || $pivotsUpdated > 0)) {
            $this->warn('Mode --dry-run : aucune écriture. Relancez sans --dry-run pour appliquer.');
        }

        return ArtisanExitCode::SUCCESS;
    }
}

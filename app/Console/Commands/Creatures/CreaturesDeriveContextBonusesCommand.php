<?php

declare(strict_types=1);

namespace App\Console\Commands\Creatures;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\FormulaVariableResolver;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Creature\Runtime\CreatureItemBonusAggregator;
use App\Services\Creature\Runtime\CreatureObjectBonusToCreatureVariables;
use App\Services\Creature\Runtime\CreatureVariableMapBuilder;
use App\Support\Creature\CreatureComposableColumns;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Déduit les bonus contextuels à partir des totaux explicites existants.
 *
 * Pour chaque caractéristique composable : contextuel = total − base − objets.
 * Par défaut le total explicite est conservé (prioritaire à l'affichage).
 * Avec `--clear-total`, le total est vidé pour basculer en mode composé.
 *
 * @example php artisan creatures:derive-context-bonuses --entity=monster --dry-run
 * @example php artisan creatures:derive-context-bonuses --entity=monster --clear-total --report=storage/logs/derive-context.md
 */
final class CreaturesDeriveContextBonusesCommand extends Command
{
    protected $signature = 'creatures:derive-context-bonuses
        {--entity=monster : monster|npc|class (class = créatures liées à un PNJ avec breed)}
        {--dry-run : Simulation sans écriture}
        {--clear-total : Vide le total explicite après écriture du contexte}
        {--report= : Chemin du rapport Markdown}
        {--limit=0 : Nombre max de créatures (0 = illimité)}';

    protected $description = 'Déduit contextuel = total − base − objets pour les créatures existantes';

    public function handle(
        CharacteristicGetterService $getter,
        FormulaResolutionService $formulas,
        CreatureVariableMapBuilder $variableMapBuilder,
        CreatureItemBonusAggregator $itemBonusAggregator,
        CreatureObjectBonusToCreatureVariables $objectBonusMerger
    ): int {
        $entity = (string) $this->option('entity');
        if (! in_array($entity, ['monster', 'npc', 'class'], true)) {
            $this->error('entity doit être monster, npc ou class.');

            return ArtisanExitCode::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $clearTotal = (bool) $this->option('clear-total');
        $limit = max(0, (int) $this->option('limit'));
        $reportPath = $this->option('report');

        $query = Creature::query()->with('items');
        if ($entity === 'monster') {
            $query->whereIn('id', Monster::query()->select('creature_id'));
        } elseif ($entity === 'npc') {
            $query->whereIn('id', Npc::query()->select('creature_id'));
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $updated = 0;
        $skipped = 0;
        $lines = [];
        $lines[] = '# Derive context bonuses';
        $lines[] = '';
        $lines[] = '- entity: '.$entity;
        $lines[] = '- dry-run: '.($dryRun ? 'yes' : 'no');
        $lines[] = '- clear-total: '.($clearTotal ? 'yes' : 'no');
        $lines[] = '';

        $query->orderBy('id')->chunkById(50, function ($creatures) use (
            $entity,
            $dryRun,
            $clearTotal,
            $getter,
            $formulas,
            $variableMapBuilder,
            $itemBonusAggregator,
            $objectBonusMerger,
            &$updated,
            &$skipped,
            &$lines
        ): void {
            foreach ($creatures as $creature) {
                /** @var Creature $creature */
                $variables = $variableMapBuilder->buildBaseMap($creature, $entity);
                $variables = FormulaVariableResolver::withShortNames('creature', $variables);
                $itemTotals = $itemBonusAggregator->aggregateTotals($creature->items);
                $objectByKey = $objectBonusMerger->mapToCharacteristicKeys($entity, $itemTotals);

                $changes = [];
                foreach (CreatureComposableColumns::all() as $column) {
                    if (! $creature->hasExplicitTotal($column)) {
                        continue;
                    }
                    $key = $this->characteristicKeyForColumn($column, $entity, $getter);
                    if ($key === null) {
                        $skipped++;
                        $lines[] = "- creature #{$creature->id} / {$column} : clé caractéristique introuvable";

                        continue;
                    }

                    $total = (float) $creature->getAttribute($column);
                    $object = (float) ($objectByKey[$key] ?? 0);
                    $base = 0.0;
                    $def = $getter->getDefinition($key, $entity);
                    $formula = $def['formula'] ?? null;
                    if (is_string($formula) && trim($formula) !== '') {
                        $evaluated = $formulas->evaluate($formula, $variables, SafeExpressionEvaluator::DICE_MODE_MIN);
                        if ($evaluated === null) {
                            $skipped++;
                            $lines[] = "- creature #{$creature->id} / {$column} : formule de base non résolue";

                            continue;
                        }
                        $base = $evaluated;
                    }

                    $context = $total - $base - $object;
                    $contextStr = $this->formatNumber($context);
                    $contextCol = CreatureComposableColumns::contextColumn($column);
                    $changes[$contextCol] = $contextStr;
                    if ($clearTotal) {
                        $changes[$column] = null;
                    }
                    $lines[] = "- creature #{$creature->id} / {$column} : total={$total} base={$base} object={$object} → context={$contextStr}";
                }

                if ($changes === []) {
                    continue;
                }
                $updated++;
                if (! $dryRun) {
                    $creature->fill($changes);
                    $creature->save();
                }
            }
        });

        $lines[] = '';
        $lines[] = "Mis à jour : {$updated} ; ignorés : {$skipped}";

        if (is_string($reportPath) && trim($reportPath) !== '') {
            File::ensureDirectoryExists(dirname($reportPath));
            File::put($reportPath, implode("\n", $lines)."\n");
            $this->info('Rapport : '.$reportPath);
        }

        $this->info(($dryRun ? '[dry-run] ' : '')."{$updated} créature(s) traitée(s), {$skipped} écart(s).");

        return ArtisanExitCode::SUCCESS;
    }

    private function characteristicKeyForColumn(string $column, string $entity, CharacteristicGetterService $getter): ?string
    {
        // Heuristique : parcours des définitions via db_column (getter ne l’expose pas directement).
        $candidates = [
            $column.'_creature',
            match ($column) {
                'strong' => 'strength_creature',
                'intel' => 'intelligence_creature',
                'agi' => 'agility_creature',
                'sagesse' => 'wisdom_creature',
                'life' => 'life_points_creature',
                'ca' => 'armor_class_creature',
                'ini' => 'initiative_creature',
                'touch' => 'hit_bonus_creature',
                'fuite' => 'dodge_creature',
                'tacle' => 'tackle_creature',
                'pa' => 'action_points_creature',
                'pm' => 'movement_points_creature',
                'po' => 'range_creature',
                'dodge_pa' => 'dodge_action_points_creature',
                'dodge_pm' => 'dodge_movement_points_creature',
                default => null,
            },
        ];

        foreach ($candidates as $key) {
            if ($key === null) {
                continue;
            }
            $def = $getter->getDefinition($key, $entity);
            if ($def !== null && ($def['db_column'] ?? null) === $column) {
                return $key;
            }
        }

        return $candidates[0] ?? null;
    }

    private function formatNumber(float $value): string
    {
        if (floor($value) === $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.');
    }
}

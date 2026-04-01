<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\Entity\Creature;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\FormulaVariableResolver;
use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Calcule les caractéristiques dérivées d’une créature (formules BDD + bonus objets) et expose une décomposition pour l’API / tooltips.
 */
final class CreatureRuntimeStatsService
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly FormulaResolutionService $formulas,
        private readonly CreatureVariableMapBuilder $variableMapBuilder,
        private readonly CreatureItemBonusAggregator $itemBonusAggregator,
        private readonly CreatureObjectBonusToCreatureVariables $objectBonusMerger
    ) {}

    /**
     * @return array{
     *   entity: string,
     *   variables: array<string, float|int>,
     *   computed: array<string, array{
     *     key: string,
     *     value: float,
     *     formula: string|null,
     *     formula_display: string|null,
     *     substituted: string|null,
     *     placeholders: list<array{id: string, value: float}>
     *   }>,
     *   unresolved_computed_keys: list<string>,
     *   items: array{
     *     aggregated: array<string, int>,
     *     lines: list<array{item_id: int, name: string, quantity: int, bonuses: array<string, int>}>
     *   }
     * }
     */
    public function resolve(Creature $creature, string $entity = 'monster'): array
    {
        if (! in_array($entity, ['monster', 'class', 'npc'], true)) {
            $entity = 'monster';
        }

        $creature->loadMissing('items');

        $variables = $this->variableMapBuilder->buildBaseMap($creature, $entity);
        $itemTotals = $this->itemBonusAggregator->aggregateTotals($creature->items);
        $this->objectBonusMerger->mergeInto($variables, $entity, $itemTotals);

        $variables = FormulaVariableResolver::withShortNames('creature', $variables);

        $computedKeys = $this->listComputedCharacteristicKeys($entity);
        $resolved = [];
        $maxPasses = max(32, count($computedKeys) + 5);
        for ($pass = 0; $pass < $maxPasses; $pass++) {
            $progress = false;
            foreach ($computedKeys as $key) {
                if (array_key_exists($key, $resolved)) {
                    continue;
                }
                $def = $this->getter->getDefinition($key, $entity);
                if ($def === null) {
                    continue;
                }
                if ($this->mergedCreatureDbColumn($key, $entity) !== null) {
                    continue;
                }
                $formula = $def['formula'] ?? null;
                if ($formula === null || trim((string) $formula) === '') {
                    continue;
                }
                $evaluated = $this->formulas->evaluate($formula, $variables);
                if ($evaluated === null) {
                    continue;
                }
                $resolved[$key] = $evaluated;
                $variables[$key] = $evaluated;
                $variables = FormulaVariableResolver::withShortNames('creature', $variables);
                $progress = true;
            }
            if (! $progress) {
                break;
            }
        }

        $unresolved = [];
        foreach ($computedKeys as $key) {
            if (! array_key_exists($key, $resolved)) {
                $def = $this->getter->getDefinition($key, $entity);
                if ($def !== null && $this->mergedCreatureDbColumn($key, $entity) === null && ! empty($def['formula'])) {
                    $unresolved[] = $key;
                }
            }
        }

        $computedPayload = [];
        foreach ($resolved as $key => $value) {
            $def = $this->getter->getDefinition($key, $entity);
            if ($def === null) {
                continue;
            }
            $formula = $def['formula'] ?? null;
            $ids = $this->formulas->extractVariablePlaceholders($formula);
            $placeholders = [];
            foreach ($ids as $id) {
                $placeholders[] = [
                    'id' => $id,
                    'value' => isset($variables[$id]) ? (float) $variables[$id] : 0.0,
                ];
            }
            $computedPayload[$key] = [
                'key' => $key,
                'value' => (float) $value,
                'formula' => is_string($formula) ? $formula : null,
                'formula_display' => isset($def['formula_display']) && is_string($def['formula_display'])
                    ? $def['formula_display']
                    : null,
                'substituted' => $this->formulas->substitutePlaceholdersForDisplay($formula, $variables),
                'placeholders' => $placeholders,
            ];
        }

        return [
            'entity' => $entity,
            'variables' => $this->normalizeNumericMap($variables),
            'computed' => $computedPayload,
            'unresolved_computed_keys' => $unresolved,
            'items' => [
                'aggregated' => $itemTotals,
                'lines' => $this->itemBonusAggregator->aggregatePerItemLines($creature->items),
            ],
        ];
    }

    /**
     * @param  array<string, int|float>  $map
     * @return array<string, float|int>
     */
    private function normalizeNumericMap(array $map): array
    {
        $out = [];
        foreach ($map as $k => $v) {
            $out[$k] = is_float($v) && floor($v) === $v ? (int) $v : (float) $v;
        }

        return $out;
    }

    /**
     * Colonne créature réelle en BDD (fusion * + entité), ou null si la carac est purement calculée.
     */
    private function mergedCreatureDbColumn(string $characteristicKey, string $entity): ?string
    {
        $characteristic = Characteristic::query()->where('key', $characteristicKey)->first();
        if ($characteristic === null) {
            return null;
        }
        $effective = $characteristic->effectiveCharacteristic();
        $rows = CharacteristicCreature::query()
            ->where('characteristic_id', $effective->id)
            ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, $entity])
            ->get();
        $base = $rows->firstWhere('entity', CharacteristicCreature::ENTITY_ALL);
        $overlay = $rows->firstWhere('entity', $entity);
        $overlayVal = $overlay !== null ? $overlay->db_column : null;
        if ($overlayVal !== null && $overlayVal !== '') {
            return $overlayVal;
        }
        $baseVal = $base !== null ? $base->db_column : null;
        if ($baseVal !== null && $baseVal !== '') {
            return $baseVal;
        }

        return null;
    }

    /**
     * Clés ayant une formule sur characteristic_creature et sans colonne stockée (calcul pur).
     *
     * @return list<string>
     */
    private function listComputedCharacteristicKeys(string $entity): array
    {
        $ids = CharacteristicCreature::query()
            ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, $entity])
            ->whereNotNull('formula')
            ->distinct()
            ->pluck('characteristic_id');

        $keys = Characteristic::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->pluck('key')
            ->all();

        $out = [];
        foreach ($keys as $key) {
            if ($this->mergedCreatureDbColumn($key, $entity) !== null) {
                continue;
            }
            $def = $this->getter->getDefinition($key, $entity);
            $formula = $def['formula'] ?? null;
            if ($def === null || ! is_string($formula) || trim($formula) === '') {
                continue;
            }
            $out[] = $key;
        }

        return $out;
    }
}

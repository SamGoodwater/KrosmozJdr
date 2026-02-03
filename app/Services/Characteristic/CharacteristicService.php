<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\EntityCharacteristic;
use Illuminate\Support\Facades\Cache;

/**
 * Service de lecture des caractéristiques depuis la base (table entity_characteristics).
 *
 * Expose une structure compatible avec ValidationService et DofusDbConversionFormulas :
 * getCharacteristics() = characteristic_key => [ name, ..., entities => [ entity => [ min, max, ... ] ] ].
 * Construit par regroupement des lignes entity_characteristics par characteristic_key.
 */
final class CharacteristicService
{
    private const CACHE_KEY = 'characteristics.full';

    private const CACHE_TTL_SECONDS = 3600;

    /** Entités qui ont une caractéristique rareté (niveau → indice de rareté). */
    private const RARITY_ENTITIES = [EntityCharacteristic::ENTITY_RESOURCE, EntityCharacteristic::ENTITY_CONSUMABLE, EntityCharacteristic::ENTITY_ITEM];

    public function __construct(
        private readonly ?FormulaEvaluator $formulaEvaluator = null
    ) {
    }

    /**
     * Rareté par défaut selon le niveau (pour resource, consumable, item).
     * Si une ligne entity_characteristics (entity, characteristic_key=rarity) a un champ computation
     * (table level → valeur), l'utilise ; sinon fallback sur config characteristics_rarity.
     *
     * @return int Indice de rareté (0 = commun, 4 = mythique)
     */
    public function getRarityByLevel(int $levelKrosmoz, string $entity): int
    {
        if (! in_array($entity, self::RARITY_ENTITIES, true)) {
            return 0;
        }
        $row = EntityCharacteristic::query()
            ->where('entity', $entity)
            ->where('characteristic_key', 'rarity')
            ->first();
        if ($row !== null && $row->computation !== null && $row->computation !== []) {
            $formula = is_string($row->computation) ? $row->computation : json_encode($row->computation);
            if ($this->formulaEvaluator !== null && trim($formula) !== '') {
                $result = $this->formulaEvaluator->evaluateFormulaOrTable($formula, ['level' => $levelKrosmoz]);
                if ($result !== null) {
                    return (int) round($result);
                }
            }
        }
        $bands = config('characteristics_rarity.rarity_default_by_level', [
            0 => 0, 3 => 1, 7 => 2, 10 => 3, 17 => 4,
        ]);
        krsort($bands, SORT_NUMERIC);
        foreach ($bands as $minLevel => $rarity) {
            if ($levelKrosmoz >= $minLevel) {
                return (int) $rarity;
            }
        }

        return 0;
    }

    /**
     * Retourne toutes les caractéristiques (characteristic_key => définition avec entities).
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCharacteristics(): array
    {
        $full = $this->getFullConfig();

        return $full['characteristics'] ?? [];
    }

    /**
     * Retourne uniquement les compétences (is_competence === true).
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCompetences(): array
    {
        $full = $this->getFullConfig();

        return $full['competences'] ?? [];
    }

    /**
     * Retourne la config complète (characteristics + competences).
     *
     * @return array{characteristics: array<string, array<string, mixed>>, competences: array<string, array<string, mixed>>}
     */
    public function getFullConfig(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildFullConfig();
        });
    }

    /**
     * Retourne les caractéristiques pour une entité donnée (organisation par entité).
     *
     * @return array<string, array<string, mixed>> characteristic_key => définition
     */
    public function getCharacteristicsForEntity(string $entity): array
    {
        $rows = EntityCharacteristic::query()
            ->where('entity', $entity)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $out[$row->characteristic_key] = $this->entityCharacteristicToArray($row);
        }

        return $out;
    }

    /**
     * Liste des entités ayant au moins une caractéristique.
     *
     * @return list<string>
     */
    public function getEntityList(): array
    {
        return EntityCharacteristic::query()
            ->distinct()
            ->pluck('entity')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Retourne une caractéristique par id (characteristic_key), ou null.
     * La définition agrège toutes les entités pour cette clé.
     *
     * @return array<string, mixed>|null
     */
    public function getCharacteristic(string $id): ?array
    {
        $all = $this->getCharacteristics();

        return $all[$id] ?? null;
    }

    /**
     * Retourne les limites min/max pour une caractéristique et une entité.
     *
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicId, string $entity): ?array
    {
        $def = $this->getCharacteristic($characteristicId);
        if ($def === null) {
            return null;
        }
        $entityDef = $def['entities'][$entity] ?? null;
        if ($entityDef === null || !isset($entityDef['min'], $entityDef['max'])) {
            return null;
        }

        return [
            'min' => (int) $entityDef['min'],
            'max' => (int) $entityDef['max'],
        ];
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Construit la config complète depuis entity_characteristics (groupé par characteristic_key).
     *
     * @return array{characteristics: array<string, array<string, mixed>>, competences: array<string, array<string, mixed>>}
     */
    private function buildFullConfig(): array
    {
        $rows = EntityCharacteristic::query()->orderBy('sort_order')->orderBy('entity')->get();
        $byKey = [];
        foreach ($rows as $row) {
            $key = $row->characteristic_key;
            if (!isset($byKey[$key])) {
                $byKey[$key] = [
                    'db_column' => $row->db_column ?? $key,
                    'name' => $row->name,
                    'short_name' => $row->short_name,
                    'description' => $row->descriptions,
                    'icon' => $row->icon,
                    'color' => $row->color,
                    'type' => $row->type ?? 'string',
                    'unit' => $row->unit,
                    'applies_to' => $row->applies_to ?? [],
                    'entities' => [],
                    'order' => $row->sort_order,
                ];
                if ($row->validation !== null && $row->validation !== []) {
                    $byKey[$key]['validation'] = $row->validation;
                }
                if ($row->value_available !== null && $row->value_available !== []) {
                    $byKey[$key]['value_available'] = $row->value_available;
                }
                if ($row->labels !== null && $row->labels !== []) {
                    $byKey[$key]['labels'] = $row->labels;
                }
                if ($row->is_competence) {
                    $byKey[$key]['is_competence'] = true;
                    if ($row->characteristic_id !== null) {
                        $byKey[$key]['characteristic'] = $row->characteristic_id;
                    }
                    if ($row->alternative_characteristic_id !== null) {
                        $byKey[$key]['alternative_characteristic'] = $row->alternative_characteristic_id;
                    }
                    if ($row->skill_type !== null) {
                        $byKey[$key]['skill_type'] = $row->skill_type;
                    }
                    if ($row->mastery_value_available !== null && $row->mastery_value_available !== []) {
                        $byKey[$key]['mastery_value_available'] = $row->mastery_value_available;
                    }
                    if ($row->mastery_labels !== null && $row->mastery_labels !== []) {
                        $byKey[$key]['mastery_labels'] = $row->mastery_labels;
                    }
                }
            }
            $byKey[$key]['entities'][$row->entity] = [
                'min' => $row->min,
                'max' => $row->max,
                'formula' => $row->formula,
                'formula_display' => $row->formula_display,
                'default' => $row->default_value !== null ? $this->castDefaultValue($row->default_value, $row->type) : null,
                'required' => $row->required,
                'validation_message' => $row->validation_message,
            ];
            if ($row->entity === 'item') {
                $byKey[$key]['forgemagie'] = [
                    'allowed' => $row->forgemagie_allowed,
                    'max' => (int) $row->forgemagie_max,
                ];
                if ($row->base_price_per_unit !== null) {
                    $byKey[$key]['base_price_per_unit'] = (float) $row->base_price_per_unit;
                }
                if ($row->rune_price_per_unit !== null) {
                    $byKey[$key]['rune_price_per_unit'] = (float) $row->rune_price_per_unit;
                }
            }
        }
        $competences = array_filter($byKey, fn ($def) => ($def['is_competence'] ?? false) === true);

        return [
            'characteristics' => $byKey,
            'competences' => $competences,
        ];
    }

    /**
     * Une ligne entity_characteristic en tableau pour l'API par entité.
     *
     * @return array<string, mixed>
     */
    private function entityCharacteristicToArray(EntityCharacteristic $row): array
    {
        $out = [
            'id' => $row->id,
            'entity' => $row->entity,
            'characteristic_key' => $row->characteristic_key,
            'name' => $row->name,
            'short_name' => $row->short_name,
            'helper' => $row->helper,
            'descriptions' => $row->descriptions,
            'icon' => $row->icon,
            'color' => $row->color,
            'unit' => $row->unit,
            'sort_order' => $row->sort_order,
            'min' => $row->min,
            'max' => $row->max,
            'formula' => $row->formula,
            'formula_display' => $row->formula_display,
            'computation' => $row->computation,
            'default_value' => $row->default_value,
            'required' => $row->required,
            'validation_message' => $row->validation_message,
            'forgemagie_allowed' => $row->forgemagie_allowed,
            'forgemagie_max' => $row->forgemagie_max,
            'base_price_per_unit' => $row->base_price_per_unit !== null ? (float) $row->base_price_per_unit : null,
            'rune_price_per_unit' => $row->rune_price_per_unit !== null ? (float) $row->rune_price_per_unit : null,
        ];

        return $out;
    }

    private function castDefaultValue(string $defaultValue, string $type): int|string|array|null
    {
        return match ($type) {
            'int' => is_numeric($defaultValue) ? (int) $defaultValue : null,
            'string' => $defaultValue,
            'array' => json_decode($defaultValue, true) ?? [],
            default => $defaultValue,
        };
    }
}

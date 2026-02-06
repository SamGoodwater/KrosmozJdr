<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Getter;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Formula\FormulaResolutionService;

/**
 * Getter généraliste : fournit les définitions d’une caractéristique par clé et entité.
 * Résout entity → groupe (creature, object, spell) et fusionne table générale + table de groupe.
 */
final class CharacteristicGetterService
{
    /** Valeur entity = « s'applique à toutes les entités du groupe ». */
    public const ENTITY_ALL = '*';

    public function __construct(
        private readonly FormulaResolutionService $formulaResolution
    ) {
    }

    /** Entités du groupe creature */
    private const GROUP_CREATURE = ['monster', 'class', 'npc'];

    /** Entités du groupe object */
    private const GROUP_OBJECT = ['item', 'consumable', 'resource', 'panoply'];

    /** Entités du groupe spell */
    private const GROUP_SPELL = ['spell'];

    /**
     * Retourne la définition complète d’une caractéristique pour une entité (nom, limites, formules, conversion, etc.).
     *
     * @return array<string, mixed>|null
     */
    public function getDefinition(string $characteristicKey, string $entity): ?array
    {
        $characteristic = Characteristic::where('key', $characteristicKey)->first();
        if ($characteristic === null) {
            return null;
        }
        [$base, $overlay] = $this->findGroupRows($characteristic->id, $entity);
        if ($base === null && $overlay === null) {
            return null;
        }
        return $this->mergeDefinition($characteristic, $base, $overlay, $entity);
    }

    /**
     * Retourne les limites min/max pour une caractéristique et une entité.
     * Min/max peuvent être une valeur fixe, une formule ([level]*2) ou une table par caractéristique ;
     * ils sont évalués avec les variables fournies (ex. level, vitality). Sans variables, formules/tables
     * sont évaluées avec 0 pour les variables manquantes.
     *
     * @param array<string, int|float> $variables Contexte pour l'évaluation (ex. ['level' => 5, 'vitality' => 10])
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicKey, string $entity, array $variables = []): ?array
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        if ($def === null) {
            return null;
        }
        $minVal = $this->resolveLimitValue($def['min'] ?? null, $variables);
        $maxVal = $this->resolveLimitValue($def['max'] ?? null, $variables);
        if ($minVal === null || $maxVal === null) {
            return null;
        }
        return [
            'min' => (int) $minVal,
            'max' => (int) $maxVal,
        ];
    }

    /**
     * Retourne les limites pour un champ de données (nom de colonne ou clé) et une entité.
     *
     * @param array<string, int|float> $variables Contexte pour l'évaluation des formules min/max
     * @return array{min: int, max: int}|null
     */
    public function getLimitsByField(string $field, string $entity, array $variables = []): ?array
    {
        $key = $this->resolveFieldToKey($field, $entity);
        return $key !== null ? $this->getLimits($key, $entity, $variables) : null;
    }

    /**
     * Retourne le groupe (creature, object, spell) pour une entité.
     */
    public function getGroupForEntity(string $entity): string
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            return 'creature';
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            return 'object';
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            return 'spell';
        }
        return 'object';
    }

    /**
     * Résout un nom de champ (ex. level, life) ou un nom court (ex. level → level_creature) en clé BDD pour une entité.
     * Accepte la clé complète, le db_column, ou le nom court sans suffixe (_creature, _object, _spell).
     */
    private function resolveFieldToKey(string $field, string $entity): ?string
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            $rows = CharacteristicCreature::whereIn('entity', [$entity, self::ENTITY_ALL])->with('characteristic')->get();
            foreach ($rows as $row) {
                if ($row->characteristic->key === $field || $row->db_column === $field) {
                    return $row->characteristic->key;
                }
            }
            $fullKey = $field . '_creature';
            if ($this->getDefinition($fullKey, $entity) !== null) {
                return $fullKey;
            }
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            $rows = CharacteristicObject::whereIn('entity', [$entity, self::ENTITY_ALL])->with('characteristic')->get();
            foreach ($rows as $row) {
                if ($row->characteristic->key === $field || $row->db_column === $field) {
                    return $row->characteristic->key;
                }
            }
            $fullKey = $field . '_object';
            if ($this->getDefinition($fullKey, $entity) !== null) {
                return $fullKey;
            }
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            $rows = CharacteristicSpell::whereIn('entity', [$entity, self::ENTITY_ALL])->with('characteristic')->get();
            foreach ($rows as $row) {
                if ($row->characteristic->key === $field || $row->db_column === $field) {
                    return $row->characteristic->key;
                }
            }
            $fullKey = $field . '_spell';
            if ($this->getDefinition($fullKey, $entity) !== null) {
                return $fullKey;
            }
        }
        return null;
    }

    /**
     * Retourne la formule de conversion Dofus → Krosmoz pour une caractéristique et une entité.
     */
    public function getConversionFormula(string $characteristicKey, string $entity): ?string
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        $formula = $def['conversion_formula'] ?? null;
        return is_string($formula) && trim($formula) !== '' ? $formula : null;
    }

    public function clearCache(): void
    {
        // Cache optionnel à ajouter plus tard avec invalidation ciblée (tags ou clés).
    }

    /**
     * Trouve la ligne de base (entity='*') et la surcharge (entity spécifique) pour characteristic_id + entity.
     * Permet d'affiner les propriétés du groupe pour une entité précise (ex. formule PV pour monster uniquement).
     *
     * @return array{0: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null, 1: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null}
     */
    private function findGroupRows(int $characteristicId, string $entity): array
    {
        $entities = $entity !== self::ENTITY_ALL ? [$entity, self::ENTITY_ALL] : [self::ENTITY_ALL];

        if (in_array($entity, self::GROUP_CREATURE, true)) {
            $rows = CharacteristicCreature::where('characteristic_id', $characteristicId)
                ->whereIn('entity', $entities)
                ->get();
            $base = $rows->firstWhere('entity', self::ENTITY_ALL);
            $overlay = $entity !== self::ENTITY_ALL ? $rows->firstWhere('entity', $entity) : null;
            return [$base, $overlay];
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            $rows = CharacteristicObject::where('characteristic_id', $characteristicId)
                ->whereIn('entity', $entities)
                ->with('allowedItemTypes')
                ->get();
            $base = $rows->firstWhere('entity', self::ENTITY_ALL);
            $overlay = $entity !== self::ENTITY_ALL ? $rows->firstWhere('entity', $entity) : null;
            return [$base, $overlay];
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            $rows = CharacteristicSpell::where('characteristic_id', $characteristicId)
                ->whereIn('entity', $entities)
                ->get();
            $base = $rows->firstWhere('entity', self::ENTITY_ALL);
            $overlay = $entity !== self::ENTITY_ALL ? $rows->firstWhere('entity', $entity) : null;
            return [$base, $overlay];
        }
        return [null, null];
    }

    /**
     * Fusionne base et overlay : pour chaque propriété du groupe, la valeur non nulle de l'overlay l'emporte.
     */
    private function pickGroupValue(
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $base,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $overlay,
        string $attribute
    ): mixed {
        $overlayVal = $overlay !== null ? $overlay->getAttribute($attribute) : null;
        if ($overlayVal !== null && $overlayVal !== '') {
            return $overlayVal;
        }
        return $base !== null ? $base->getAttribute($attribute) : null;
    }

    /**
     * Résout une limite (min ou max) : valeur fixe, formule ou table → entier.
     *
     * @param mixed $value Valeur en BDD (string numérique, formule ou JSON table)
     * @param array<string, int|float> $variables Contexte pour l'évaluation
     */
    private function resolveLimitValue(mixed $value, array $variables): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        if (is_numeric($s)) {
            return (int) (float) $s;
        }
        $evaluated = $this->formulaResolution->evaluate($s, $variables);
        if ($evaluated === null) {
            return null;
        }
        return (int) round($evaluated);
    }

    /**
     * Fusionne la caractéristique générale et les lignes de groupe (base + surcharge entité) en un seul tableau.
     * Les propriétés non généralistes (min, max, formula, etc.) sont prises sur la surcharge si non vides, sinon sur la base.
     *
     * @param CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $base Ligne entity='*'
     * @param CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $overlay Ligne entity précise (ex. monster)
     * @return array<string, mixed>
     */
    private function mergeDefinition(
        Characteristic $characteristic,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $base,
        CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null $overlay,
        string $entity
    ): array {
        $row = $overlay ?? $base;
        $out = [
            'id' => $characteristic->id,
            'key' => $characteristic->key,
            'name' => $characteristic->name,
            'short_name' => $characteristic->short_name,
            'helper' => $characteristic->helper,
            'descriptions' => $characteristic->descriptions,
            'icon' => $characteristic->icon,
            'color' => $characteristic->color,
            'unit' => $characteristic->unit,
            'type' => $characteristic->type,
            'entity' => $entity,
            'db_column' => $this->pickGroupValue($base, $overlay, 'db_column') ?? $characteristic->key,
            'min' => $this->pickGroupValue($base, $overlay, 'min'),
            'max' => $this->pickGroupValue($base, $overlay, 'max'),
            'formula' => $this->pickGroupValue($base, $overlay, 'formula'),
            'formula_display' => $this->pickGroupValue($base, $overlay, 'formula_display'),
            'default_value' => $this->pickGroupValue($base, $overlay, 'default_value'),
            'conversion_formula' => $this->pickGroupValue($base, $overlay, 'conversion_formula'),
            'conversion_dofus_sample' => $this->pickGroupValue($base, $overlay, 'conversion_dofus_sample'),
            'conversion_krosmoz_sample' => $this->pickGroupValue($base, $overlay, 'conversion_krosmoz_sample'),
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_allowed'] = $this->pickGroupValue($base, $overlay, 'forgemagie_allowed') ?? $row->forgemagie_allowed;
            $out['forgemagie_max'] = $this->pickGroupValue($base, $overlay, 'forgemagie_max') ?? $row->forgemagie_max;
            $out['base_price_per_unit'] = $this->pickGroupValue($base, $overlay, 'base_price_per_unit');
            $out['rune_price_per_unit'] = $this->pickGroupValue($base, $overlay, 'rune_price_per_unit');
            $out['value_available'] = $this->pickGroupValue($base, $overlay, 'value_available') ?? $row->value_available;
            $overlayObj = $overlay instanceof CharacteristicObject ? $overlay : null;
            $baseObj = $base instanceof CharacteristicObject ? $base : null;
            $out['allowed_item_type_ids'] = ($overlayObj && $overlayObj->relationLoaded('allowedItemTypes') && $overlayObj->allowedItemTypes->isNotEmpty())
                ? $overlayObj->allowedItemTypes->pluck('id')->all()
                : ($baseObj && $baseObj->relationLoaded('allowedItemTypes') ? $baseObj->allowedItemTypes->pluck('id')->all() : []);
        }
        if ($row instanceof CharacteristicCreature) {
            $out['labels'] = $this->pickGroupValue($base, $overlay, 'labels') ?? $row->labels;
            $out['validation'] = $this->pickGroupValue($base, $overlay, 'validation') ?? $row->validation;
        }
        if ($row instanceof CharacteristicSpell) {
            $out['value_available'] = $this->pickGroupValue($base, $overlay, 'value_available') ?? $row->value_available;
        }
        return $out;
    }
}

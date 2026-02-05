<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Getter;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
/**
 * Getter généraliste : fournit les définitions d’une caractéristique par clé et entité.
 * Résout entity → groupe (creature, object, spell) et fusionne table générale + table de groupe.
 */
final class CharacteristicGetterService
{
    /** Valeur entity = « s'applique à toutes les entités du groupe ». */
    public const ENTITY_ALL = '*';

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
        $row = $this->findGroupRow($characteristic->id, $entity);
        if ($row === null) {
            return null;
        }
        return $this->mergeDefinition($characteristic, $row);
    }

    /**
     * Retourne les limites min/max pour une caractéristique et une entité.
     *
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicKey, string $entity): ?array
    {
        $def = $this->getDefinition($characteristicKey, $entity);
        if ($def === null || !isset($def['min'], $def['max'])) {
            return null;
        }
        return [
            'min' => (int) $def['min'],
            'max' => (int) $def['max'],
        ];
    }

    /**
     * Retourne les limites pour un champ de données (nom de colonne ou clé) et une entité.
     * Résout field via key ou db_column.
     *
     * @return array{min: int, max: int}|null
     */
    public function getLimitsByField(string $field, string $entity): ?array
    {
        $key = $this->resolveFieldToKey($field, $entity);
        return $key !== null ? $this->getLimits($key, $entity) : null;
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
     * Trouve la ligne du groupe pour characteristic_id + entity.
     * Priorité : 1) ligne avec entity = entité demandée (surcharge), 2) ligne avec entity = '*' (défaut groupe).
     *
     * @return CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null
     */
    private function findGroupRow(int $characteristicId, string $entity): CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null
    {
        if (in_array($entity, self::GROUP_CREATURE, true)) {
            return CharacteristicCreature::where('characteristic_id', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL])
                ->orderByRaw('entity = ? DESC', [$entity])
                ->first();
        }
        if (in_array($entity, self::GROUP_OBJECT, true)) {
            return CharacteristicObject::where('characteristic_id', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL])
                ->orderByRaw('entity = ? DESC', [$entity])
                ->first();
        }
        if (in_array($entity, self::GROUP_SPELL, true)) {
            return CharacteristicSpell::where('characteristic_id', $characteristicId)
                ->whereIn('entity', [$entity, self::ENTITY_ALL])
                ->orderByRaw('entity = ? DESC', [$entity])
                ->first();
        }
        return null;
    }

    /**
     * Fusionne la caractéristique générale et la ligne de groupe en un seul tableau.
     *
     * @param CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row
     * @return array<string, mixed>
     */
    private function mergeDefinition(Characteristic $characteristic, CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row): array
    {
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
            'sort_order' => $row->sort_order,
            'entity' => $row->entity,
            'db_column' => $row->db_column ?? $characteristic->key,
            'min' => $row->min,
            'max' => $row->max,
            'formula' => $row->formula,
            'formula_display' => $row->formula_display,
            'default_value' => $row->default_value,
            'required' => $row->required,
            'validation_message' => $row->validation_message,
            'conversion_formula' => $row->conversion_formula,
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_allowed'] = $row->forgemagie_allowed;
            $out['forgemagie_max'] = $row->forgemagie_max;
            $out['base_price_per_unit'] = $row->base_price_per_unit;
            $out['rune_price_per_unit'] = $row->rune_price_per_unit;
        }
        if ($row instanceof CharacteristicCreature) {
            $out['applies_to'] = $row->applies_to;
            $out['is_competence'] = $row->is_competence;
            $out['value_available'] = $row->value_available;
            $out['labels'] = $row->labels;
            $out['validation'] = $row->validation;
            $out['mastery_value_available'] = $row->mastery_value_available;
            $out['mastery_labels'] = $row->mastery_labels;
        }
        if ($row instanceof CharacteristicObject || $row instanceof CharacteristicSpell) {
            $out['value_available'] = $row->value_available ?? null;
        }
        return $out;
    }
}

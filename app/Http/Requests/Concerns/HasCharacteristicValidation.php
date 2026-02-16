<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Trait pour dériver les règles de validation depuis CharacteristicGetterService.
 * Les règles dépendent du type de la caractéristique : boolean, list (in:...), string/int (min/max).
 *
 * @example
 * 'rarity' => $this->characteristicRules('rarity', 'resource') ?: ['nullable', 'integer', 'min:0', 'max:5'],
 * 'level' => array_merge(['nullable', 'integer'], $this->characteristicMinMaxRules('level', 'item') ?: ['min:0']),
 */
trait HasCharacteristicValidation
{
    /**
     * Retourne les règles Laravel pour un champ caractéristique selon son type en base.
     * - boolean : ['nullable', 'boolean']
     * - list : ['nullable', 'in:val1,val2,...'] (d'après value_available)
     * - string/int : ['nullable', 'integer' ou 'string', 'min:x', 'max:y']
     * Retourne [] si aucune définition en base (à combiner avec un fallback).
     *
     * @return array<int, string>
     */
    protected function characteristicRules(string $field, string $entity, string $numericType = 'integer'): array
    {
        $getter = app(CharacteristicGetterService::class);
        $def = $getter->getDefinitionByField($field, $entity);
        if ($def === null) {
            return [];
        }
        $type = isset($def['type']) ? strtolower((string) $def['type']) : 'string';
        $rules = ['nullable'];

        if ($type === 'boolean' || $type === 'bool') {
            $rules[] = 'boolean';
            return $rules;
        }

        if ($type === 'list' || $type === 'array') {
            $allowed = $def['value_available'] ?? null;
            if (is_array($allowed) && $allowed !== []) {
                $rules[] = $numericType;
                $rules[] = 'in:' . implode(',', array_map(strval(...), $allowed));
            }
            return $rules;
        }

        $limits = $getter->getLimitsByField($field, $entity);
        $rules[] = $numericType;
        if ($limits !== null) {
            if (isset($limits['min'])) {
                $rules[] = 'min:' . $limits['min'];
            }
            if (isset($limits['max'])) {
                $rules[] = 'max:' . $limits['max'];
            }
        }
        return $rules;
    }

    /**
     * Retourne les règles Laravel min/max pour un champ dont les limites sont définies par entité.
     * À utiliser pour les types numériques (string/int). Pour list/boolean, préférer characteristicRules().
     *
     * @return array<int, string> ex. ['min:0', 'max:5']
     */
    protected function characteristicMinMaxRules(string $field, string $entity): array
    {
        $getter = app(CharacteristicGetterService::class);
        $def = $getter->getDefinitionByField($field, $entity);
        if ($def !== null) {
            $type = isset($def['type']) ? strtolower((string) $def['type']) : 'string';
            if ($type === 'list' || $type === 'array' || $type === 'boolean' || $type === 'bool') {
                return [];
            }
        }
        $limits = $getter->getLimitsByField($field, $entity);
        if ($limits === null) {
            return [];
        }
        $rules = [];
        if (isset($limits['min'])) {
            $rules[] = 'min:' . $limits['min'];
        }
        if (isset($limits['max'])) {
            $rules[] = 'max:' . $limits['max'];
        }
        return $rules;
    }
}

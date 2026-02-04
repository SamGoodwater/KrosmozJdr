<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Trait pour dériver les règles de validation min/max depuis CharacteristicGetterService.
 *
 * Option B : les FormRequests utilisent les limites définies en base (characteristic_*)
 * au lieu de règles min/max codées en dur.
 *
 * @example
 * 'rarity' => array_merge(
 *     ['nullable', 'integer'],
 *     $this->characteristicMinMaxRules('rarity', 'resource') ?: ['min:0', 'max:5']
 * ),
 */
trait HasCharacteristicValidation
{
    /**
     * Retourne les règles Laravel min/max pour un champ dont les limites sont définies par entité.
     * Si aucune limite n'est configurée en base, retourne [] (à combiner avec un fallback si besoin).
     *
     * @return array<int, string> ex. ['min:0', 'max:5']
     */
    protected function characteristicMinMaxRules(string $field, string $entity): array
    {
        $getter = app(CharacteristicGetterService::class);
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

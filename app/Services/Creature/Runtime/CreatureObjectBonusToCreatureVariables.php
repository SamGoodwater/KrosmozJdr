<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Applique les bonus agrégés d’objets (clés courtes type Dofus→Krosmoz) sur la carte de variables runtime.
 *
 * Règles :
 * - Clé `{stat}_object` → `{stat}_creature` si la créature a une carac avec db_column (ex. strength → strength_creature).
 * - Compétences D&D : clé anglaise courte (ex. athletics) → colonne bonus français (athletisme_bonus) — voir mapping explicite.
 */
final class CreatureObjectBonusToCreatureVariables
{
    /**
     * Clé JSON objet (sans _object) → nom de variable/colonne utilisé dans les formules [athletisme_bonus], etc.
     *
     * @var array<string, string>
     */
    private const SKILL_SHORT_KEY_TO_BONUS_VARIABLE = [
        'athletics' => 'athletisme_bonus',
        'intimidation' => 'intimidation_bonus',
        'acrobatics' => 'acrobatie_bonus',
        'stealth' => 'discretion_bonus',
        'sleight_of_hand' => 'escamotage_bonus',
        'arcana' => 'arcane_bonus',
        'history' => 'histoire_bonus',
        'investigation' => 'investigation_bonus',
        'nature' => 'nature_bonus',
        'religion' => 'religion_bonus',
        'animal_handling' => 'dressage_bonus',
        'medicine' => 'medecine_bonus',
        'perception' => 'perception_bonus',
        'insight' => 'perspicacite_bonus',
        'survival' => 'survie_bonus',
        'persuasion' => 'persuasion_bonus',
        'performance' => 'representation_bonus',
        'deception' => 'supercherie_bonus',
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter
    ) {}

    /**
     * Fusionne les totaux d’objets dans $variables (modifié en place). Retourne les mêmes totaux pour la payload.
     *
     * @param  array<string, int|float>  $variables
     * @param  array<string, int>  $itemTotals
     * @return array<string, int>
     */
    public function mergeInto(array &$variables, string $entity, array $itemTotals): array
    {
        foreach ($itemTotals as $shortKey => $amount) {
            if ($amount === 0) {
                continue;
            }
            $target = $this->resolveTargetVariable($shortKey, $entity);
            if ($target === null) {
                continue;
            }
            $prev = isset($variables[$target]) ? (float) $variables[$target] : 0.0;
            $variables[$target] = $prev + (float) $amount;
        }

        return $itemTotals;
    }

    private function resolveTargetVariable(string $objectShortKey, string $entity): ?string
    {
        if (isset(self::SKILL_SHORT_KEY_TO_BONUS_VARIABLE[$objectShortKey])) {
            return self::SKILL_SHORT_KEY_TO_BONUS_VARIABLE[$objectShortKey];
        }

        $creatureKey = $objectShortKey.'_creature';
        $def = $this->getter->getDefinition($creatureKey, $entity);
        if ($def !== null && ! empty($def['db_column'])) {
            return $creatureKey;
        }

        return null;
    }
}

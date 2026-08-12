<?php

declare(strict_types=1);

namespace App\Support\Creature;

/**
 * Colonnes `*_mastery` (compétences 0–2) sur `creatures`.
 *
 * @see resources/js/Utils/Entity/buildCreatureCompetenceGroups.js
 */
final class CreatureMasteryColumns
{
    /**
     * @var list<string>
     */
    public const COLUMNS = [
        'acrobatie_mastery',
        'discretion_mastery',
        'escamotage_mastery',
        'athletisme_mastery',
        'intimidation_mastery',
        'dressage_mastery',
        'medecine_mastery',
        'nature_mastery',
        'perception_mastery',
        'perspicacite_mastery',
        'survie_mastery',
        'arcane_mastery',
        'histoire_mastery',
        'investigation_mastery',
        'religion_mastery',
        'supercherie_mastery',
        'representation_mastery',
        'persuasion_mastery',
    ];

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return self::COLUMNS;
    }

    /**
     * Extrait les maîtrises d'une créature pour un payload API tableau.
     *
     * @return array<string, int>
     */
    public static function extractFrom(object $creature): array
    {
        $out = [];
        foreach (self::COLUMNS as $column) {
            $out[$column] = (int) ($creature->{$column} ?? 0);
        }

        return $out;
    }
}

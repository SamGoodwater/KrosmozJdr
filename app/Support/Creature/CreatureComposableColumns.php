<?php

declare(strict_types=1);

namespace App\Support\Creature;

/**
 * Colonnes de `creatures` qui participent au modèle base + objets + contexte.
 *
 * Chaque colonne X peut porter un total explicite (`X`) et un bonus contextuel (`X_context`).
 * Les maîtrises (`*_mastery`) restent des flags hors composition.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
final class CreatureComposableColumns
{
    /**
     * @var list<string>
     */
    public const COLUMNS = [
        // Combat / ressources
        'life', 'pa', 'pm', 'po', 'ini', 'invocation', 'touch', 'ca',
        'dodge_pa', 'dodge_pm', 'fuite', 'tacle', 'critical_hit', 'heal_bonus',
        // Scores
        'vitality', 'sagesse', 'strong', 'intel', 'agi', 'chance',
        // Dégâts fixes
        'do_fixe_neutre', 'do_fixe_terre', 'do_fixe_feu', 'do_fixe_air', 'do_fixe_eau',
        'do_sagesse', 'do_vitalite',
        // Résistances
        'res_fixe_neutre', 'res_fixe_terre', 'res_fixe_feu', 'res_fixe_air', 'res_fixe_eau',
        'res_neutre', 'res_terre', 'res_feu', 'res_air', 'res_eau', 'res_sagesse', 'res_vitalite',
        // Bonus de compétences
        'acrobatie_bonus', 'discretion_bonus', 'escamotage_bonus', 'athletisme_bonus',
        'intimidation_bonus', 'arcane_bonus', 'histoire_bonus', 'investigation_bonus',
        'nature_bonus', 'religion_bonus', 'dressage_bonus', 'medecine_bonus',
        'perception_bonus', 'perspicacite_bonus', 'survie_bonus', 'persuasion_bonus',
        'representation_bonus', 'supercherie_bonus',
        // Bonus de sauvegardes
        'save_vitality_bonus', 'save_wisdom_bonus', 'save_strength_bonus',
        'save_intelligence_bonus', 'save_chance_bonus', 'save_agility_bonus',
    ];

    public static function contextColumn(string $column): string
    {
        return $column.'_context';
    }

    /**
     * @return list<string>
     */
    public static function contextColumns(): array
    {
        return array_map(static fn (string $column): string => self::contextColumn($column), self::COLUMNS);
    }

    public static function isComposable(string $column): bool
    {
        return in_array($column, self::COLUMNS, true);
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return self::COLUMNS;
    }
}

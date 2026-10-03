/**
 * Colonnes créature composables (miroir PHP `CreatureComposableColumns`).
 *
 * @see app/Support/Creature/CreatureComposableColumns.php
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */

/** @type {readonly string[]} */
export const CREATURE_COMPOSABLE_COLUMNS = Object.freeze([
    "life",
    "pa",
    "pm",
    "po",
    "ini",
    "invocation",
    "touch",
    "ca",
    "dodge_pa",
    "dodge_pm",
    "fuite",
    "tacle",
    "critical_hit",
    "heal_bonus",
    "vitality",
    "sagesse",
    "strong",
    "intel",
    "agi",
    "chance",
    "do_fixe_neutre",
    "do_fixe_terre",
    "do_fixe_feu",
    "do_fixe_air",
    "do_fixe_eau",
    "do_fixe_multiple",
    "do_sagesse",
    "do_vitalite",
    "res_fixe_neutre",
    "res_fixe_terre",
    "res_fixe_feu",
    "res_fixe_air",
    "res_fixe_eau",
    "res_neutre",
    "res_terre",
    "res_feu",
    "res_air",
    "res_eau",
    "res_sagesse",
    "res_vitalite",
    "acrobatie_bonus",
    "discretion_bonus",
    "escamotage_bonus",
    "athletisme_bonus",
    "intimidation_bonus",
    "arcane_bonus",
    "histoire_bonus",
    "investigation_bonus",
    "nature_bonus",
    "religion_bonus",
    "dressage_bonus",
    "medecine_bonus",
    "perception_bonus",
    "perspicacite_bonus",
    "survie_bonus",
    "persuasion_bonus",
    "representation_bonus",
    "supercherie_bonus",
    "artisanat_bonus",
    "herbaliste_bonus",
    "connaissance_creatures_bonus",
    "save_vitality_bonus",
    "save_wisdom_bonus",
    "save_strength_bonus",
    "save_intelligence_bonus",
    "save_chance_bonus",
    "save_agility_bonus",
]);

/**
 * @param {string} column
 * @returns {string}
 */
export function contextColumnForComposable(column) {
    return `${column}_context`;
}

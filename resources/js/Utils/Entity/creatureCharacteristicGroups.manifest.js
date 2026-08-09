/**
 * Manifeste canonique des groupes de caractéristiques créature.
 *
 * Une seule source d’ordre / titres pour toutes les densités d’affichage.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */

/** Stats du résumé compact (une rangée plate). */
export const CREATURE_CHARACTERISTIC_SUMMARY_KEYS = Object.freeze([
    "pa",
    "pm",
    "po",
    "life",
    "ini",
]);

/**
 * @typedef {Object} CreatureCharacteristicGroupDef
 * @property {string} id
 * @property {string} title
 * @property {'db'|'modifiers'|'resistances'|'damages'|'saves'} kind
 * @property {string[]} [dbColumns]
 */

/** @type {ReadonlyArray<CreatureCharacteristicGroupDef>} */
export const CREATURE_CHARACTERISTIC_GROUPS = Object.freeze([
    {
        id: "combat",
        title: "Combat",
        kind: "db",
        dbColumns: Object.freeze(["pa", "pm", "po", "life", "ini", "invocation"]),
    },
    {
        id: "stats",
        title: "Stats",
        kind: "db",
        dbColumns: Object.freeze(["strong", "intel", "agi", "chance", "vitality", "sagesse"]),
    },
    {
        id: "modifiers",
        title: "Modificateurs",
        kind: "modifiers",
    },
    {
        id: "resistances",
        title: "Résistances",
        kind: "resistances",
    },
    {
        id: "damages",
        title: "Dommages",
        kind: "damages",
    },
    {
        id: "control",
        title: "Contrôle",
        kind: "db",
        dbColumns: Object.freeze([
            "ca",
            "dodge_pa",
            "dodge_pm",
            "fuite",
            "tacle",
            "critical_hit",
            "heal_bonus",
        ]),
    },
    {
        id: "saves",
        title: "Sauvegardes",
        kind: "saves",
    },
]);

/** Densités de CharacteristicsCard (surfaces UI). */
export const CHARACTERISTIC_CARD_DENSITY = Object.freeze({
    icon: "icon",
    labeled: "labeled",
    spacious: "spacious",
});

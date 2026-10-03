/**
 * Sections d’édition des caractéristiques composables (ordre aligné sur le manifeste d’affichage).
 *
 * @see resources/js/Utils/Entity/creatureCharacteristicGroups.manifest.js
 */

import {
    CREATURE_ABILITY_STAT_DEFS,
    CREATURE_CHARACTERISTIC_GROUPS,
} from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { CREATURE_COMPOSABLE_COLUMNS } from "@/Support/Creature/creatureComposableColumns";

const RESISTANCE_ELEMENTS = ["neutre", "terre", "feu", "air", "eau"];

/** Bonus de compétence (hors sauvegardes déjà dans abilityStack). */
const CREATURE_SKILL_BONUS_COLUMNS = CREATURE_COMPOSABLE_COLUMNS.filter(
    (col) => col.endsWith("_bonus") && !col.startsWith("save_"),
);

/**
 * @typedef {Object} CreatureComposableEditSection
 * @property {string} id
 * @property {string} title
 * @property {string[]} dbColumns
 */

/**
 * @param {Record<string, { name?: string, short_name?: string }>} [byDbColumn]
 * @returns {CreatureComposableEditSection[]}
 */
export function buildCreatureComposableEditSections(byDbColumn = {}) {
    /** @type {CreatureComposableEditSection[]} */
    const sections = [];

    for (const group of CREATURE_CHARACTERISTIC_GROUPS) {
        if (group.kind === "db" && group.dbColumns?.length) {
            sections.push({
                id: group.id,
                title: group.title,
                dbColumns: [...group.dbColumns],
            });
        }
    }

    const abilityColumns = [];
    for (const row of CREATURE_ABILITY_STAT_DEFS) {
        abilityColumns.push(row.stat, row.saveBonusColumn);
    }
    sections.push({
        id: "abilities",
        title: "Caractéristiques & sauvegardes",
        dbColumns: abilityColumns,
    });

    const resistanceColumns = [];
    for (const el of RESISTANCE_ELEMENTS) {
        resistanceColumns.push(`res_fixe_${el}`, `res_${el}`);
    }
    resistanceColumns.push("res_sagesse", "res_vitalite");
    sections.push({
        id: "resistances",
        title: "Résistances",
        dbColumns: resistanceColumns,
    });

    sections.push({
        id: "damages",
        title: "Dommages",
        dbColumns: [
            "touch",
            ...RESISTANCE_ELEMENTS.map((el) => `do_fixe_${el}`),
            "do_fixe_multiple",
            "do_sagesse",
            "do_vitalite",
        ],
    });

    if (CREATURE_SKILL_BONUS_COLUMNS.length > 0) {
        sections.push({
            id: "skills",
            title: "Compétences (bonus)",
            dbColumns: [...CREATURE_SKILL_BONUS_COLUMNS],
        });
    }

    void byDbColumn;

    return sections;
}

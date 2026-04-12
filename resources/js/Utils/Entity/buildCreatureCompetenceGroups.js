import { getByDbColumnMap } from "@/Composables/store/useCharacteristicsStore";
import { getDisplayValue } from "@/Composables/entity/useCharacteristicDisplay";

/**
 * Colonnes `*_mastery` (0–2) dans l’ordre logique d’affichage.
 * @type {readonly string[]}
 */
export const CREATURE_MASTERY_DB_COLUMNS = Object.freeze([
    "acrobatie_mastery",
    "discretion_mastery",
    "escamotage_mastery",
    "athletisme_mastery",
    "intimidation_mastery",
    "dressage_mastery",
    "medecine_mastery",
    "nature_mastery",
    "perception_mastery",
    "perspicacite_mastery",
    "survie_mastery",
    "arcane_mastery",
    "histoire_mastery",
    "investigation_mastery",
    "religion_mastery",
    "supercherie_mastery",
    "representation_mastery",
    "persuasion_mastery",
]);

/**
 * Caractéristique primaire (clé stats créature) associée à chaque maîtrise de compétence (règles 2.2.2 / formules créature).
 * @type {Readonly<Record<string, 'strong'|'agi'|'intel'|'sagesse'|'chance'>>}
 */
export const MASTERY_DB_COLUMN_TO_PRIMARY_STAT = Object.freeze({
    acrobatie_mastery: "agi",
    discretion_mastery: "agi",
    escamotage_mastery: "agi",
    athletisme_mastery: "strong",
    intimidation_mastery: "strong",
    dressage_mastery: "sagesse",
    medecine_mastery: "sagesse",
    perception_mastery: "sagesse",
    perspicacite_mastery: "sagesse",
    survie_mastery: "sagesse",
    arcane_mastery: "intel",
    histoire_mastery: "intel",
    investigation_mastery: "intel",
    nature_mastery: "intel",
    religion_mastery: "intel",
    supercherie_mastery: "chance",
    representation_mastery: "chance",
    persuasion_mastery: "chance",
});

/** Ordre des sous-groupes « par stat » (titres FR). */
const PRIMARY_STAT_ORDER = ["strong", "agi", "intel", "sagesse", "chance"];

const PRIMARY_STAT_TITLE = {
    strong: "Force",
    agi: "Agilité",
    intel: "Intelligence",
    sagesse: "Sagesse",
    chance: "Charisme",
};

/** Libellés courts pour les paliers de maîtrise (0–2). */
const MASTERY_TIER_LABEL = {
    0: "—",
    1: "Maîtrise",
    2: "Expertise",
};

/**
 * @param {string} dbColumn
 * @param {unknown} raw
 * @param {Object} def
 * @returns {string}
 */
function formatMasteryTierDisplay(dbColumn, raw, def) {
    const n = raw === null || raw === undefined || raw === "" ? 0 : Number.parseInt(String(raw), 10);
    if (!Number.isFinite(n)) {
        return getDisplayValue(dbColumn, raw, def);
    }
    if (Object.prototype.hasOwnProperty.call(MASTERY_TIER_LABEL, n)) {
        return MASTERY_TIER_LABEL[n];
    }
    return getDisplayValue(dbColumn, raw, def);
}

/**
 * Construit des groupes pour CharacteristicsCard : une section par caractéristique primaire
 * contenant les compétences (maîtrises) rattachées à cette stat.
 *
 * @param {Object|null} creature - Ligne créature (colonnes `*_mastery`, `level`, etc.)
 * @param {Object} [options]
 * @param {Record<string, Object>} [options.byDbColumn] - Override store (tests)
 * @param {boolean} [options.includeZero=false] - Inclure les maîtrises à 0 (sinon seulement 1 ou 2)
 * @returns {Array<{ title: string, characteristics: Array<{ type: 'formula', def: Object, value: string }> }>}
 */
export function buildCreatureCompetenceGroupsByPrimary(creature, options = {}) {
    if (!creature || typeof creature !== "object") {
        return [];
    }

    const byDb =
        options?.byDbColumn && typeof options.byDbColumn === "object"
            ? options.byDbColumn
            : getByDbColumnMap("creature");
    const getDef = (dbColumn) =>
        byDb[dbColumn] || { key: dbColumn, name: dbColumn, short_name: dbColumn };

    const includeZero = options.includeZero === true;

    /** @type {Record<string, string[]>} */
    const byPrimary = {};
    for (const stat of PRIMARY_STAT_ORDER) {
        byPrimary[stat] = [];
    }

    for (const dbColumn of CREATURE_MASTERY_DB_COLUMNS) {
        const primary = MASTERY_DB_COLUMN_TO_PRIMARY_STAT[dbColumn];
        if (!primary || !byPrimary[primary]) continue;
        const raw = creature[dbColumn];
        const n = raw === null || raw === undefined || raw === "" ? 0 : Number.parseInt(String(raw), 10);
        if (!Number.isFinite(n)) continue;
        if (!includeZero && n <= 0) continue;
        byPrimary[primary].push(dbColumn);
    }

    const groups = [];
    for (const stat of PRIMARY_STAT_ORDER) {
        const columns = byPrimary[stat];
        if (!columns.length) continue;

        const characteristics = [];
        for (const dbColumn of columns) {
            const def = getDef(dbColumn);
            const value = creature[dbColumn];
            const displayValue = formatMasteryTierDisplay(dbColumn, value, def);
            characteristics.push({
                type: "formula",
                def: { ...def, key: def.key || dbColumn },
                value: displayValue,
                formulaResolved: "",
                formulaRaw: "",
            });
        }
        groups.push({
            title: PRIMARY_STAT_TITLE[stat] ?? stat,
            characteristics,
        });
    }

    return groups;
}

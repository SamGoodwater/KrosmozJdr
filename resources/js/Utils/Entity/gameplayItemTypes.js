/**
 * Types d’équipement utiles en jeu (catalogue objets).
 *
 * @description
 * Cosmétiques (apparat, costume, montures d’apparat, percepteur, etc.) restent
 * dans le filtre Type mais ne sont pas cochés par défaut.
 *
 * @example
 * resolveGameplayItemTypeIds([{ id: 12, name: 'Amulette', dofusdb_type_id: 1 }])
 * // ['12']
 */

/** @type {readonly string[]} */
export const GAMEPLAY_ITEM_TYPE_LABELS = Object.freeze([
    "Amulette",
    "Anneau",
    "Arc",
    "Baguette",
    "Bâton",
    "Bottes",
    "Bouclier",
    "Cape",
    "Ceinture",
    "Chapeau",
    "Dague",
    "Dofus",
    "Épée",
    "Familier",
    "Hache",
    "Lance",
    "Marteau",
    "Outil",
    "Pelle",
    "Pioche",
    "Trophée",
]);

/** Ids DofusDB stables (item_types.dofusdb_type_id). */
export const GAMEPLAY_ITEM_TYPE_DOFUS_IDS = Object.freeze([
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 16, 17, 18, 19, 20, 21, 23, 82, 151, 271,
]);

/**
 * @param {unknown} value
 * @returns {string}
 */
export function normalizeItemTypeLabel(value) {
    return String(value ?? "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "")
        .replace(/[\u2018\u2019\u201B`'´]/g, "'")
        .trim();
}

/**
 * @param {unknown} filters
 * @returns {boolean}
 */
export function hasItemTypeFilter(filters) {
    const raw = filters && typeof filters === "object" ? filters.item_type_id : undefined;
    if (raw === null || typeof raw === "undefined" || raw === "") return false;
    if (Array.isArray(raw)) return raw.some((v) => String(v).trim() !== "");
    return String(raw).trim() !== "";
}

/**
 * Résout les ids locaux `item_types.id` à cocher par défaut.
 *
 * @param {Array<Record<string, unknown>>} types - Lignes `{id|value, name|label, dofusdb_type_id?}`
 * @returns {string[]}
 */
export function resolveGameplayItemTypeIds(types) {
    const rows = Array.isArray(types) ? types : [];
    const wantedLabels = new Set(GAMEPLAY_ITEM_TYPE_LABELS.map(normalizeItemTypeLabel));
    const wantedDofus = new Set(GAMEPLAY_ITEM_TYPE_DOFUS_IDS.map(Number));
    const ids = [];
    const seen = new Set();

    for (const row of rows) {
        if (!row || typeof row !== "object") continue;
        const id = row.id ?? row.value;
        if (id === null || typeof id === "undefined" || id === "") continue;

        const dofus = Number(row.dofusdb_type_id ?? row.dofusdbTypeId);
        const label = normalizeItemTypeLabel(row.name ?? row.label);
        const match =
            (Number.isFinite(dofus) && dofus > 0 && wantedDofus.has(dofus)) || wantedLabels.has(label);
        if (!match) continue;

        const key = String(id);
        if (seen.has(key)) continue;
        seen.add(key);
        ids.push(key);
    }

    return ids;
}

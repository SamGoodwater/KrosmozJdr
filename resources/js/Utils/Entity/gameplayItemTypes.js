/**
 * Types d’équipement utiles en jeu (catalogue objets).
 *
 * @description
 * Le filtre Type pré-coche les lignes `show_in_catalog`. Les listes GAMEPLAY_*
 * restent un repli si le flag n’est pas dans le payload (tests / données anciennes).
 *
 * @example
 * resolveGameplayItemTypeIds([{ id: 12, name: 'Amulette', show_in_catalog: true }])
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

/** Ids DofusDB stables (item_types.dofusdb_type_id) — repli si `show_in_catalog` absent. */
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
 * @param {unknown} row
 * @returns {boolean}
 */
export function isShownInCatalog(row) {
    if (!row || typeof row !== "object") return false;
    const v = row.show_in_catalog ?? row.showInCatalog;
    return v === true || v === 1 || v === "1";
}

/**
 * @param {unknown} row
 * @returns {boolean}
 */
export function hasCatalogFlag(row) {
    if (!row || typeof row !== "object") return false;
    return Object.prototype.hasOwnProperty.call(row, "show_in_catalog")
        || Object.prototype.hasOwnProperty.call(row, "showInCatalog");
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
 * Résout les ids locaux à cocher par défaut (`show_in_catalog`, sinon listes fournies).
 *
 * @param {Array<Record<string, unknown>>} types
 * @param {{ labels?: readonly string[], dofusIds?: readonly number[] }} [fallback]
 * @returns {string[]}
 */
export function resolveCatalogTypeIds(types, fallback = {}) {
    const rows = Array.isArray(types) ? types : [];
    const wantedLabels = new Set((fallback.labels || []).map(normalizeItemTypeLabel));
    const wantedDofus = new Set((fallback.dofusIds || []).map(Number));
    const ids = [];
    const seen = new Set();
    let anyFlag = false;

    for (const row of rows) {
        if (!row || typeof row !== "object") continue;
        if (hasCatalogFlag(row)) anyFlag = true;
    }

    if (anyFlag) {
        for (const row of rows) {
            if (!isShownInCatalog(row)) continue;
            const id = row.id ?? row.value;
            if (id === null || typeof id === "undefined" || id === "") continue;
            const key = String(id);
            if (seen.has(key)) continue;
            seen.add(key);
            ids.push(key);
        }
        return ids;
    }

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

/**
 * Résout les ids locaux `item_types.id` à cocher par défaut.
 *
 * @param {Array<Record<string, unknown>>} types
 * @returns {string[]}
 */
export function resolveGameplayItemTypeIds(types) {
    return resolveCatalogTypeIds(types, {
        labels: GAMEPLAY_ITEM_TYPE_LABELS,
        dofusIds: GAMEPLAY_ITEM_TYPE_DOFUS_IDS,
    });
}

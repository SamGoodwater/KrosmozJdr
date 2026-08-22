/**
 * Types de ressource utiles en jeu (catalogue ressources).
 *
 * @description
 * Quêtes, zones, souvenirs, essences de donjon, etc. restent dans le filtre Type
 * mais ne sont pas cochés par défaut.
 *
 * @example
 * resolveGameplayResourceTypeIds([{ id: 8, name: 'Bois', dofusdb_type_id: 38 }])
 * // ['8']
 */

import { normalizeItemTypeLabel } from "@/Utils/Entity/gameplayItemTypes";

/** @type {readonly string[]} */
export const GAMEPLAY_RESOURCE_TYPE_LABELS = Object.freeze([
    "Aile",
    "Alliage",
    "Bois",
    "Bourgeon",
    "Carapace",
    "Carte",
    "Céréale",
    "Champignon",
    "Coquille",
    "Cuir",
    "Écorce",
    "Étoffe",
    "Fleur",
    "Fruit",
    "Galet",
    "Gelée",
    "Graine",
    "Huile",
    "Jetons",
    "Laine",
    "Légume",
    "Liquide",
    "Matériel d'alchimie",
    "Matériel d'exploration",
    "Matériel de mégaltération",
    "Métaria",
    "Minerai",
    "Nourriture pour familier",
    "Œil",
    "Œuf",
    "Orbe de forgemagie",
    "Oreille",
    "Os",
    "Patte",
    "Peau",
    "Peluche",
    "Pierre brute",
    "Pierre précieuse",
    "Planche",
    "Plante",
    "Plume",
    "Poil",
    "Poisson",
    "Poudre",
    "Queue",
    "Racine",
    "Rune astrale",
    "Rune de forgemagie",
    "Sève",
    "Substrat",
    "Teinture",
    "Viande",
    "Vêtement",
]);

/** Ids DofusDB stables (resource_types.dofusdb_type_id). */
export const GAMEPLAY_RESOURCE_TYPE_DOFUS_IDS = Object.freeze([
    34, 35, 36, 38, 39, 40, 41, 46, 47, 48, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 68,
    70, 71, 78, 95, 96, 98, 103, 104, 105, 106, 107, 108, 109, 110, 111, 119, 148, 152, 164, 174, 183,
    185, 189, 195, 209, 228, 233, 262,
]);

/**
 * @param {unknown} filters
 * @returns {boolean}
 */
export function hasResourceTypeFilter(filters) {
    const raw = filters && typeof filters === "object" ? filters.resource_type_id : undefined;
    if (raw === null || typeof raw === "undefined" || raw === "") return false;
    if (Array.isArray(raw)) return raw.some((v) => String(v).trim() !== "");
    return String(raw).trim() !== "";
}

/**
 * Résout les ids locaux `resource_types.id` à cocher par défaut.
 *
 * @param {Array<Record<string, unknown>>} types - Lignes `{id|value, name|label, dofusdb_type_id?}`
 * @returns {string[]}
 */
export function resolveGameplayResourceTypeIds(types) {
    const rows = Array.isArray(types) ? types : [];
    const wantedLabels = new Set(GAMEPLAY_RESOURCE_TYPE_LABELS.map(normalizeItemTypeLabel));
    const wantedDofus = new Set(GAMEPLAY_RESOURCE_TYPE_DOFUS_IDS.map(Number));
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

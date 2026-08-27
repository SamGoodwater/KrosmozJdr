/**
 * Types de consommables utiles en jeu (catalogue).
 *
 * @description
 * Certificats, fées d’artifice, coffres, temporis, etc. restent dans le filtre
 * Type mais ne sont pas cochés par défaut.
 *
 * @example
 * resolveGameplayConsumableTypeIds([{ id: 4, name: 'Potion', dofusdb_type_id: 12 }])
 * // ['4']
 */

import { resolveCatalogTypeIds } from "@/Utils/Entity/gameplayItemTypes";

/** @type {readonly string[]} */
export const GAMEPLAY_CONSUMABLE_TYPE_LABELS = Object.freeze([
    "Bière",
    "Boisson",
    "Bourse",
    "Document",
    "Éclats",
    "Éklâme",
    "Figurine",
    "Friandise",
    "Malédiction",
    "Nourriture boost",
    "Objet de Mutation",
    "Objet invisible",
    "Objet utilisable",
    "Pain",
    "Parchemin d'attitude",
    "Parchemin de caractéristique",
    "Parchemin de sortilège",
    "Pierre d'âme",
    "Pierre d'âme pleine",
    "Pierre magique",
    "Poisson comestible",
    "Potion",
    "Potion de téléportation",
    "Sac de ressources",
    "Viande comestible",
    "Viande primitive",
]);

/** Ids DofusDB stables (`consumable_types.dofusdb_type_id`). */
export const GAMEPLAY_CONSUMABLE_TYPE_DOFUS_IDS = Object.freeze([
    12, 25, 27, 28, 30, 33, 37, 42, 43, 49, 69, 75, 76, 79, 83, 85, 88, 94, 100,
    157, 173, 187, 203, 216, 310, 322,
]);

/**
 * @param {unknown} filters
 * @returns {boolean}
 */
export function hasConsumableTypeFilter(filters) {
    const raw = filters && typeof filters === "object" ? filters.consumable_type_id : undefined;
    if (raw === null || typeof raw === "undefined" || raw === "") return false;
    if (Array.isArray(raw)) return raw.some((v) => String(v).trim() !== "");
    return String(raw).trim() !== "";
}

/**
 * Résout les ids locaux `consumable_types.id` à cocher par défaut.
 *
 * @param {Array<Record<string, unknown>>} types - Lignes `{id|value, name|label, dofusdb_type_id?}`
 * @returns {string[]}
 */
export function resolveGameplayConsumableTypeIds(types) {
    return resolveCatalogTypeIds(types, {
        labels: GAMEPLAY_CONSUMABLE_TYPE_LABELS,
        dofusIds: GAMEPLAY_CONSUMABLE_TYPE_DOFUS_IDS,
    });
}

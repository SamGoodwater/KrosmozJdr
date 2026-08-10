/**
 * Charge des modèles d’entité via `api.tables.*` (whitelist d’ids).
 *
 * @description
 * Utilisé par favoris / recherche pour hydrater une fiche avant modal ou vue Minimal.
 *
 * @example
 * const monsters = await fetchEntityModelsByIds('monsters', [1, 2]);
 */
import { getEntityConfig, normalizeEntityType } from "@/Entities/entity-registry";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";

/** @type {Readonly<Record<string, string>>} */
export const ENTITY_VIEW_PROP_BY_TYPE = Object.freeze({
    monsters: "monster",
    spells: "spell",
    items: "item",
    resources: "resource",
    consumables: "consumable",
    breeds: "breed",
    panoplies: "panoply",
    capabilities: "capability",
    npcs: "npc",
    conditions: "condition",
    shops: "shop",
    campaigns: "campaign",
    scenarios: "scenario",
    specializations: "specialization",
    "creature-traits": "creatureTrait",
    "resource-types": "resourceType",
});

/**
 * @param {string} entityType
 * @returns {string}
 */
export function entityViewPropName(entityType) {
    const t = normalizeActionEntityType(normalizeEntityType(entityType) || entityType);
    return ENTITY_VIEW_PROP_BY_TYPE[t] || "entity";
}

/**
 * Types qui ont une vue Minimal / Texte catalogue (hors pages CMS).
 * @param {string} entityType
 * @returns {boolean}
 */
export function supportsEntityCatalogViews(entityType) {
    const key = normalizeEntityType(entityType);
    return Boolean(getEntityConfig(key)?.model) && Boolean(ENTITY_VIEW_PROP_BY_TYPE[normalizeActionEntityType(key)]);
}

/**
 * @param {string} entityType
 * @param {Array<number|string>} ids
 * @returns {Promise<object[]>}
 */
export async function fetchEntityModelsByIds(entityType, ids) {
    const key = normalizeEntityType(entityType);
    const uniqueIds = [...new Set((ids || []).map((id) => String(id).trim()).filter(Boolean))];
    if (!key || uniqueIds.length === 0) return [];

    const config = getEntityConfig(key);
    let routeName = `api.tables.${key}`;
    try {
        route(routeName);
    } catch {
        return [];
    }

    const params = new URLSearchParams();
    params.set("format", "entities");
    params.set("limit", String(Math.max(uniqueIds.length, 1)));
    for (const id of uniqueIds) {
        params.append("whitelist[]", String(id));
    }

    const res = await fetch(`${route(routeName)}?${params.toString()}`, {
        headers: { Accept: "application/json" },
        credentials: "same-origin",
    });
    if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
    }
    const data = await res.json();
    const rawList = Array.isArray(data?.entities) ? data.entities : [];
    if (config?.model && typeof config.model.fromArray === "function") {
        return config.model.fromArray(rawList);
    }
    return rawList;
}

/**
 * @param {string} entityType
 * @param {number|string} id
 * @returns {Promise<object|null>}
 */
export async function fetchEntityModelById(entityType, id) {
    const list = await fetchEntityModelsByIds(entityType, [id]);
    return list[0] ?? null;
}

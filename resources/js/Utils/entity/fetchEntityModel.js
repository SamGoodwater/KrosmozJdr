/**
 * Charge un modèle d’entité via `api.tables.*` (format entities).
 *
 * @param {string} entityType
 * @param {number|string} entityId
 * @returns {Promise<object|null>}
 */
import { getEntityConfig, normalizeEntityType } from "@/Entities/entity-registry";

const PROP_BY_TYPE = Object.freeze({
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
export function entityPropNameForType(entityType) {
    const type = normalizeEntityType(entityType);
    return PROP_BY_TYPE[type] || "entity";
}

/**
 * @param {string} entityType
 * @returns {boolean}
 */
export function canOpenEntityModal(entityType) {
    const type = normalizeEntityType(entityType);
    return Boolean(getEntityConfig(type)?.model);
}

/**
 * @param {string} entityType
 * @param {number|string} entityId
 * @returns {Promise<object|null>}
 */
export async function fetchEntityModel(entityType, entityId) {
    const type = normalizeEntityType(entityType);
    const id = String(entityId ?? "").trim();
    if (!type || !id) return null;

    const config = getEntityConfig(type);
    if (!config?.model) return null;

    let routeName = `api.tables.${type}`;
    try {
        route(routeName);
    } catch {
        return null;
    }

    const url = `${route(routeName)}?format=entities&limit=1&filters[id]=${encodeURIComponent(id)}`;
    const res = await fetch(url, {
        headers: { Accept: "application/json" },
        credentials: "same-origin",
    });
    if (!res.ok) return null;
    const data = await res.json();
    const raw = Array.isArray(data?.entities) ? data.entities[0] : null;
    if (!raw) return null;

    const ModelClass = config.model;
    if (typeof ModelClass.fromArray === "function") {
        return ModelClass.fromArray([raw])[0] ?? raw;
    }
    return raw;
}

/**
 * @param {string} entityType
 * @param {Array<number|string>} ids
 * @returns {Promise<object[]>}
 */
export async function fetchEntityModels(entityType, ids) {
    const type = normalizeEntityType(entityType);
    const list = [...new Set((ids || []).map((v) => String(v).trim()).filter(Boolean))];
    if (!type || list.length === 0) return [];

    const config = getEntityConfig(type);
    if (!config?.model) return [];

    let routeName = `api.tables.${type}`;
    try {
        route(routeName);
    } catch {
        return [];
    }

    const url = `${route(routeName)}?format=entities&limit=${Math.min(500, list.length)}&whitelist=${encodeURIComponent(list.join(","))}`;
    const res = await fetch(url, {
        headers: { Accept: "application/json" },
        credentials: "same-origin",
    });
    if (!res.ok) return [];
    const data = await res.json();
    const entities = Array.isArray(data?.entities) ? data.entities : [];
    const ModelClass = config.model;
    if (typeof ModelClass.fromArray === "function") {
        return ModelClass.fromArray(entities);
    }
    return entities;
}

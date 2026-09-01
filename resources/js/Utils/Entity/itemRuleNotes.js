/**
 * Notes de fiche objet (règles faciles à oublier à table).
 *
 * @example
 * itemRuleNotes({ itemType: { name: "Monture" } });
 */

export const MOUNT_RULE_NOTE = "Bonus (ex. PM) hors plafond ; perdus si tu descends.";

const MOUNT_TYPE_NAMES = new Set(["monture", "dragodinde"]);

/**
 * @param {unknown} value
 * @returns {string}
 */
function normalizeTypeName(value) {
    return String(value ?? "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

/**
 * @param {object|null|undefined} entity
 * @returns {string}
 */
export function itemTypeName(entity) {
    if (entity == null || typeof entity !== "object") {
        return "";
    }
    const data = entity._data && typeof entity._data === "object" ? entity._data : entity;
    const type = entity.itemType ?? entity.item_type ?? data.itemType ?? data.item_type;
    if (typeof type === "string") {
        return type;
    }
    if (type && typeof type === "object") {
        return String(type.name ?? type.label ?? "");
    }
    return "";
}

/**
 * @param {object|null|undefined} entity
 * @returns {string[]}
 */
export function itemRuleNotes(entity) {
    const name = normalizeTypeName(itemTypeName(entity));
    if (MOUNT_TYPE_NAMES.has(name) || name.includes("monture") || name.includes("dragodinde")) {
        return [MOUNT_RULE_NOTE];
    }
    return [];
}

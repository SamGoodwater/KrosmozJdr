/**
 * Notes de fiche consommable (règles faciles à oublier à table).
 *
 * @example
 * consumableRuleNotes({ consumableType: { name: "Potion" } });
 */

export const CONSUMABLE_STACK_NOTE =
    "Même type d’effet : pas de cumul, le meilleur gagne.";

export const LEARN_SCROLL_NOTE = "Détruit seulement si le sort réussit.";

const LEARN_SCROLL_TYPE_NAMES = new Set(["parchemin de sortilege"]);

const BUFF_CONSUMABLE_TYPE_NAMES = new Set([
    "potion",
    "nourriture boost",
    "pain",
    "biere",
    "boisson",
    "friandise",
    "viande comestible",
    "poisson comestible",
    "malediction",
    "benediction",
    "roleplay buffs",
    "potion de monture",
    "potion de teleportation",
]);

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
export function consumableTypeName(entity) {
    if (entity == null || typeof entity !== "object") {
        return "";
    }
    const data = entity._data && typeof entity._data === "object" ? entity._data : entity;
    const type =
        entity.consumableType ??
        entity.consumable_type ??
        data.consumableType ??
        data.consumable_type;
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
export function consumableRuleNotes(entity) {
    const notes = [];
    const name = normalizeTypeName(consumableTypeName(entity));
    if (LEARN_SCROLL_TYPE_NAMES.has(name) || name.includes("parchemin de sortilege")) {
        notes.push(LEARN_SCROLL_NOTE);
        return notes;
    }
    if (BUFF_CONSUMABLE_TYPE_NAMES.has(name)) {
        notes.push(CONSUMABLE_STACK_NOTE);
    }
    return notes;
}

/**
 * Helpers d'affichage unifiés pour les états (entité Condition) et paramètres d’effets.
 */

import { toDisplayLabel } from "@/Utils/dofus/dofusHyperlinkText";

export const CONDITION_DISPELLABLE_ICON = "icons/caracteristics/unenchantable.webp";
export const CONDITION_NOT_DISPELLABLE_ICON = "icons/caracteristics/notUnenchantable.webp";

/**
 * Dissipabilité d’un état référencé en base : par défaut oui si absent / null.
 * @param {boolean|null|undefined} value
 * @returns {boolean}
 */
export function resolveEntityDissipable(value) {
    if (value === null || value === undefined) return true;
    return Boolean(value);
}

/**
 * Normalise un mode d'application d'condition.
 * @param {string|null|undefined} value
 * @returns {'self'|'target'}
 */
export function resolveConditionMode(value) {
    const v = String(value || "").trim().toLowerCase();
    if (v === "self" || v === "s-appliquer-etat") return "self";
    return "target";
}

/**
 * Libellé humain du mode d'application.
 * @param {string|null|undefined} value
 * @param {{ variant?: 'inline'|'table' }} [options]
 * @returns {string}
 */
export function formatConditionMode(value, options = {}) {
    const mode = resolveConditionMode(value);
    const variant = options?.variant || "inline";
    if (variant === "table") return mode === "self" ? "Lanceur" : "Cible";
    return mode === "self" ? "sur lanceur" : "sur cible";
}

/**
 * Formate la durée d'condition.
 * @param {number|string|null|undefined} value
 * @returns {string|null}
 */
export function formatConditionDuration(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) return null;
    return `durée: ${num} tour(s)`;
}

/**
 * Formate la dissipabilité (effet ou état).
 * @param {boolean|null|undefined} value
 * @returns {string|null}
 */
export function formatConditionDispellable(value) {
    if (typeof value !== "boolean") return null;
    return value ? "Dissipable" : "Non dissipable";
}

/**
 * Retourne la source d'icône selon la dissipabilité.
 * @param {boolean|null|undefined} value
 * @returns {string|null}
 */
export function getConditionDispellableIcon(value) {
    if (typeof value !== "boolean") return null;
    return value ? CONDITION_DISPELLABLE_ICON : CONDITION_NOT_DISPELLABLE_ICON;
}

/**
 * Formate le masque de cible d'un condition.
 * @param {string|null|undefined} value
 * @returns {string|null}
 */
export function formatConditionMask(value) {
    const mask = typeof value === "string" ? value.trim() : "";
    if (mask === "") return null;
    return `masque: ${mask}`;
}

/**
 * Formate "Nom (#ID)".
 * @param {string|null|undefined} name
 * @param {number|string|null|undefined} id
 * @returns {string}
 */
export function formatConditionIdentity(name, id) {
    const stateName = toDisplayLabel(typeof name === "string" ? name : "");
    const num = Number(id);
    const hasId = Number.isFinite(num);
    if (stateName && hasId) return `${stateName} (#${num})`;
    if (stateName) return stateName;
    if (hasId) return `État #${num}`;
    return "État inconnu";
}

/**
 * Construit un méta-texte unifié (durée, dissipable, masque).
 * @param {{duration?: number|string|null, dispellable?: boolean|null, dissipable?: boolean|null, targetMask?: string|null}} data
 * @param {string} [separator]
 * @returns {string}
 */
export function formatConditionMeta(data, separator = " · ") {
    const disp =
        typeof data?.dispellable === "boolean"
            ? data.dispellable
            : typeof data?.dissipable === "boolean"
              ? data.dissipable
              : null;
    const parts = [
        formatConditionDuration(data?.duration),
        formatConditionDispellable(disp),
        formatConditionMask(data?.targetMask),
    ].filter(Boolean);
    return parts.join(separator);
}

/**
 * Flags mécaniques affichés sur les fiches d’état (hors métadonnées UI Dofus).
 * @type {ReadonlyArray<{key: string, label: string}>}
 */
export const CONDITION_MECHANICAL_FLAGS = Object.freeze([
    { key: "prevents_spell_cast", label: "Empêche de lancer des sorts" },
    { key: "prevents_fight", label: "Empêche de combattre" },
    { key: "cant_be_moved", label: "Ne peut pas être déplacé" },
    { key: "cant_be_pushed", label: "Ne peut pas être poussé" },
    { key: "cant_deal_damage", label: "Ne peut pas infliger de dégâts" },
    { key: "invulnerable", label: "Invulnérable" },
    { key: "cant_switch_position", label: "Ne peut pas échanger de position" },
    { key: "incurable", label: "Incurable" },
    { key: "invulnerable_melee", label: "Invulnérable au corps à corps" },
    { key: "invulnerable_range", label: "Invulnérable à distance" },
    { key: "cant_tackle", label: "Ne peut pas tacler" },
    { key: "cant_be_tackled", label: "Ne peut pas être taclé" },
]);

/**
 * États de publication précochés dans le catalogue (Brut décoché).
 * @type {ReadonlyArray<string>}
 */
export const CONDITION_CATALOG_STATE_DEFAULT = Object.freeze([
    "playable",
]);

/**
 * Lit un booléen de flag depuis une instance Condition ou un payload brut.
 *
 * @param {object|null|undefined} entity
 * @param {string} key
 * @returns {boolean}
 *
 * @example
 * readConditionFlag({ cant_be_moved: true }, "cant_be_moved");
 * // true
 */
export function readConditionFlag(entity, key) {
    if (!entity || typeof entity !== "object" || !key) return false;
    const nested = entity._data && typeof entity._data === "object" ? entity._data : null;
    const raw = entity[key];
    if (typeof raw === "boolean") return raw;
    if (nested && typeof nested[key] === "boolean") return nested[key];
    return Boolean(raw ?? nested?.[key]);
}

/**
 * Flags mécaniques actifs d’un état, avec libellé.
 *
 * @param {object|null|undefined} entity
 * @returns {Array<{key: string, label: string}>}
 *
 * @example
 * listActiveMechanicalFlags({ cant_be_moved: true });
 * // [{ key: "cant_be_moved", label: "Ne peut pas être déplacé" }]
 */
export function listActiveMechanicalFlags(entity) {
    return CONDITION_MECHANICAL_FLAGS.filter((flag) => readConditionFlag(entity, flag.key));
}


/**
 * Helpers d'affichage unifiés pour les conditions.
 */

export const CONDITION_DISPELLABLE_ICON = "icons/caracteristics/unenchantable.webp";
export const CONDITION_NOT_DISPELLABLE_ICON = "icons/caracteristics/notUnenchantable.webp";

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
 * Formate la dissipabilité d'un condition.
 * @param {boolean|null|undefined} value
 * @returns {string|null}
 */
export function formatConditionDispellable(value) {
    if (typeof value !== "boolean") return null;
    return value ? "dissipable" : "non dissipable";
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
    const stateName = typeof name === "string" ? name.trim() : "";
    const num = Number(id);
    const hasId = Number.isFinite(num);
    if (stateName && hasId) return `${stateName} (#${num})`;
    if (stateName) return stateName;
    if (hasId) return `Condition #${num}`;
    return "Condition inconnu";
}

/**
 * Construit un méta-texte unifié (durée, dissipable, masque).
 * @param {{duration?: number|string|null, dispellable?: boolean|null, targetMask?: string|null}} data
 * @param {string} [separator]
 * @returns {string}
 */
export function formatConditionMeta(data, separator = " · ") {
    const parts = [
        formatConditionDuration(data?.duration),
        formatConditionDispellable(data?.dispellable),
        formatConditionMask(data?.targetMask),
    ].filter(Boolean);
    return parts.join(separator);
}


/**
 * Helpers d'affichage unifiés pour les états de sorts.
 */

export const SPELL_STATE_DISPELLABLE_ICON = "icons/caracteristics/unenchantable.webp";
export const SPELL_STATE_NOT_DISPELLABLE_ICON = "icons/caracteristics/notUnenchantable.webp";

/**
 * Normalise un mode d'application d'état.
 * @param {string|null|undefined} value
 * @returns {'self'|'target'}
 */
export function resolveSpellStateMode(value) {
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
export function formatSpellStateMode(value, options = {}) {
    const mode = resolveSpellStateMode(value);
    const variant = options?.variant || "inline";
    if (variant === "table") return mode === "self" ? "Lanceur" : "Cible";
    return mode === "self" ? "sur lanceur" : "sur cible";
}

/**
 * Formate la durée d'état.
 * @param {number|string|null|undefined} value
 * @returns {string|null}
 */
export function formatSpellStateDuration(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) return null;
    return `durée: ${num} tour(s)`;
}

/**
 * Formate la dissipabilité d'un état.
 * @param {boolean|null|undefined} value
 * @returns {string|null}
 */
export function formatSpellStateDispellable(value) {
    if (typeof value !== "boolean") return null;
    return value ? "dissipable" : "non dissipable";
}

/**
 * Retourne la source d'icône selon la dissipabilité.
 * @param {boolean|null|undefined} value
 * @returns {string|null}
 */
export function getSpellStateDispellableIcon(value) {
    if (typeof value !== "boolean") return null;
    return value ? SPELL_STATE_DISPELLABLE_ICON : SPELL_STATE_NOT_DISPELLABLE_ICON;
}

/**
 * Formate le masque de cible d'un état.
 * @param {string|null|undefined} value
 * @returns {string|null}
 */
export function formatSpellStateMask(value) {
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
export function formatSpellStateIdentity(name, id) {
    const stateName = typeof name === "string" ? name.trim() : "";
    const num = Number(id);
    const hasId = Number.isFinite(num);
    if (stateName && hasId) return `${stateName} (#${num})`;
    if (stateName) return stateName;
    if (hasId) return `État #${num}`;
    return "État inconnu";
}

/**
 * Construit un méta-texte unifié (durée, dissipable, masque).
 * @param {{duration?: number|string|null, dispellable?: boolean|null, targetMask?: string|null}} data
 * @param {string} [separator]
 * @returns {string}
 */
export function formatSpellStateMeta(data, separator = " · ") {
    const parts = [
        formatSpellStateDuration(data?.duration),
        formatSpellStateDispellable(data?.dispellable),
        formatSpellStateMask(data?.targetMask),
    ].filter(Boolean);
    return parts.join(separator);
}


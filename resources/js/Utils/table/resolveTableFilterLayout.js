/**
 * Contrôle d’un filtre de tableau : liste (menu), interrupteur, saisie, ou plage.
 *
 * @example
 * resolveTableFilterLayout({ type: "multi" }) // "menu"
 * resolveTableFilterLayout({ type: "range" }) // "range"
 */

export const TABLE_FILTER_MENU_SEARCH_MIN_OPTIONS = 8;

/**
 * @param {object} params
 * @param {string} [params.type]
 * @param {boolean} [params.isBooleanSelect]
 * @returns {"toggle"|"text"|"menu"|"range"|"unsupported"}
 */
export function resolveTableFilterLayout({ type, isBooleanSelect = false } = {}) {
    const kind = String(type || "");
    if (kind === "toggle" || kind === "boolean" || isBooleanSelect) {
        return "toggle";
    }
    if (kind === "text") {
        return "text";
    }
    if (kind === "range") {
        return "range";
    }
    if (kind === "multi" || kind === "select") {
        return "menu";
    }
    return "unsupported";
}

/**
 * Recherche dans le menu seulement si la liste est longue.
 *
 * @param {object} params
 * @param {number} [params.optionCount]
 * @param {boolean} [params.searchable]
 * @returns {boolean}
 */
export function shouldShowTableFilterMenuSearch({ optionCount = 0, searchable = true } = {}) {
    if (searchable === false) {
        return false;
    }
    return Number(optionCount) > TABLE_FILTER_MENU_SEARCH_MIN_OPTIONS;
}

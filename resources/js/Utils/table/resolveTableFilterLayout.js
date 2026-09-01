/**
 * Densité d’un filtre de tableau : menu (défaut, compact) ou chips (opt-in).
 *
 * @example
 * resolveTableFilterLayout({ type: "multi", optionCount: 6 }) // "menu"
 * resolveTableFilterLayout({ type: "multi", uiLayout: "chips", optionCount: 6 }) // "chips"
 */

export const TABLE_FILTER_CHIP_MAX_OPTIONS = 8;

/**
 * @param {object} params
 * @param {string} [params.type]
 * @param {string} [params.uiLayout] `"chips"` | `"menu"` (forcé)
 * @param {number} [params.optionCount]
 * @param {boolean} [params.isBooleanSelect]
 * @returns {"toggle"|"text"|"chips"|"menu"|"range"|"unsupported"}
 */
export function resolveTableFilterLayout({
    type,
    uiLayout = "",
    optionCount = 0,
    isBooleanSelect = false,
} = {}) {
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
    if (kind !== "multi" && kind !== "select") {
        return "unsupported";
    }

    const forced = String(uiLayout || "").toLowerCase();
    if (forced === "chips" || forced === "menu") {
        return forced;
    }

    return "menu";
}

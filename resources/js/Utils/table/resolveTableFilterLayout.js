/**
 * Densité d’un filtre de tableau : chips (petit ensemble) ou menu (liste longue).
 *
 * @example
 * resolveTableFilterLayout({ type: "multi", optionCount: 6 }) // "chips"
 * resolveTableFilterLayout({ type: "multi", optionCount: 40 }) // "menu"
 */

export const TABLE_FILTER_CHIP_MAX_OPTIONS = 8;

/**
 * @param {object} params
 * @param {string} [params.type]
 * @param {string} [params.uiLayout] `"chips"` | `"menu"` (forcé)
 * @param {number} [params.optionCount]
 * @param {boolean} [params.isBooleanSelect]
 * @returns {"toggle"|"text"|"chips"|"menu"|"unsupported"}
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
    if (kind !== "multi" && kind !== "select") {
        return "unsupported";
    }

    const forced = String(uiLayout || "").toLowerCase();
    if (forced === "chips" || forced === "menu") {
        return forced;
    }

    const count = Number(optionCount);
    if (Number.isFinite(count) && count > 0 && count <= TABLE_FILTER_CHIP_MAX_OPTIONS) {
        return "chips";
    }
    return "menu";
}

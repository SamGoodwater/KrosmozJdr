/**
 * Résumé lisible d'une caractéristique, séparé par origine
 * (personnage / PNJ, créature, équipement, sort).
 *
 * @example
 * characteristicScopeLabel({ group: 'creature', entity: '*' }) // 'Personnage et PNJ'
 */

/**
 * @param {string|null|undefined} rawKey
 * @returns {string}
 */
export function canonicalCharacteristicKey(rawKey) {
    return String(rawKey || "").replace(/_(creature|object|spell)$/i, "");
}

/**
 * @param {{ group?: string, entity?: string }|null|undefined} row
 * @returns {string}
 */
export function characteristicScopeLabel(row) {
    const group = String(row?.group || "");
    const entity = String(row?.entity || "*");
    if (group === "creature" && entity === "*") return "Personnage et PNJ";
    if (group === "creature" && entity === "monster") return "Créature";
    if (group === "creature") return "Créature";
    if (group === "object") return "Équipement";
    if (group === "spell") return "Sort";
    return "Valeur";
}

/**
 * @param {unknown} min
 * @param {unknown} max
 * @returns {string}
 *
 * @example
 * formatCharacteristicBoundRange('3', '6') // '3 à 6'
 * formatCharacteristicBoundRange(null, '3') // 'maximum 3'
 */
export function formatCharacteristicBoundRange(min, max) {
    const hasMin = min != null && String(min).trim() !== "";
    const hasMax = max != null && String(max).trim() !== "";
    if (hasMin && hasMax) return `${min} à ${max}`;
    if (hasMin) return `minimum ${min}`;
    if (hasMax) return `maximum ${max}`;
    return "";
}

/**
 * @param {unknown} value
 * @returns {string}
 */
function clean(value) {
    if (value == null) return "";
    return String(value).trim();
}

/**
 * @param {Array<Record<string, unknown>>|null|undefined} rows
 * @returns {Array<{ id: string, label: string, detail: string, formula: string }>}
 */
export function buildCharacteristicKrefScopes(rows) {
    const list = Array.isArray(rows) ? rows : [];
    const groupOrder = { creature: 0, object: 1, spell: 2 };
    const entityOrder = { "*": 0, monster: 1 };

    return [...list]
        .sort((a, b) => {
            const groupDelta = (groupOrder[String(a?.group)] ?? 9) - (groupOrder[String(b?.group)] ?? 9);
            if (groupDelta !== 0) return groupDelta;
            return (entityOrder[String(a?.entity || "*")] ?? 5) - (entityOrder[String(b?.entity || "*")] ?? 5);
        })
        .map((row) => {
            const formula = clean(row?.formula_display) || clean(row?.formula);
            const range = formatCharacteristicBoundRange(row?.min, row?.max);
            const defaultValue = clean(row?.default_value);
            const showDefault = defaultValue !== "" && defaultValue !== "0";
            const forgeRaw = row?.group === "object" ? row?.forgemagie_max_bonus : null;
            const forge = forgeRaw != null && String(forgeRaw).trim() !== "" && Number(forgeRaw) > 0
                ? `forgemagie +${forgeRaw}`
                : "";
            const rangeText = row?.group === "object" && range ? `sur un objet : ${range}` : range;
            const detail = [rangeText, showDefault ? `défaut ${defaultValue}` : "", forge].filter(Boolean).join(" · ");
            const entity = String(row?.entity || "*");
            return {
                id: `${row?.group || "value"}:${entity}:${row?.key || ""}`,
                label: characteristicScopeLabel(row),
                detail,
                formula,
            };
        })
        .filter((scope) => scope.detail !== "" || scope.formula !== "");
}

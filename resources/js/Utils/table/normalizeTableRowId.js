/**
 * Normalise un id de ligne tableau (JSON string vs number).
 *
 * @param {unknown} id
 * @returns {number|string|null}
 *
 * @example
 * normalizeTableRowId("12") // 12
 * normalizeTableRowId(12) // 12
 */
export function normalizeTableRowId(id) {
    if (id === null || typeof id === "undefined" || id === "") {
        return null;
    }
    if (typeof id === "number") {
        return Number.isFinite(id) ? id : null;
    }
    const asString = String(id).trim();
    if (asString === "") {
        return null;
    }
    const asNumber = Number(asString);
    if (Number.isFinite(asNumber) && String(asNumber) === asString) {
        return asNumber;
    }
    return asString;
}

/**
 * @param {Iterable<unknown>|null|undefined} ids
 * @returns {Set<number|string>}
 */
export function toSelectedIdSet(ids) {
    const next = new Set();
    if (!ids) {
        return next;
    }
    for (const id of ids) {
        const normalized = normalizeTableRowId(id);
        if (normalized !== null) {
            next.add(normalized);
        }
    }
    return next;
}

/**
 * @param {Set<unknown>} selected
 * @param {unknown} id
 * @returns {boolean}
 */
export function selectedSetHas(selected, id) {
    const normalized = normalizeTableRowId(id);
    if (normalized === null || !selected) {
        return false;
    }
    return selected.has(normalized);
}

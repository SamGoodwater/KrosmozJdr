/**
 * Normalise les panoplies embarquées sur un équipement.
 *
 * @example
 * itemPanopliesFrom(item);
 */

/**
 * @param {object|null|undefined} item
 * @returns {Array<{ id: number, name?: string, bonus?: unknown, items?: Array }>}
 */
export function itemPanopliesFrom(item) {
    const raw = item?.panoplies ?? item?._data?.panoplies ?? [];
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter((panoply) => panoply && panoply.id != null);
}

/**
 * Pièces du set hors l’équipement courant (tooltip « autres équipements »).
 *
 * @param {object|null|undefined} panoply
 * @param {number|string|null|undefined} currentItemId
 * @returns {Array}
 *
 * @example
 * otherPanoplyItems(panoply, item.id);
 */
export function otherPanoplyItems(panoply, currentItemId) {
    const items = Array.isArray(panoply?.items) ? panoply.items : [];
    const current = currentItemId != null && currentItemId !== "" ? Number(currentItemId) : null;
    if (!Number.isFinite(current)) {
        return items.filter((row) => row && row.id != null);
    }
    return items.filter((row) => row && row.id != null && Number(row.id) !== current);
}

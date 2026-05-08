/**
 * Fusionne les entités déjà liées et le référentiel pour l’auto-complétion (id → objet).
 *
 * @param {Array<{ id?: unknown }>} relations
 * @param {Array<{ id?: unknown }>} available
 * @returns {Map<number, object>}
 */
export function mergeEntityOptionsById(relations, available) {
    const map = new Map();
    for (const item of relations || []) {
        if (item?.id != null) {
            map.set(Number(item.id), item);
        }
    }
    for (const item of available || []) {
        if (item?.id == null) {
            continue;
        }
        const id = Number(item.id);
        if (!map.has(id)) {
            map.set(id, item);
        }
    }
    return map;
}

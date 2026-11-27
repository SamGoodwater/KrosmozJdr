/**
 * Payload de synchronisation pour {@link EntityRelationsManager}.
 *
 * Les relations nivelées (`level`, éventuellement `quantity`) attendent
 * `[{ id, level, quantity? }]`. Les autres pivots (quantité/prix boutique,
 * emplacements de sorts) restent `{ [id]: { … } }`.
 *
 * @param {Array<number|string>} itemIds
 * @param {string[]|null|undefined} pivotFields
 * @param {Record<string|number, Record<string, *>>} [pivotValues]
 * @returns {Array<number|string>|Array<Record<string, *>>|Record<string, Record<string, *>>}
 */
export function buildRelationSyncPayload(itemIds, pivotFields, pivotValues = {}) {
    const ids = Array.isArray(itemIds) ? itemIds : [];
    const fields = Array.isArray(pivotFields) ? pivotFields : [];
    if (fields.length === 0) {
        return ids;
    }

    const usesLeveledList =
        fields.includes('level') &&
        !fields.some((field) =>
            ['price', 'comment', 'character_level', 'slot_index', 'choice_order'].includes(field),
        );

    if (usesLeveledList) {
        return ids.map((itemId) => {
            const row = { id: itemId };
            fields.forEach((field) => {
                const value = pivotValues?.[itemId]?.[field] ?? '';
                if (field === 'quantity' || field === 'level') {
                    const n = Number(value);
                    row[field] = Number.isFinite(n) && n >= 1 ? Math.floor(n) : 1;
                } else {
                    row[field] = value;
                }
            });
            return row;
        });
    }

    const dataWithPivots = {};
    ids.forEach((itemId) => {
        dataWithPivots[itemId] = {};
        fields.forEach((field) => {
            const value = pivotValues?.[itemId]?.[field] ?? '';
            if ((field === 'quantity' || field === 'price') && value !== '') {
                dataWithPivots[itemId][field] = Number(value) || 0;
            } else if (field === 'character_level' || field === 'slot_index') {
                const n = Number(value);
                dataWithPivots[itemId][field] = Number.isFinite(n) && n >= 1 ? n : 1;
            } else if (field === 'choice_order') {
                const n = Number(value);
                dataWithPivots[itemId][field] = Number.isFinite(n) && n >= 0 ? Math.floor(n) : 0;
            } else {
                dataWithPivots[itemId][field] = value;
            }
        });
    });
    return dataWithPivots;
}

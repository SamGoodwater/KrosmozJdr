/**
 * Regroupe les lignes pivot `effect_sub_effect` pour l’affichage « journal ».
 *
 * @description
 * Une ligne avec `logic_operator === 'OR'` est une alternative au bloc précédent
 * (même principe que l’éditeur : opérateur avec le précédent).
 *
 * @param {Array<Record<string, unknown>>} rows - Lignes triées par `order`
 * @returns {Array<{ type: 'or' | 'sequence', rows: Array<Record<string, unknown>> }>}
 *
 * @example
 * segmentSpellEffectRows([a, bOr, cOr]) // [{ type: 'or', rows: [a,bOr,cOr] }]
 */
export function segmentSpellEffectRows(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
        return [];
    }

    const segments = [];
    let buffer = [];

    const flush = () => {
        if (buffer.length === 0) return;
        const isOrBranch =
            buffer.length > 1 &&
            buffer.slice(1).every((r) => String(r?.logic_operator ?? '').toUpperCase() === 'OR');
        segments.push({
            type: isOrBranch ? 'or' : 'sequence',
            rows: buffer,
        });
        buffer = [];
    };

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        if (i === 0) {
            buffer = [row];
            continue;
        }
        if (String(row?.logic_operator ?? '').toUpperCase() === 'OR') {
            buffer.push(row);
        } else {
            flush();
            buffer = [row];
        }
    }
    flush();

    return segments;
}

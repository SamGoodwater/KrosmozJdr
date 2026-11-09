import {
    formatTriggerTurnGroupLabel,
    normalizeTriggerTurn,
} from "@/Composables/entity/useSpellSubEffectPresentation";

/**
 * Tour de déclenchement d’une ligne pivot (colonne ou params).
 *
 * @param {Record<string, unknown>|null|undefined} row
 * @returns {number}
 */
export function rowTriggerTurn(row) {
    if (!row || typeof row !== "object") {
        return 0;
    }
    if (row.trigger_turn != null && row.trigger_turn !== "") {
        return normalizeTriggerTurn(row.trigger_turn);
    }
    const params = row.params && typeof row.params === "object" ? row.params : null;
    return normalizeTriggerTurn(params?.trigger_turn);
}

/**
 * Groupes de lignes par tour de déclenchement (ordre croissant), order conservé dans chaque groupe.
 *
 * @param {Array<Record<string, unknown>>} rows
 * @returns {Array<{ triggerTurn: number, label: string, rows: Array<Record<string, unknown>>, segments: Array<{ type: 'or' | 'sequence', rows: Array<Record<string, unknown>> }> }>}
 */
export function groupSpellEffectRowsByTriggerTurn(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
        return [];
    }
    /** @type {Map<number, Array<Record<string, unknown>>>} */
    const byTurn = new Map();
    for (const row of rows) {
        const turn = rowTriggerTurn(row);
        if (!byTurn.has(turn)) {
            byTurn.set(turn, []);
        }
        byTurn.get(turn).push(row);
    }
    return [...byTurn.entries()]
        .sort((a, b) => a[0] - b[0])
        .map(([triggerTurn, groupRows]) => ({
            triggerTurn,
            label: formatTriggerTurnGroupLabel(triggerTurn),
            rows: groupRows,
            segments: segmentSpellEffectRows(groupRows),
        }));
}

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

/**
 * Décodage / encodage du champ formula des characteristic_entities.
 *
 * Le champ peut contenir :
 * - Une formule simple (chaîne) : ex. "[vitality]*10+[level]*2"
 * - Un tableau par caractéristique (JSON) : ex. {"characteristic":"level","1":0,"7":2,"14":4}
 *   → pour chaque valeur X de la caractéristique, on prend la plus grande clé ≤ X.
 *
 * @see app/Services/Characteristic/FormulaConfigDecoder.php
 */

/**
 * Décode le champ formula et retourne une structure normalisée.
 *
 * @param {string|null} formula
 * @returns {{ type: 'formula', expression: string }|{ type: 'table', characteristic: string, entries: Array<{ from: number, value: number|string }> }}
 */
export function decodeFormulaConfig(formula) {
    if (formula == null || String(formula).trim() === '') {
        return { type: 'formula', expression: '' };
    }
    const trimmed = String(formula).trim();
    if (trimmed === '') {
        return { type: 'formula', expression: '' };
    }
    if (trimmed.startsWith('{')) {
        try {
            const decoded = JSON.parse(trimmed);
            if (decoded && typeof decoded.characteristic === 'string' && decoded.characteristic !== '') {
                const entries = [];
                for (const [key, value] of Object.entries(decoded)) {
                    if (key === 'characteristic') continue;
                    const from = parseInt(key, 10);
                    if (!Number.isNaN(from)) {
                        entries.push({
                            from,
                            value: typeof value === 'number' ? value : String(value ?? ''),
                        });
                    }
                }
                entries.sort((a, b) => a.from - b.from);
                return {
                    type: 'table',
                    characteristic: decoded.characteristic,
                    entries,
                };
            }
        } catch {
            // invalid JSON → treat as formula
        }
        return { type: 'formula', expression: formula };
    }
    return { type: 'formula', expression: formula };
}

/**
 * Encode une structure normalisée en chaîne pour stockage.
 *
 * @param {{ type: 'formula', expression: string }|{ type: 'table', characteristic: string, entries: Array<{ from: number, value: number|string }> }} decoded
 * @returns {string}
 */
export function encodeFormulaConfig(decoded) {
    if (!decoded) return '';
    if (decoded.type === 'formula') {
        return String(decoded.expression ?? '');
    }
    if (decoded.type === 'table' && decoded.entries?.length) {
        const obj = { characteristic: decoded.characteristic ?? '' };
        for (const entry of decoded.entries) {
            const from = Number(entry.from);
            if (!Number.isNaN(from)) {
                const v = entry.value;
                obj[String(from)] = typeof v === 'number' ? v : String(v ?? '');
            }
        }
        return JSON.stringify(obj);
    }
    return '';
}

/**
 * Indique si la chaîne formula est un JSON table.
 *
 * @param {string|null} formula
 * @returns {boolean}
 */
export function isFormulaTable(formula) {
    return decodeFormulaConfig(formula).type === 'table';
}

/**
 * Libellé de tranche : la ligne vaut de `from` inclus jusqu’au palier suivant exclus.
 *
 * @param {number} from
 * @param {number|null} nextFrom
 * @returns {string}
 *
 * @example
 * formulaTableRangeLabel(1, 3) // '1–2'
 * formulaTableRangeLabel(16, null) // '16+'
 */
export function formulaTableRangeLabel(from, nextFrom) {
    const start = Number(from);
    if (!Number.isFinite(start)) return '';
    if (nextFrom == null || !Number.isFinite(Number(nextFrom))) {
        return `${start}+`;
    }
    const next = Number(nextFrom);
    if (next <= start + 1) {
        return String(start);
    }
    return `${start}\u2013${next - 1}`;
}

/**
 * Vue lisible d’une formule tableau, ou null si la chaîne n’en est pas une.
 *
 * @param {string|null} formula
 * @returns {{ characteristic: string, rows: Array<{ range: string, value: number|string }> }|null}
 *
 * @example
 * buildFormulaTableView('{"characteristic":"level","1":"0","3":"1"}')
 * // { characteristic: 'level', rows: [{ range: '1–2', value: '0' }, { range: '3+', value: '1' }] }
 */
export function buildFormulaTableView(formula) {
    const decoded = decodeFormulaConfig(formula);
    if (decoded.type !== 'table' || !Array.isArray(decoded.entries) || decoded.entries.length === 0) {
        return null;
    }
    return {
        characteristic: decoded.characteristic,
        rows: decoded.entries.map((entry, index) => {
            const next = decoded.entries[index + 1];
            return {
                range: formulaTableRangeLabel(entry.from, next ? next.from : null),
                value: entry.value,
            };
        }),
    };
}

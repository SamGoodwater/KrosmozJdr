/**
 * Utilitaires pour l’autocomplétion des références de caractéristiques dans les champs formule.
 *
 * Les formules métier (PHP) n’acceptent que des variables {@code [ident]} ; l’affichage peut rester en clés nues.
 */

const IDENT_CHAR = /[a-zA-Z0-9_]/;

/**
 * Borne du mot / identifiant sous le curseur (suite de [a-zA-Z0-9_]).
 *
 * @param {string} value
 * @param {number} caretPos
 * @returns {{ start: number, end: number, token: string, caret: number }}
 */
export function getActiveIdentifierBounds(value, caretPos) {
    const v = String(value ?? "");
    const n = v.length;
    const caret = Math.min(Math.max(0, caretPos | 0), n);
    let start = caret;
    let end = caret;
    while (start > 0 && IDENT_CHAR.test(v[start - 1])) start--;
    while (end < n && IDENT_CHAR.test(v[end])) end++;
    return { start, end, token: v.slice(start, end), caret };
}

/**
 * @param {Array<{ id: string, name?: string|null, short_name?: string|null }>} suggestions
 * @param {string} query
 * @param {{ minLength?: number, maxResults?: number }} [opts]
 * @returns {typeof suggestions}
 */
export function filterCharacteristicSuggestions(suggestions, query, opts = {}) {
    const minLength = opts.minLength ?? 3;
    const maxResults = opts.maxResults ?? 60;
    const q = String(query ?? "").trim().toLowerCase();
    if (q.length < minLength) return [];

    const scored = [];
    for (const s of suggestions) {
        const id = String(s?.id ?? "").toLowerCase();
        if (!id) continue;
        const name = String(s?.name ?? "").toLowerCase();
        const shortN = String(s?.short_name ?? "").toLowerCase();
        if (!id.includes(q) && !name.includes(q) && !shortN.includes(q)) continue;

        let rank = 4;
        if (id === q) rank = 0;
        else if (id.startsWith(q)) rank = 1;
        else if (name.startsWith(q) || shortN.startsWith(q)) rank = 2;
        else rank = 3;

        scored.push({ s, rank, id });
    }
    scored.sort((a, b) => a.rank - b.rank || a.id.localeCompare(b.id));
    return scored.slice(0, maxResults).map((x) => x.s);
}

/**
 * Chaîne à insérer pour remplacer le segment [start, end), selon le contexte des crochets.
 *
 * @param {string} value texte complet
 * @param {number} start début identifiant
 * @param {number} end fin identifiant (exclu)
 * @param {string} key clé métier (ex. vitality_creature)
 * @param {boolean} useBrackets true pour formules PHP ([clé])
 */
export function buildInsertionForFormula(value, start, end, key, useBrackets) {
    if (!useBrackets) return key;
    const before = start > 0 ? value[start - 1] : "";
    const after = end < value.length ? value[end] : "";
    if (before === "[" && after === "]") return key;
    if (before === "[" && after !== "]") return `${key}]`;
    return `[${key}]`;
}

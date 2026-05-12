/**
 * Parser de formules de dés (notation ndX)
 *
 * @description
 * Parse et évalue des formules de lancer de dés (ex: 2d6+3, 3d10-1, 4d6/2).
 * Supporte les opérations +, -, *, / et les notations alternatives (x pour *, : pour /).
 * Priorité des opérateurs : * et / avant + et -.
 *
 * @example
 * parseDiceFormula('2d6+3')  // { min: 5, max: 15, average: 10, isValid: true }
 * rollDiceFormula('2d6+3')   // { result: 12, breakdown: ['2d6: 4+5', '+3'] }
 */

const DICE_REGEX = /^(\d*)d(\d+)/i;
const NUMBER_REGEX = /^\d+/;
const OP_REGEX = /^[-+*/x:]/;

/**
 * Normalise la formule : remplace x par *, : par /, et supprime les espaces.
 *
 * @param {string} formula - Formule brute
 * @returns {string} Formule normalisée
 */
function normalizeFormula(formula) {
    if (typeof formula !== 'string') return '';
    return formula
        .replace(/\s/g, '')
        .replace(/x/gi, '*')
        .replace(/:/g, '/');
}

/**
 * Tokenize une formule normalisée en tokens (dice, number, op).
 *
 * @param {string} str - Formule normalisée
 * @returns {{ tokens: Array<{ type: string, ... }>, error: string|null }}
 */
function tokenize(str) {
    const tokens = [];
    let i = 0;

    while (i < str.length) {
        const rest = str.slice(i);

        const diceMatch = rest.match(DICE_REGEX);
        if (diceMatch) {
            const n = diceMatch[1] ? parseInt(diceMatch[1], 10) : 1;
            const x = parseInt(diceMatch[2], 10);
            if (n < 1 || x < 1) {
                return { tokens: [], error: 'Format ndX invalide (n et X doivent être >= 1)' };
            }
            tokens.push({ type: 'dice', n, x });
            i += diceMatch[0].length;
            continue;
        }

        const numMatch = rest.match(NUMBER_REGEX);
        if (numMatch) {
            tokens.push({ type: 'number', value: parseInt(numMatch[0], 10) });
            i += numMatch[0].length;
            continue;
        }

        const opMatch = rest.match(OP_REGEX);
        if (opMatch) {
            const op = opMatch[0] === 'x' ? '*' : opMatch[0] === ':' ? '/' : opMatch[0];
            tokens.push({ type: 'op', value: op });
            i += opMatch[0].length;
            continue;
        }

        return { tokens: [], error: `Caractère inattendu à la position ${i}: "${rest[0]}"` };
    }

    return { tokens, error: null };
}

/**
 * Vérifie que la séquence de tokens est valide (alternance terme/opérateur).
 *
 * @param {Array} tokens - Liste de tokens
 * @returns {string|null} Message d'erreur ou null
 */
function validateTokenSequence(tokens) {
    if (tokens.length === 0) return 'Formule vide';
    const terms = ['dice', 'number'];
    const ops = ['op'];
    let expectTerm = true;

    for (const t of tokens) {
        if (expectTerm) {
            if (!terms.includes(t.type)) return 'Terme attendu (nombre ou ndX)';
            expectTerm = false;
        } else {
            if (t.type !== 'op') return 'Opérateur (+, -, *, /) attendu';
            expectTerm = true;
        }
    }
    if (expectTerm) return 'Terme attendu après le dernier opérateur';
    return null;
}

/**
 * Calcule min, max et moyenne pour un dé ou une constante.
 *
 * @param {{ type: string, n?: number, x?: number, value?: number }} token - Token dice ou number
 * @returns {{ min: number, max: number, average: number }}
 */
function getTokenStats(token) {
    if (token.type === 'dice') {
        const { n, x } = token;
        const min = n;
        const max = n * x;
        const average = n * ((1 + x) / 2);
        return { min, max, average };
    }
    const v = token.value;
    return { min: v, max: v, average: v };
}

/**
 * Applique un opérateur binaire à deux statistiques.
 *
 * @param {{ min: number, max: number, average: number }} a
 * @param {{ min: number, max: number, average: number }} b
 * @param {string} op - '+', '-', '*', '/'
 * @returns {{ min: number, max: number, average: number }}
 */
function applyOp(a, b, op) {
    const round = (n) => Math.round(n * 1000) / 1000;

    switch (op) {
        case '+':
            return {
                min: a.min + b.min,
                max: a.max + b.max,
                average: round(a.average + b.average),
            };
        case '-':
            return {
                min: a.min - b.max,
                max: a.max - b.min,
                average: round(a.average - b.average),
            };
        case '*':
            return {
                min: a.min * b.min,
                max: a.max * b.max,
                average: round(a.average * b.average),
            };
        case '/':
            if (b.min === 0 && b.max === 0) {
                return { min: 0, max: 0, average: 0 };
            }
            return {
                min: Math.floor(a.min / (b.max || 1)),
                max: Math.floor(a.max / (b.min || 1)),
                average: round(a.average / (b.average || 1)),
            };
        default:
            return a;
    }
}

/**
 * Évalue les tokens en appliquant les priorités (* et / avant + et -).
 *
 * @param {Array} tokens - Liste de tokens
 * @returns {{ min: number, max: number, average: number }|null}
 */
function evaluateStats(tokens) {
    const terms = [];
    const ops = [];

    for (let i = 0; i < tokens.length; i++) {
        const t = tokens[i];
        if (t.type === 'dice' || t.type === 'number') {
            terms.push(getTokenStats(t));
        } else {
            ops.push(t.value);
        }
    }

    if (terms.length !== ops.length + 1) return null;

    // Première passe : * et /
    const mulDiv = (arr, opArr) => {
        const res = [arr[0]];
        const resOps = [];
        let j = 0;

        for (let i = 0; i < opArr.length; i++) {
            if (opArr[i] === '*' || opArr[i] === '/') {
                const b = arr[j + 1];
                const a = res[res.length - 1];
                res[res.length - 1] = applyOp(a, b, opArr[i]);
                j++;
            } else {
                res.push(arr[j + 1]);
                resOps.push(opArr[i]);
                j++;
            }
        }
        return { terms: res, ops: resOps };
    };

    const { terms: terms2, ops: ops2 } = mulDiv(terms, ops);

    // Deuxième passe : + et -
    let stats = terms2[0];
    for (let i = 0; i < ops2.length; i++) {
        stats = applyOp(stats, terms2[i + 1], ops2[i]);
    }

    return stats;
}

/**
 * Parse une formule de dés et retourne min, max, moyenne.
 *
 * @param {string} formula - Formule (ex: 2d6+3, 3d10-1, 4d6/2)
 * @returns {{ min: number|null, max: number|null, average: number|null, isValid: boolean, error: string|null }}
 */
export function parseDiceFormula(formula) {
    const normalized = normalizeFormula(formula);
    if (!normalized) {
        return { min: null, max: null, average: null, isValid: false, error: null };
    }

    const { tokens, error: tokenError } = tokenize(normalized);
    if (tokenError) {
        return { min: null, max: null, average: null, isValid: false, error: tokenError };
    }

    const seqError = validateTokenSequence(tokens);
    if (seqError) {
        return { min: null, max: null, average: null, isValid: false, error: seqError };
    }

    const stats = evaluateStats(tokens);
    if (!stats) {
        return { min: null, max: null, average: null, isValid: false, error: 'Impossible d\'évaluer la formule' };
    }

    return {
        min: stats.min,
        max: stats.max,
        average: stats.average,
        isValid: true,
        error: null,
    };
}

/**
 * Lance un dé à X faces (résultat entre 1 et X inclus).
 *
 * @param {number} x - Nombre de faces
 * @returns {number}
 */
function rollOne(x) {
    return Math.floor(Math.random() * x) + 1;
}

/**
 * Évalue une expression en "rolling" les dés.
 *
 * @param {Array} tokens - Liste de tokens
 * @returns {{ result: number, breakdown: string[] }}
 */
function rollTokens(tokens) {
    const terms = [];
    const ops = [];

    for (let i = 0; i < tokens.length; i++) {
        const t = tokens[i];
        if (t.type === 'dice') {
            const rolls = [];
            for (let j = 0; j < t.n; j++) {
                rolls.push(rollOne(t.x));
            }
            const sum = rolls.reduce((a, b) => a + b, 0);
            terms.push({
                value: sum,
                dice: t,
                rolls,
            });
        } else if (t.type === 'number') {
            terms.push({ value: t.value, constant: true });
        } else {
            ops.push(t.value);
        }
    }

    const values = terms.map((x) => x.value);
    const breakdown = [];

    for (let i = 0; i < terms.length; i++) {
        if (terms[i].rolls) {
            breakdown.push(`${terms[i].dice.n}d${terms[i].dice.x}: ${terms[i].rolls.join('+')}=${terms[i].value}`);
        } else {
            breakdown.push(`${terms[i].value}`);
        }
    }

    // Même logique de priorité que evaluateStats
    const mulDiv = (arr, opArr) => {
        const res = [arr[0]];
        const resOps = [];
        let j = 0;

        for (let i = 0; i < opArr.length; i++) {
            if (opArr[i] === '*' || opArr[i] === '/') {
                const b = arr[j + 1];
                const a = res[res.length - 1];
                if (opArr[i] === '*') {
                    res[res.length - 1] = a * b;
                } else {
                    res[res.length - 1] = Math.floor(a / b) || 0;
                }
                j++;
            } else {
                res.push(arr[j + 1]);
                resOps.push(opArr[i]);
                j++;
            }
        }
        return { values: res, ops: resOps };
    };

    const { values: values2, ops: ops2 } = mulDiv(
        values,
        ops,
    );

    let result = values2[0];
    for (let i = 0; i < ops2.length; i++) {
        if (ops2[i] === '+') {
            result += values2[i + 1];
        } else {
            result -= values2[i + 1];
        }
    }

    return { result, breakdown };
}

/**
 * Simule un lancer de dés selon la formule.
 *
 * @param {string} formula - Formule (ex: 2d6+3)
 * @returns {{ result: number, breakdown: string[], isValid: boolean, error: string|null }}
 */
export function rollDiceFormula(formula) {
    const normalized = normalizeFormula(formula);
    if (!normalized) {
        return { result: 0, breakdown: [], isValid: false, error: 'Formule vide' };
    }

    const { tokens, error: tokenError } = tokenize(normalized);
    if (tokenError) {
        return { result: 0, breakdown: [], isValid: false, error: tokenError };
    }

    const seqError = validateTokenSequence(tokens);
    if (seqError) {
        return { result: 0, breakdown: [], isValid: false, error: seqError };
    }

    const { result, breakdown } = rollTokens(tokens);
    return { result, breakdown, isValid: true, error: null };
}

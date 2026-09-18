/**
 * Parser de formules de dés (miroir de App\Services\Jdr\DiceFormulaService).
 *
 * @description
 * Parse et évalue des formules tapées par l’utilisateur : dés (`d12`, `3d8`),
 * nombres (virgule ou point), tranches (`[2-6]`) et opérateurs `+ - * /`
 * (alias `x` `×` `:` `÷`). Aucun `eval`. Toute la chaîne doit être une formule.
 *
 * Une tranche entière `[min-max]` s’affiche en dés exacts : `[2-6]` → `1d5+1`.
 * Min / max / moyenne et le lancer utilisent la fourchette réelle.
 *
 * @example
 * parseDiceFormula('2d6+3')
 * // { min: 5, max: 15, average: 10, isValid: true, isRecognized: true, rangeEquivalents: [] }
 * parseDiceFormula('[2-6]')
 * // { min: 2, max: 6, average: 4, isRecognized: true, rangeEquivalents: ['[2-6] = 1d5+1'] }
 */

export const DICE_FORMULA_LIMITS = {
    MAX_FORMULA_LENGTH: 64,
    MAX_DICE_COUNT: 40,
    MAX_DICE_SIDES: 1000,
    MAX_TERMS: 12,
    MAX_ABS_NUMBER: 100000,
    MAX_RANGE_SPAN: 1000,
};

/**
 * Convertit une fourchette entière inclusive en notation exacte 1dN+k.
 *
 * @param {number} min
 * @param {number} max
 * @returns {string}
 */
export function fromInclusiveRange(min, max) {
    let a = Math.trunc(min);
    let b = Math.trunc(max);
    if (b < a) {
        const swap = a;
        a = b;
        b = swap;
    }
    if (a === b) {
        return String(a);
    }
    const faces = b - a + 1;
    const modifier = a - 1;
    const dice = `1d${faces}`;
    if (modifier === 0) {
        return dice;
    }
    return modifier > 0 ? `${dice}+${modifier}` : `${dice}${modifier}`;
}

/**
 * @param {unknown} formula
 * @returns {string}
 */
function normalizeFormula(formula) {
    if (typeof formula !== 'string') {
        return '';
    }
    return formula
        .replace(/\u00a0/g, '')
        .replace(/\s+/g, '')
        .replace(/[×xX]/g, '*')
        .replace(/[÷:]/g, '/')
        .replace(/[–—]/g, '-')
        .replace(/(\d),(\d)/g, '$1.$2');
}

function isFiniteNumber(value) {
    return typeof value === 'number' && Number.isFinite(value);
}

function isWholeNumber(value) {
    return isFiniteNumber(value) && Math.abs(value - Math.round(value)) < 1e-7;
}

function roundStat(value) {
    if (!isFiniteNumber(value)) {
        return 0;
    }
    const rounded = Math.round(value * 1000) / 1000;
    return isWholeNumber(rounded) ? Math.round(rounded) : rounded;
}

function formatNumber(value) {
    return String(roundStat(value));
}

function invalidParse(error) {
    return {
        min: null,
        max: null,
        average: null,
        isValid: false,
        isRecognized: false,
        rangeEquivalents: [],
        error,
    };
}

function makeRangeToken(rawMin, rawMax) {
    const minRaw = Number(rawMin);
    const maxRaw = Number(rawMax);
    if (!isFiniteNumber(minRaw) || !isFiniteNumber(maxRaw)) {
        return 'Tranche invalide';
    }
    let min = minRaw;
    let max = maxRaw;
    if (max < min) {
        const swap = min;
        min = max;
        max = swap;
    }
    if (Math.abs(min) > DICE_FORMULA_LIMITS.MAX_ABS_NUMBER || Math.abs(max) > DICE_FORMULA_LIMITS.MAX_ABS_NUMBER) {
        return 'Nombre trop grand';
    }
    if (max - min > DICE_FORMULA_LIMITS.MAX_RANGE_SPAN) {
        return 'Tranche trop large';
    }
    const equivalent = isWholeNumber(min) && isWholeNumber(max)
        ? fromInclusiveRange(min, max)
        : null;
    return { type: 'range', min, max, equivalent };
}

function makeDiceToken(rawN, rawX) {
    const n = rawN === '' ? 1 : Number.parseInt(rawN, 10);
    const x = Number.parseInt(rawX, 10);
    if (!Number.isInteger(n) || n < 1 || n > DICE_FORMULA_LIMITS.MAX_DICE_COUNT) {
        return 'Nombre de dés hors limites';
    }
    if (!Number.isInteger(x) || x < 1 || x > DICE_FORMULA_LIMITS.MAX_DICE_SIDES) {
        return 'Nombre de faces hors limites';
    }
    return { type: 'dice', n, x };
}

function makeNumberToken(raw) {
    if (/[eE]/.test(raw)) {
        return 'Caractères non autorisés';
    }
    const value = Number(raw);
    if (!isFiniteNumber(value) || Math.abs(value) > DICE_FORMULA_LIMITS.MAX_ABS_NUMBER) {
        return 'Nombre trop grand';
    }
    return { type: 'number', value };
}

/**
 * @param {string} str
 * @returns {{ tokens: Array, error: string|null }}
 */
function tokenize(str) {
    const tokens = [];
    let i = 0;
    let termCount = 0;

    while (i < str.length) {
        const rest = str.slice(i);

        const rangeMatch = rest.match(/^\[(-?\d+(?:\.\d+)?)-(-?\d+(?:\.\d+)?)\]/);
        if (rangeMatch) {
            const range = makeRangeToken(rangeMatch[1], rangeMatch[2]);
            if (typeof range === 'string') {
                return { tokens: [], error: range };
            }
            tokens.push(range);
            termCount += 1;
            i += rangeMatch[0].length;
            continue;
        }

        const diceMatch = rest.match(/^(\d{0,2})d(\d{1,4})/i);
        if (diceMatch) {
            const dice = makeDiceToken(diceMatch[1], diceMatch[2]);
            if (typeof dice === 'string') {
                return { tokens: [], error: dice };
            }
            tokens.push(dice);
            termCount += 1;
            i += diceMatch[0].length;
            continue;
        }

        const numMatch = rest.match(/^\d+(?:\.\d+)?/);
        if (numMatch) {
            const number = makeNumberToken(numMatch[0]);
            if (typeof number === 'string') {
                return { tokens: [], error: number };
            }
            tokens.push(number);
            termCount += 1;
            i += numMatch[0].length;
            continue;
        }

        const opMatch = rest.match(/^[+\-*/]/);
        if (opMatch) {
            tokens.push({ type: 'op', value: opMatch[0] });
            i += 1;
            continue;
        }

        return { tokens: [], error: 'Caractère inattendu' };
    }

    if (termCount > DICE_FORMULA_LIMITS.MAX_TERMS) {
        return { tokens: [], error: 'Trop de termes' };
    }

    return { tokens, error: null };
}

function validateTokenSequence(tokens) {
    if (tokens.length === 0) {
        return 'Formule vide';
    }
    let expectTerm = true;
    for (const token of tokens) {
        const isTerm = token.type === 'dice' || token.type === 'number' || token.type === 'range';
        if (expectTerm) {
            if (!isTerm) {
                return 'Terme attendu (nombre, dé ou tranche)';
            }
            expectTerm = false;
        } else {
            if (token.type !== 'op') {
                return 'Opérateur attendu';
            }
            expectTerm = true;
        }
    }
    return expectTerm ? 'Terme attendu après le dernier opérateur' : null;
}

function tokenStats(token) {
    if (token.type === 'dice') {
        const { n, x } = token;
        return { min: n, max: n * x, average: n * (1 + x) / 2 };
    }
    if (token.type === 'range') {
        return {
            min: token.min,
            max: token.max,
            average: (token.min + token.max) / 2,
        };
    }
    return { min: token.value, max: token.value, average: token.value };
}

function endpointStats(a, b, combine) {
    const values = [
        combine(a.min, b.min),
        combine(a.min, b.max),
        combine(a.max, b.min),
        combine(a.max, b.max),
    ];
    return {
        min: Math.min(...values),
        max: Math.max(...values),
        average: combine(a.average, b.average),
    };
}

function applyStatsOp(a, b, op) {
    switch (op) {
        case '+':
            return {
                min: a.min + b.min,
                max: a.max + b.max,
                average: a.average + b.average,
            };
        case '-':
            return {
                min: a.min - b.max,
                max: a.max - b.min,
                average: a.average - b.average,
            };
        case '*':
            return endpointStats(a, b, (x, y) => x * y);
        case '/':
            if (b.min <= 0 && b.max >= 0) {
                return { min: 0, max: 0, average: 0 };
            }
            return endpointStats(a, b, (x, y) => x / y);
        default:
            return a;
    }
}

function reduceTerms(terms, ops, apply) {
    const acc = [terms[0]];
    const accOps = [];
    let j = 0;

    for (let i = 0; i < ops.length; i += 1) {
        if (ops[i] === '*' || ops[i] === '/') {
            acc[acc.length - 1] = apply(acc[acc.length - 1], terms[j + 1], ops[i]);
            j += 1;
        } else {
            acc.push(terms[j + 1]);
            accOps.push(ops[i]);
            j += 1;
        }
    }

    let result = acc[0];
    for (let i = 0; i < accOps.length; i += 1) {
        result = apply(result, acc[i + 1], accOps[i]);
    }
    return result;
}

function evaluateStats(tokens) {
    const terms = [];
    const ops = [];
    for (const token of tokens) {
        if (token.type === 'op') {
            ops.push(token.value);
        } else {
            terms.push(tokenStats(token));
        }
    }
    if (terms.length === 0 || terms.length !== ops.length + 1) {
        return null;
    }
    return reduceTerms(terms, ops, applyStatsOp);
}

function randomInt(min, max) {
    const low = Math.trunc(min);
    const high = Math.trunc(max);
    const span = high - low + 1;
    if (span <= 0) {
        return low;
    }
    if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
        const buf = new Uint32Array(1);
        crypto.getRandomValues(buf);
        return low + (buf[0] % span);
    }
    return low + Math.floor(Math.random() * span);
}

function rollToken(token) {
    if (token.type === 'dice') {
        const rolls = [];
        let sum = 0;
        for (let i = 0; i < token.n; i += 1) {
            const roll = randomInt(1, token.x);
            rolls.push(roll);
            sum += roll;
        }
        return {
            value: sum,
            label: `${token.n}d${token.x}: ${rolls.join('+')}=${sum}`,
        };
    }
    if (token.type === 'range') {
        let value;
        if (isWholeNumber(token.min) && isWholeNumber(token.max)) {
            value = randomInt(token.min, token.max);
        } else {
            value = token.min + (token.max - token.min) * (randomInt(0, 10000) / 10000);
        }
        return {
            value,
            label: `[${formatNumber(token.min)}-${formatNumber(token.max)}]: ${formatNumber(value)}`,
        };
    }
    return { value: token.value, label: formatNumber(token.value) };
}

function rollTokens(tokens) {
    const values = [];
    const ops = [];
    const breakdown = [];

    for (const token of tokens) {
        if (token.type === 'op') {
            ops.push(token.value);
            continue;
        }
        const rolled = rollToken(token);
        values.push(rolled.value);
        breakdown.push(rolled.label);
    }

    if (values.length === 0 || values.length !== ops.length + 1) {
        return null;
    }

    const result = reduceTerms(values, ops, (a, b, op) => {
        switch (op) {
            case '+':
                return a + b;
            case '-':
                return a - b;
            case '*':
                return a * b;
            case '/':
                return b === 0 ? 0 : a / b;
            default:
                return a;
        }
    });

    return { result, breakdown };
}

function parseTokens(formula) {
    const normalized = normalizeFormula(formula);
    if (!normalized) {
        return { empty: true, error: 'Formule vide', tokens: [], recognized: false };
    }
    if (normalized.length > DICE_FORMULA_LIMITS.MAX_FORMULA_LENGTH) {
        return { empty: false, error: 'Formule trop longue', tokens: [], recognized: false };
    }
    if (!/^[0-9d+\-*/.[\]]+$/i.test(normalized)) {
        return { empty: false, error: 'Caractères non autorisés', tokens: [], recognized: false };
    }

    const { tokens, error } = tokenize(normalized);
    if (error) {
        return { empty: false, error, tokens: [], recognized: false };
    }

    const seqError = validateTokenSequence(tokens);
    if (seqError) {
        return { empty: false, error: seqError, tokens: [], recognized: false };
    }

    const recognized = tokens.some((token) => token.type === 'dice' || token.type === 'range');
    return { empty: false, error: null, tokens, recognized };
}

/**
 * Parse une formule de dés et retourne min, max, moyenne.
 *
 * @param {string} formula
 * @returns {{
 *   min: number|null,
 *   max: number|null,
 *   average: number|null,
 *   isValid: boolean,
 *   isRecognized: boolean,
 *   rangeEquivalents: string[],
 *   error: string|null
 * }}
 */
export function parseDiceFormula(formula) {
    const parsed = parseTokens(formula);
    if (parsed.error) {
        return invalidParse(parsed.empty ? null : parsed.error);
    }

    const stats = evaluateStats(parsed.tokens);
    if (!stats) {
        return invalidParse('Impossible d\'évaluer la formule');
    }

    const rangeEquivalents = parsed.tokens
        .filter((token) => token.type === 'range' && token.equivalent)
        .map((token) => `[${formatNumber(token.min)}-${formatNumber(token.max)}] = ${token.equivalent}`);

    return {
        min: roundStat(stats.min),
        max: roundStat(stats.max),
        average: roundStat(stats.average),
        isValid: true,
        isRecognized: parsed.recognized,
        rangeEquivalents,
        error: null,
    };
}

/**
 * Simule un lancer de dés selon la formule.
 *
 * @param {string} formula
 * @returns {{ result: number, breakdown: string[], isValid: boolean, error: string|null }}
 */
export function rollDiceFormula(formula) {
    const parsed = parseTokens(formula);
    if (parsed.error) {
        return {
            result: 0,
            breakdown: [],
            isValid: false,
            error: parsed.empty ? 'Formule vide' : parsed.error,
        };
    }

    const rolled = rollTokens(parsed.tokens);
    if (!rolled) {
        return {
            result: 0,
            breakdown: [],
            isValid: false,
            error: 'Impossible d\'évaluer la formule',
        };
    }

    return {
        result: roundStat(rolled.result),
        breakdown: rolled.breakdown,
        isValid: true,
        error: null,
    };
}

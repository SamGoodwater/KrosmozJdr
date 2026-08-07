/**
 * Miroir JS de la grammaire de formules Krosmoz (voir FormulaExpressionParser.php).
 *
 * @description
 * Permet d'afficher et de prévisualiser une saisie sans aller-retour serveur : nombre simple,
 * formule `{ expression }` avec suffixe d'arrondi (`+` supérieur, `-` inférieur, rien = normal),
 * références `[cle]` et domaines `[x-y]` / `[ndX]` (réservés au niveau).
 *
 * Le serveur reste la source de vérité : ce module sert l'UI (aperçu, sélecteur de niveau, aide).
 *
 * @example
 * evaluateFormulaValue('{[niveau] / 3}+', { level: 7 });   // 3
 * enumerateFormulaOutcomes('{8 + [1d4]}');                 // [9, 10, 11, 12]
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */

export const ROUNDING_NONE = 'none';
export const ROUNDING_ROUND = 'round';
export const ROUNDING_CEIL = 'ceil';
export const ROUNDING_FLOOR = 'floor';

export const KIND_EMPTY = 'empty';
export const KIND_NUMBER = 'number';
export const KIND_FORMULA = 'formula';

/** Alias français acceptés, résolus seulement si la clé brute est absente des variables. */
export const IDENTIFIER_ALIASES = {
    niveau: 'level',
    vitalite: 'vitality',
    force: 'strength',
    agilite: 'agility',
    sagesse_score: 'wisdom',
};

const MAX_COMBINATIONS = 400;

const PATTERN_IDENTIFIER = /\[([a-zA-Z_][a-zA-Z0-9_]*)\]/g;
const PATTERN_RANGE = /\[\s*(\d+)\s*(?:-|–|\.\.)\s*(\d+)\s*\]/g;
const PATTERN_DICE_BRACKET = /\[\s*(\d*)\s*[dD]\s*(\d+)\s*\]/g;
const PATTERN_DICE_BARE = /(?<![\w\]])(\d*)[dD](\d+)(?![\w])/g;

const ALLOWED_FUNCTIONS = {
    floor: Math.floor,
    ceil: Math.ceil,
    round: Math.round,
    sqrt: (v) => (v < 0 ? 0 : Math.sqrt(v)),
    abs: Math.abs,
    exp: Math.exp,
    log: (v) => (v <= 0 ? 0 : Math.log(v)),
    min: Math.min,
    max: Math.max,
    pow: Math.pow,
};

/**
 * @param {string} raw
 * @returns {{ kind: string, raw: string, number: number|null, expression: string, rounding: string, braced: boolean, domains: Array, identifiers: string[] }}
 */
export function parseFormulaValue(raw) {
    const value = String(raw ?? '').trim();
    const empty = {
        kind: KIND_EMPTY,
        raw: value,
        number: null,
        expression: '',
        rounding: ROUNDING_NONE,
        braced: false,
        domains: [],
        identifiers: [],
    };

    if (value === '') return empty;
    if (isNumericString(value)) return { ...empty, kind: KIND_NUMBER, number: Number(value) };

    let braced = false;
    let rounding = ROUNDING_NONE;
    let expression = value;

    if (value.startsWith('{')) {
        const closing = value.lastIndexOf('}');
        if (closing === -1) {
            return { ...empty, kind: KIND_FORMULA, expression: value.replace(/^\{+/, '') };
        }
        braced = true;
        expression = value.slice(1, closing);
        const suffix = value.slice(closing + 1).trim();
        rounding = suffix === '+' ? ROUNDING_CEIL : suffix === '-' ? ROUNDING_FLOOR : ROUNDING_ROUND;
    }

    return {
        kind: KIND_FORMULA,
        raw: value,
        number: null,
        expression: expression.trim(),
        rounding,
        braced,
        domains: extractDomains(expression),
        identifiers: extractIdentifiers(expression),
    };
}

/**
 * @param {string} raw
 * @param {Record<string, number>} variables
 * @returns {number|null}
 */
export function evaluateFormulaValue(raw, variables = {}) {
    const parsed = parseFormulaValue(raw);
    if (parsed.kind === KIND_EMPTY) return null;
    if (parsed.kind === KIND_NUMBER) return parsed.number;

    let expression = substituteDomains(parsed.expression, parsed.domains);
    expression = substituteIdentifiers(expression, variables);
    const result = evaluateExpression(expression);

    return result === null ? null : applyRounding(result, parsed.rounding);
}

/**
 * Toutes les valeurs possibles, triées et dédoublonnées.
 *
 * @param {string} raw
 * @param {Record<string, number>} variables
 * @param {number} maxOutcomes
 * @returns {number[]}
 */
export function enumerateFormulaOutcomes(raw, variables = {}, maxOutcomes = 20) {
    const parsed = parseFormulaValue(raw);
    if (parsed.kind === KIND_EMPTY) return [];
    if (parsed.kind === KIND_NUMBER) return [parsed.number];
    if (parsed.domains.length === 0) {
        const single = evaluateFormulaValue(raw, variables);
        return single === null ? [] : [single];
    }

    const outcomes = new Set();
    for (const combination of domainCombinations(parsed.domains)) {
        let expression = parsed.expression;
        for (const [token, chosen] of Object.entries(combination)) {
            expression = expression.split(token).join(String(chosen));
        }
        const result = evaluateExpression(substituteIdentifiers(expression, variables));
        if (result === null) continue;
        outcomes.add(applyRounding(result, parsed.rounding));
    }

    const values = [...outcomes].sort((a, b) => a - b);

    return sampleEvenly(values, Math.max(1, maxOutcomes));
}

/**
 * Remplace les références par leurs valeurs, en conservant la forme de la formule.
 *
 * @param {string} raw
 * @param {Record<string, number>} variables
 * @returns {string|null}
 */
export function substituteFormulaForDisplay(raw, variables = {}) {
    const parsed = parseFormulaValue(raw);
    if (parsed.kind !== KIND_FORMULA) return null;

    return substituteIdentifiers(parsed.expression, variables);
}

/**
 * @param {string} raw
 * @param {{ allowDomains?: boolean, allowedIdentifiers?: string[]|null }} options
 * @returns {string[]} Liste d'erreurs (vide si valide)
 */
export function validateFormulaValue(raw, { allowDomains = false, allowedIdentifiers = null } = {}) {
    const value = String(raw ?? '').trim();
    if (value === '' || isNumericString(value)) return [];

    const errors = [];

    if (value.startsWith('{')) {
        if (!value.includes('}')) return ['Accolade fermante « } » manquante.'];
        const suffix = value.slice(value.lastIndexOf('}') + 1).trim();
        if (!['', '+', '-'].includes(suffix)) {
            errors.push(`Suffixe d'arrondi « ${suffix} » inconnu (attendu : rien, + ou -).`);
        }
    } else {
        errors.push('Une formule doit être encadrée par des accolades, par exemple {[niveau] / 3}+.');
    }

    const parsed = parseFormulaValue(value);

    if (!allowDomains && parsed.domains.length > 0) {
        errors.push('Fourchettes et dés ne sont autorisés que sur le niveau.');
    }

    const withoutDomains = substituteDomains(parsed.expression, parsed.domains);
    for (const match of withoutDomains.matchAll(/\[([^\]]*)\]/g)) {
        if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(match[1])) {
            errors.push(`Référence « [${match[1]}] » invalide (attendu une clé de caractéristique).`);
        }
    }

    if (Array.isArray(allowedIdentifiers)) {
        for (const identifier of parsed.identifiers) {
            const canonical = IDENTIFIER_ALIASES[identifier] ?? identifier;
            if (!allowedIdentifiers.includes(identifier) && !allowedIdentifiers.includes(canonical)) {
                errors.push(`Caractéristique « [${identifier}] » inconnue.`);
            }
        }
    }

    if (errors.length === 0 && evaluateExpression(withoutDomains.replace(PATTERN_IDENTIFIER, '0')) === null) {
        errors.push('Expression mathématique invalide.');
    }

    return [...new Set(errors)];
}

/**
 * @param {number} value
 * @param {string} rounding
 * @returns {number}
 */
export function applyRounding(value, rounding) {
    if (rounding === ROUNDING_CEIL) return Math.ceil(value);
    if (rounding === ROUNDING_FLOOR) return Math.floor(value);
    if (rounding === ROUNDING_ROUND) return Math.round(value);

    return value;
}

function isNumericString(value) {
    return /^-?\d+(\.\d+)?$/.test(value);
}

function extractDomains(expression) {
    const domains = new Map();

    for (const match of expression.matchAll(PATTERN_RANGE)) {
        const min = Math.min(Number(match[1]), Number(match[2]));
        const max = Math.max(Number(match[1]), Number(match[2]));
        domains.set(match[0], { token: match[0], kind: 'range', label: `${min}-${max}`, values: rangeValues(min, max) });
    }

    const withoutBracketDice = expression.replace(PATTERN_DICE_BRACKET, '');
    for (const [pattern, source] of [
        [PATTERN_DICE_BRACKET, expression],
        [PATTERN_DICE_BARE, withoutBracketDice],
    ]) {
        for (const match of source.matchAll(pattern)) {
            if (domains.has(match[0])) continue;
            const count = match[1] === '' ? 1 : Math.max(1, Number(match[1]));
            const faces = Math.max(1, Number(match[2]));
            domains.set(match[0], {
                token: match[0],
                kind: 'dice',
                label: `${count}d${faces}`,
                values: rangeValues(count, count * faces),
            });
        }
    }

    return [...domains.values()];
}

function extractIdentifiers(expression) {
    const withoutDomains = expression.replace(PATTERN_RANGE, '0').replace(PATTERN_DICE_BRACKET, '0');
    const out = [];
    for (const match of withoutDomains.matchAll(PATTERN_IDENTIFIER)) {
        if (!out.includes(match[1])) out.push(match[1]);
    }

    return out;
}

function rangeValues(min, max) {
    const values = [];
    for (let i = min; i <= max; i++) values.push(i);

    return values;
}

function substituteDomains(expression, domains) {
    let out = expression;
    for (const domain of domains) {
        out = out.split(domain.token).join(String(domain.values[0]));
    }

    return out;
}

function substituteIdentifiers(expression, variables) {
    return expression.replace(PATTERN_IDENTIFIER, (_match, identifier) => {
        if (Object.prototype.hasOwnProperty.call(variables, identifier)) {
            return String(Number(variables[identifier]) || 0);
        }
        const alias = IDENTIFIER_ALIASES[identifier];
        if (alias && Object.prototype.hasOwnProperty.call(variables, alias)) {
            return String(Number(variables[alias]) || 0);
        }

        return '0';
    });
}

function domainCombinations(domains) {
    let combinations = [{}];
    for (const domain of domains) {
        const next = [];
        outer: for (const combination of combinations) {
            for (const value of domain.values) {
                if (next.length >= MAX_COMBINATIONS) break outer;
                next.push({ ...combination, [domain.token]: value });
            }
        }
        combinations = next;
    }

    return combinations;
}

function sampleEvenly(values, max) {
    if (values.length <= max) return values;

    const picked = new Map();
    for (let i = 0; i < max; i++) {
        const index = Math.round((i * (values.length - 1)) / (max - 1 || 1));
        picked.set(index, values[index]);
    }

    return [...picked.keys()].sort((a, b) => a - b).map((index) => picked.get(index));
}

/**
 * Évaluateur arithmétique restreint (nombres, + - * / **, parenthèses, fonctions autorisées, dés min).
 *
 * @param {string} expression
 * @returns {number|null}
 */
export function evaluateExpression(expression) {
    const source = String(expression ?? '').trim();
    if (source === '') return null;
    if (!/^[\d\s+\-*/().,a-zA-Z]+$/.test(source)) return null;

    let pos = 0;

    const skipSpaces = () => {
        while (pos < source.length && /\s/.test(source[pos])) pos++;
    };

    const parseNumber = () => {
        skipSpaces();
        const match = /^\d+(\.\d+)?([eE][+-]?\d+)?/.exec(source.slice(pos));
        if (!match) return null;
        pos += match[0].length;

        return Number(match[0]);
    };

    const parseFactor = () => {
        skipSpaces();
        if (pos >= source.length) return null;

        if (source[pos] === '-') {
            pos++;
            const inner = parseFactor();

            return inner === null ? null : -inner;
        }
        if (source[pos] === '(') {
            pos++;
            const inner = parseExpr();
            skipSpaces();
            if (source[pos] !== ')') return null;
            pos++;

            return inner;
        }

        const fnMatch = /^([a-zA-Z]+)\s*\(/.exec(source.slice(pos));
        if (fnMatch) {
            const name = fnMatch[1].toLowerCase();
            const fn = ALLOWED_FUNCTIONS[name];
            if (!fn) return null;
            pos += fnMatch[0].length;
            const args = [];
            for (;;) {
                const arg = parseExpr();
                if (arg === null) return null;
                args.push(arg);
                skipSpaces();
                if (source[pos] === ',') {
                    pos++;
                    continue;
                }
                break;
            }
            skipSpaces();
            if (source[pos] !== ')') return null;
            pos++;

            return fn(...args);
        }

        const number = parseNumber();
        if (number === null) return null;
        skipSpaces();
        if (source[pos] === 'd' || source[pos] === 'D') {
            const save = pos;
            pos++;
            const faces = parseNumber();
            if (faces === null) {
                pos = save;

                return number;
            }

            // Dés en mode déterministe minimal : NdX => N (le serveur tire réellement en jeu).
            return Math.max(1, number);
        }

        return number;
    };

    const parseTerm = () => {
        let left = parseFactor();
        if (left === null) return null;
        for (;;) {
            skipSpaces();
            if (source[pos] === '*' && source[pos + 1] === '*') {
                pos += 2;
                const right = parseTerm();
                if (right === null) return null;
                left = Math.pow(left, right);
            } else if (source[pos] === '*') {
                pos++;
                const right = parseFactor();
                if (right === null) return null;
                left *= right;
            } else if (source[pos] === '/') {
                pos++;
                const right = parseFactor();
                if (right === null) return null;
                left = right === 0 ? 0 : left / right;
            } else {
                break;
            }
        }

        return left;
    };

    const parseExpr = () => {
        let left = parseTerm();
        if (left === null) return null;
        for (;;) {
            skipSpaces();
            if (source[pos] === '+') {
                pos++;
                const right = parseTerm();
                if (right === null) return null;
                left += right;
            } else if (source[pos] === '-') {
                pos++;
                const right = parseTerm();
                if (right === null) return null;
                left -= right;
            } else {
                break;
            }
        }

        return left;
    };

    const result = parseExpr();
    skipSpaces();

    return result === null || pos < source.length || !Number.isFinite(result) ? null : result;
}

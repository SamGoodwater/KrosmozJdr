/**
 * Calculateur de part des dés de vie (miroir PHP LifeDiceCalculator).
 *
 * @description
 * Niveau 1 = max du dé / de la formule. Niveaux suivants = lancer (relance des 1)
 * ou méthode stable. Le mod. de Vitalité n’entre pas dans le total stocké.
 */

import {
    defaultFixedBySides,
    listDiceGroups,
    parseDiceFormula,
    rollDiceFormula,
} from '@/Utils/dice/diceParser.js';

export const LIFE_DICE_METHOD_RANDOM = 'random';
export const LIFE_DICE_METHOD_STABLE = 'stable';

/** @type {Record<number, string>} */
const CLASS_STABLE_FORMULA = {
    6: '6',
    8: '6+1d2',
    10: '6+1d4',
    12: '6+1d6',
};

/**
 * @param {string|null|undefined} lifeDice
 * @returns {number|null}
 */
export function parseClassDieSides(lifeDice) {
    if (lifeDice == null) return null;
    const trimmed = String(lifeDice).trim().toLowerCase();
    if (!trimmed) return null;
    const match = /^(?:(\d*)d)?(\d+)$/.exec(trimmed);
    if (!match) return null;
    const sides = Number(match[2]);
    return [4, 6, 8, 10, 12, 20].includes(sides) ? sides : null;
}

/**
 * @param {number} lifeDiceTotal
 * @param {number} level
 * @param {number} vitalityModifier
 * @returns {number}
 */
export function previewLifePoints(lifeDiceTotal, level, vitalityModifier) {
    const lvl = Math.max(1, Math.trunc(level) || 1);
    const mod = Math.max(0, Math.trunc(vitalityModifier) || 0);
    return Math.trunc(lifeDiceTotal) + mod * lvl;
}

/**
 * @param {number} sides
 * @param {object} [options]
 * @returns {{ value: number, breakdown: string[] }|null}
 */
function rollClassRandomGain(sides, options = {}) {
    const result = rollDiceFormula(`1d${sides}`, {
        rerollOnes: true,
        randomInt: options.randomInt,
    });
    if (!result.isValid) return null;
    return { value: Math.round(result.result), breakdown: result.breakdown };
}

/**
 * @param {number} sides
 * @param {object} [options]
 * @returns {{ value: number, breakdown: string[] }|null}
 */
function rollClassStableGain(sides, options = {}) {
    const formula = CLASS_STABLE_FORMULA[sides];
    if (!formula) return null;
    const result = rollDiceFormula(formula, {
        rerollOnes: false,
        randomInt: options.randomInt,
    });
    if (!result.isValid) return null;
    return { value: Math.round(result.result), breakdown: result.breakdown };
}

/**
 * @param {string} formula
 * @param {object} [options]
 * @returns {{ value: number, breakdown: string[] }|null}
 */
function rollFormulaRandomGain(formula, options = {}) {
    const result = rollDiceFormula(formula, {
        rerollOnes: true,
        randomInt: options.randomInt,
    });
    if (!result.isValid) return null;
    return { value: Math.round(result.result), breakdown: result.breakdown };
}

/**
 * @param {string} formula
 * @param {Record<number|string, number>} fixedBySides
 * @param {object} [options]
 * @returns {{ value: number, breakdown: string[] }|null}
 */
function rollFormulaStableGain(formula, fixedBySides, options = {}) {
    const result = rollDiceFormula(formula, {
        rerollOnes: false,
        fixedBySides,
        randomInt: options.randomInt,
    });
    if (!result.isValid) return null;
    return { value: Math.round(result.result), breakdown: result.breakdown };
}

/**
 * @param {number} sides
 * @param {number} level
 * @param {string} method
 * @param {object} [options]
 */
export function totalForClass(sides, level, method, options = {}) {
    const lvl = Math.max(1, Math.min(20, Math.trunc(level) || 1));
    if (!CLASS_STABLE_FORMULA[sides]) {
        return { ok: false, error: 'Dé de classe inconnu (attendu d6, d8, d10 ou d12).', lifeDiceTotal: 0, levels: [], method: '' };
    }
    if (method !== LIFE_DICE_METHOD_RANDOM && method !== LIFE_DICE_METHOD_STABLE) {
        return { ok: false, error: 'Méthode inconnue.', lifeDiceTotal: 0, levels: [], method: '' };
    }

    /** @type {{ level: number, gain: number, breakdown: string[] }[]} */
    const levels = [{
        level: 1,
        gain: sides,
        breakdown: [`niveau 1 : max du dé = ${sides}`],
    }];
    let total = sides;

    for (let n = 2; n <= lvl; n += 1) {
        const gain = method === LIFE_DICE_METHOD_STABLE
            ? rollClassStableGain(sides, options)
            : rollClassRandomGain(sides, options);
        if (!gain) {
            return { ok: false, error: 'Impossible de lancer le dé de classe.', lifeDiceTotal: 0, levels: [], method: '' };
        }
        total += gain.value;
        levels.push({ level: n, gain: gain.value, breakdown: gain.breakdown });
    }

    return { ok: true, error: null, lifeDiceTotal: total, levels, method };
}

/**
 * @param {string} formula
 * @param {number} level
 * @param {string} method
 * @param {object} [options]
 */
export function totalForFormula(formula, level, method, options = {}) {
    const lvl = Math.max(1, Math.min(20, Math.trunc(level) || 1));
    if (method !== LIFE_DICE_METHOD_RANDOM && method !== LIFE_DICE_METHOD_STABLE) {
        return { ok: false, error: 'Méthode inconnue.', lifeDiceTotal: 0, levels: [], method: '' };
    }

    const analysis = parseDiceFormula(formula);
    if (!analysis.isValid || analysis.max == null) {
        return { ok: false, error: analysis.error || 'Formule invalide.', lifeDiceTotal: 0, levels: [], method: '' };
    }

    const levelOne = Math.round(analysis.max);
    /** @type {{ level: number, gain: number, breakdown: string[] }[]} */
    const levels = [{
        level: 1,
        gain: levelOne,
        breakdown: [`niveau 1 : max de la formule = ${levelOne}`],
    }];
    let total = levelOne;
    const fixed = options.fixedBySides || defaultFixedBySides(formula);

    for (let n = 2; n <= lvl; n += 1) {
        const gain = method === LIFE_DICE_METHOD_STABLE
            ? rollFormulaStableGain(formula, fixed, options)
            : rollFormulaRandomGain(formula, options);
        if (!gain) {
            return { ok: false, error: 'Impossible d’évaluer la formule.', lifeDiceTotal: 0, levels: [], method: '' };
        }
        total += gain.value;
        levels.push({ level: n, gain: gain.value, breakdown: gain.breakdown });
    }

    return { ok: true, error: null, lifeDiceTotal: total, levels, method };
}

/**
 * @param {number} sides
 * @param {string} method
 * @param {object} [options]
 */
export function gainForClass(sides, method, options = {}) {
    if (!CLASS_STABLE_FORMULA[sides]) return null;
    return method === LIFE_DICE_METHOD_STABLE
        ? rollClassStableGain(sides, options)
        : rollClassRandomGain(sides, options);
}

/**
 * @param {string} formula
 * @param {string} method
 * @param {object} [options]
 */
export function gainForFormula(formula, method, options = {}) {
    const fixed = options.fixedBySides || defaultFixedBySides(formula);
    return method === LIFE_DICE_METHOD_STABLE
        ? rollFormulaStableGain(formula, fixed, options)
        : rollFormulaRandomGain(formula, options);
}

export { defaultFixedBySides, listDiceGroups };

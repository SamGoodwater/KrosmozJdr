/**
 * Filtres tableau de type plage ({ min, max }).
 *
 * @example
 * normalizeTableRangeValue({ min: 3, max: 12 }, { min: 0, max: 20 })
 * // { min: 3, max: 12 }
 */

import { enumerateFormulaOutcomes, evaluateFormulaValue } from "@/Utils/characteristic/formulaGrammar.js";

const DEFAULT_BOUNDS = Object.freeze({ min: 0, max: 20 });

/**
 * @param {unknown} value
 * @returns {number|null}
 */
function toFiniteNumber(value) {
    if (value === null || typeof value === "undefined" || value === "") {
        return null;
    }
    const n = Number(value);
    return Number.isFinite(n) ? n : null;
}

/**
 * Bornes d’un slider : payload serveur `{ min, max }`, sinon `filter.ui`, sinon 0–20.
 *
 * @param {unknown} option
 * @param {{ min?: number, max?: number }} [fallback]
 * @param {{ min?: number, max?: number }} [ui]
 * @returns {{ min: number, max: number }}
 */
export function rangeBoundsFromFilterOption(option, fallback = DEFAULT_BOUNDS, ui = {}) {
    const fbMin = toFiniteNumber(fallback?.min) ?? DEFAULT_BOUNDS.min;
    const fbMax = toFiniteNumber(fallback?.max) ?? DEFAULT_BOUNDS.max;

    const fromOption = option && typeof option === "object" && !Array.isArray(option)
        ? { min: toFiniteNumber(option.min), max: toFiniteNumber(option.max) }
        : { min: null, max: null };
    const fromUi = {
        min: toFiniteNumber(ui?.min),
        max: toFiniteNumber(ui?.max),
    };

    let min = fromOption.min ?? fromUi.min ?? fbMin;
    let max = fromOption.max ?? fromUi.max ?? fbMax;
    if (min > max) {
        const swap = min;
        min = max;
        max = swap;
    }
    if (min === max) {
        const spanMin = fromUi.min ?? fbMin;
        const spanMax = fromUi.max ?? fbMax;
        if (spanMin < spanMax) {
            min = Math.min(min, spanMin);
            max = Math.max(max, spanMax);
        }
    }

    return { min, max };
}

/**
 * Normalise une valeur de filtre plage. Sans saisie, renvoie les bornes du slider.
 *
 * @param {unknown} raw
 * @param {{ min?: number, max?: number }|null} [bounds]
 * @returns {{ min: number, max: number }}
 */
export function normalizeTableRangeValue(raw, bounds = null) {
    const loHi = rangeBoundsFromFilterOption(bounds, DEFAULT_BOUNDS);
    if (raw === null || typeof raw === "undefined" || raw === "") {
        return { min: loHi.min, max: loHi.max };
    }
    if (typeof raw !== "object" || Array.isArray(raw)) {
        return { min: loHi.min, max: loHi.max };
    }

    const min = toFiniteNumber(raw.min);
    const max = toFiniteNumber(raw.max);
    let a = min ?? loHi.min;
    let b = max ?? loHi.max;
    if (a > b) {
        const swap = a;
        a = b;
        b = swap;
    }

    return { min: a, max: b };
}

/**
 * Une plage est active si min/max sont posés et (avec bornes) ne couvrent pas tout le slider.
 *
 * @param {unknown} raw
 * @param {{ min?: number, max?: number }|null} [bounds]
 * @returns {boolean}
 */
export function isTableRangeActive(raw, bounds = null) {
    if (raw === null || typeof raw === "undefined" || raw === "") {
        return false;
    }
    if (typeof raw !== "object" || Array.isArray(raw)) {
        return false;
    }
    const hasMin = toFiniteNumber(raw.min) !== null;
    const hasMax = toFiniteNumber(raw.max) !== null;
    if (!hasMin && !hasMax) {
        return false;
    }
    if (!bounds) {
        return true;
    }
    const v = normalizeTableRangeValue(raw, bounds);
    const loHi = rangeBoundsFromFilterOption(bounds, DEFAULT_BOUNDS);

    return v.min !== loHi.min || v.max !== loHi.max;
}

/**
 * Entier minimal d’une formule (entité niveau 1), pour filtrer PA / PO / stats.
 *
 * @param {unknown} raw
 * @param {Record<string, number>} [variables]
 * @returns {number|null}
 *
 * @example
 * minFormulaInteger("2+level") // 3
 */
export function minFormulaInteger(raw, variables = { level: 1, niveau: 1 }) {
    if (raw === null || typeof raw === "undefined" || raw === "") {
        return null;
    }
    const outcomes = enumerateFormulaOutcomes(String(raw), variables);
    if (outcomes.length > 0) {
        return Math.trunc(Math.min(...outcomes));
    }
    const value = evaluateFormulaValue(String(raw), variables);
    if (value === null || !Number.isFinite(value)) {
        return null;
    }

    return Math.trunc(value);
}

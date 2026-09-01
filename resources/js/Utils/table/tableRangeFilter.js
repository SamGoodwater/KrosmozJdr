/**
 * Valeur d’un filtre tableau de type plage (min / max).
 *
 * @example
 * normalizeTableRangeValue({ min: 3, max: 12 }) // { min: 3, max: 12 }
 * isTableRangeActive({ min: 1, max: 20 }, { min: 1, max: 20 }) // false
 */

/**
 * @param {unknown} raw
 * @returns {{ min: number, max: number }|null}
 */
export function normalizeTableRangeValue(raw) {
    if (raw === null || typeof raw === "undefined" || raw === "") return null;
    if (typeof raw !== "object" || Array.isArray(raw)) return null;
    const min = Number(raw.min);
    const max = Number(raw.max);
    if (!Number.isFinite(min) && !Number.isFinite(max)) return null;
    const lo = Number.isFinite(min) ? min : max;
    const hi = Number.isFinite(max) ? max : min;
    return { min: Math.min(lo, hi), max: Math.max(lo, hi) };
}

/**
 * @param {unknown} raw
 * @param {{ min?: number, max?: number }|null|undefined} bounds
 * @returns {boolean}
 */
export function isTableRangeActive(raw, bounds) {
    const value = normalizeTableRangeValue(raw);
    if (!value) return false;
    const minBound = Number(bounds?.min);
    const maxBound = Number(bounds?.max);
    if (!Number.isFinite(minBound) || !Number.isFinite(maxBound)) return true;
    return value.min > minBound || value.max < maxBound;
}

/**
 * @param {unknown} option
 * @returns {{ min: number, max: number }|null}
 */
export function rangeBoundsFromFilterOption(option) {
    if (!option || typeof option !== "object" || Array.isArray(option)) return null;
    const min = Number(option.min);
    const max = Number(option.max);
    if (!Number.isFinite(min) || !Number.isFinite(max)) return null;
    return { min: Math.min(min, max), max: Math.max(min, max) };
}

/**
 * Position d’un curseur sur une barre 0–100.
 *
 * @param {number} value
 * @param {number} min
 * @param {number} max
 * @returns {number}
 *
 * @example
 * rangeValuePercent(5, 0, 10) // 50
 */
export function rangeValuePercent(value, min, max) {
    const lo = Number(min);
    const hi = Number(max);
    const n = Number(value);
    if (!Number.isFinite(lo) || !Number.isFinite(hi) || !Number.isFinite(n) || hi === lo) {
        return 0;
    }
    const pct = ((n - lo) / (hi - lo)) * 100;
    return Math.min(100, Math.max(0, pct));
}

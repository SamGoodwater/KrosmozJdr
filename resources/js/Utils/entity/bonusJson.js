/**
 * Valeur numérique d’une caractéristique dans `bonus` JSON (objet plat ou paliers panoplie).
 *
 * @example
 * bonusNumericForKey('{"strength":3}', 'strength') // 3
 * bonusNumericForKey({ 2: { strength: 1 }, 3: { strength: 2 } }, 'strength') // 3
 */

import {
    isPanoplyPieceBonusMap,
    shortBonusKey,
} from "@/Utils/entity/panoplyBonus.js";

/**
 * @param {unknown} raw
 * @returns {unknown}
 */
function decodeBonusPayload(raw) {
    if (raw && typeof raw === "object") {
        return raw;
    }
    if (typeof raw !== "string") {
        return null;
    }
    const trimmed = raw.trim();
    if (!trimmed) {
        return null;
    }
    try {
        return JSON.parse(trimmed);
    } catch {
        return null;
    }
}

/**
 * @param {unknown} map
 * @param {string} short
 * @returns {number|null}
 */
function numericFromFlatMap(map, short) {
    if (!map || typeof map !== "object" || Array.isArray(map)) {
        return null;
    }
    for (const [key, value] of Object.entries(map)) {
        if (value !== null && typeof value === "object") {
            continue;
        }
        if (shortBonusKey(key) !== short) {
            continue;
        }
        const n = Number(value);
        return Number.isFinite(n) ? n : null;
    }
    return null;
}

/**
 * @param {unknown} raw
 * @param {unknown} key
 * @returns {number|null}
 *
 * @example
 * bonusNumericForKey({ vitality: 10 }, "vitality") // 10
 */
export function bonusNumericForKey(raw, key) {
    const short = shortBonusKey(key);
    if (!short) {
        return null;
    }
    const payload = decodeBonusPayload(raw);
    if (!payload || typeof payload !== "object" || Array.isArray(payload)) {
        return null;
    }
    if (isPanoplyPieceBonusMap(payload)) {
        let sum = 0;
        let found = false;
        for (const stats of Object.values(payload)) {
            const n = numericFromFlatMap(stats, short);
            if (n !== null) {
                found = true;
                sum += n;
            }
        }
        return found ? sum : null;
    }
    return numericFromFlatMap(payload, short);
}

/**
 * Filtres bonus « à la demande » (DofusDB) : carte { strength: { min, max }, vitality: {} }.
 *
 * Une clé présente sans bornes = « a cette caractéristique ».
 */

import { shortBonusKey } from "@/Utils/entity/panoplyBonus.js";
import { getByCharacteristicKeyMap } from "@/Composables/store/useCharacteristicsStore";

/** @type {string[]} */
export const BONUS_FILTER_META_STEMS = ["name", "description", "level", "rarity", "price", "weight"];

/**
 * @param {unknown} key
 * @returns {boolean}
 */
export function isBonusMetaKey(key) {
    const stem = shortBonusKey(key).replace(/_(creature|object|spell)$/i, "");
    return BONUS_FILTER_META_STEMS.includes(stem.toLowerCase());
}

/**
 * @param {unknown} raw
 * @returns {boolean}
 */
export function isPickedRangeMap(raw) {
    if (!raw || typeof raw !== "object" || Array.isArray(raw)) {
        return false;
    }
    const keys = Object.keys(raw);
    if (keys.length === 0) {
        return false;
    }
    if (Object.prototype.hasOwnProperty.call(raw, "min") || Object.prototype.hasOwnProperty.call(raw, "max")) {
        return false;
    }
    return keys.every((key) => typeof key === "string" && key.trim() !== "");
}

/**
 * @param {unknown} inner
 * @returns {boolean}
 */
function innerIsOn(inner) {
    if (inner === false || inner === 0 || inner === "0") {
        return false;
    }
    if (inner && typeof inner === "object" && !Array.isArray(inner)) {
        if (Object.prototype.hasOwnProperty.call(inner, "on") || Object.prototype.hasOwnProperty.call(inner, "active")) {
            const flag = inner.on ?? inner.active;
            return ["1", "true", "yes", "on"].includes(String(flag).toLowerCase());
        }
    }
    return true;
}

/**
 * @param {unknown} inner
 * @returns {{ min: number|null, max: number|null }|null}
 */
export function pickedRangeBounds(inner) {
    if (!inner || typeof inner !== "object" || Array.isArray(inner)) {
        return null;
    }
    const min = inner.min === "" || inner.min == null ? null : Number(inner.min);
    const max = inner.max === "" || inner.max == null ? null : Number(inner.max);
    const minN = Number.isFinite(min) ? min : null;
    const maxN = Number.isFinite(max) ? max : null;
    if (minN === null && maxN === null) {
        return null;
    }
    if (minN !== null && maxN !== null && minN > maxN) {
        return { min: maxN, max: minN };
    }
    return { min: minN, max: maxN };
}

/**
 * @param {unknown} raw
 * @returns {Array<{ key: string, bounds: { min: number|null, max: number|null }|null }>}
 */
export function pickedRangeEntries(raw) {
    if (!isPickedRangeMap(raw)) {
        return [];
    }
    const out = [];
    for (const [key, inner] of Object.entries(raw)) {
        const short = shortBonusKey(key);
        if (!short || isBonusMetaKey(short) || !innerIsOn(inner)) {
            continue;
        }
        out.push({ key: short, bounds: pickedRangeBounds(inner) });
    }
    return out;
}

/**
 * @param {unknown} raw
 * @returns {boolean}
 */
export function isPickedRangeActive(raw) {
    return pickedRangeEntries(raw).length > 0;
}

/**
 * Options du sélecteur : payload API, sinon share Inertia `characteristics`.
 *
 * @param {unknown} filterOption
 * @param {{ entityType?: string }} [options]
 * @returns {Array<{ value: string, label: string, short_name?: string, min?: number, max?: number }>}
 */
export function bonusFilterOptions(filterOption, options = {}) {
    if (Array.isArray(filterOption) && filterOption.length > 0) {
        return filterOption
            .map((row) => {
                const value = shortBonusKey(row?.value ?? row?.key ?? "");
                if (!value || isBonusMetaKey(value)) {
                    return null;
                }
                return {
                    value,
                    label: String(row?.label || row?.name || value),
                    short_name: String(row?.short_name || ""),
                    min: Number.isFinite(Number(row?.min)) ? Number(row.min) : -200,
                    max: Number.isFinite(Number(row?.max)) ? Number(row.max) : 200,
                };
            })
            .filter(Boolean);
    }

    const groups = options.entityType === "panoply" || options.entityType === "panoplies"
        ? ["panoply", "item"]
        : ["item", "panoply"];
    const seen = new Set();
    const out = [];
    for (const group of groups) {
        const map = getByCharacteristicKeyMap(group);
        for (const [rawKey, def] of Object.entries(map || {})) {
            const value = shortBonusKey(rawKey);
            if (!value || isBonusMetaKey(value) || seen.has(value)) {
                continue;
            }
            seen.add(value);
            out.push({
                value,
                label: String(def?.name || def?.short_name || value),
                short_name: String(def?.short_name || ""),
                min: Number.isFinite(Number(def?.limit_min)) ? Number(def.limit_min) : -200,
                max: Number.isFinite(Number(def?.limit_max)) ? Number(def.limit_max) : 200,
            });
        }
    }
    out.sort((a, b) => a.label.localeCompare(b.label, "fr"));
    return out;
}

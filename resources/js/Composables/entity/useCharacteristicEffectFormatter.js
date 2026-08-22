/**
 * Outils de formatage des effets/bonus vers cellules "chips".
 *
 * @description
 * - Parse des payloads JSON (objet/array) provenant de `effect` / `bonus`
 * - Résolution des caractéristiques via useCharacteristicsStore (Inertia share)
 * - Génération d'une cellule `chips` (icon + color) avec fallback texte
 *
 * @example
 * const cell = buildCharacteristicEffectCell({
 *   rawValues: [entity.bonus, entity.effect],
 *   options,
 *   sourceGroups: ["item", "panoply"],
 *   format,
 *   size,
 *   chipsLayout: { maxRows: 3 },
 * });
 */

/**
 * @param {unknown} value
 * @returns {Object|Array|null}
 */
function parseJsonPayload(value) {
    if (value && typeof value === "object") return value;
    if (typeof value !== "string") return null;
    const trimmed = value.trim();
    if (!trimmed) return null;
    if (!(trimmed.startsWith("{") || trimmed.startsWith("["))) return null;
    try {
        return JSON.parse(trimmed);
    } catch {
        return null;
    }
}

/**
 * Bonus de panoplie : `{ "2": { strength: 1 }, "3": { vitality: 2 } }`.
 *
 * @param {unknown} payload
 * @returns {boolean}
 */
function isNumericPieceBonusMap(payload) {
    if (!payload || Array.isArray(payload) || typeof payload !== "object") {
        return false;
    }
    const keys = Object.keys(payload);
    if (keys.length === 0) {
        return false;
    }
    return keys.every((key) => {
        if (!/^\d+$/.test(key)) {
            return false;
        }
        const inner = payload[key];
        return Boolean(inner) && typeof inner === "object" && !Array.isArray(inner);
    });
}

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isEffectValueZero(value) {
    if (value === null || typeof value === "undefined" || value === "") {
        return true;
    }
    if (typeof value === "object") {
        return false;
    }
    const n = Number(value);
    return Number.isFinite(n) && n === 0;
}

/**
 * @param {Object|Array|null} payload
 * @returns {Array<{key: string, value: unknown, pieceCount?: number}>}
 */
function extractEffectEntries(payload) {
    if (!payload) return [];

    if (isNumericPieceBonusMap(payload)) {
        const out = [];
        for (const [piece, stats] of Object.entries(payload)) {
            const pieceCount = Number(piece);
            for (const entry of extractEffectEntries(stats)) {
                if (isEffectValueZero(entry.value)) {
                    continue;
                }
                out.push({
                    ...entry,
                    pieceCount: Number.isFinite(pieceCount) ? pieceCount : entry.pieceCount,
                });
            }
        }
        return out;
    }

    if (!Array.isArray(payload) && typeof payload === "object") {
        return Object.entries(payload)
            .filter(([, value]) => !isEffectValueZero(value) && (value === null || typeof value !== "object"))
            .map(([key, value]) => ({ key: String(key), value }));
    }

    if (Array.isArray(payload)) {
        return payload
            .map((row) => {
                if (!row || typeof row !== "object") return null;
                const charNum = Number(row.characteristic);
                const charOk = Number.isFinite(charNum) && charNum > 0;
                const key =
                    row.db_column ??
                    row.key ??
                    (charOk ? row.characteristic : null) ??
                    row.stat ??
                    row.name ??
                    row.label ??
                    null;
                let value = row.value ?? row.amount ?? row.val ?? null;
                if (value == null) {
                    const from = row.from;
                    const to = row.to;
                    if (from != null && to != null) {
                        if (Number(to) === 0) {
                            value = from;
                        } else if (Number(from) === Number(to)) {
                            value = from;
                        } else {
                            value = `${from}–${to}`;
                        }
                    } else {
                        value = to ?? from ?? row.max ?? row.min ?? null;
                    }
                }
                if (!key || value === null || typeof value === "undefined") return null;
                return { key: String(key), value };
            })
            .filter(Boolean);
    }

    return [];
}

import {
    getByDbColumnMap,
    getByCharacteristicKeyMap,
    getByDofusdbIdMap,
} from "@/Composables/store/useCharacteristicsStore";

/**
 * Collecte byDbColumn depuis le store (Inertia share).
 * @param {string[]} sourceGroups
 * @returns {Record<string, any>}
 */
function collectCharacteristicsByDb(sourceGroups = []) {
    const out = {};
    for (const group of sourceGroups) {
        const byDb = getByDbColumnMap(group);
        if (byDb && typeof byDb === "object") {
            Object.assign(out, byDb);
        }
    }
    return out;
}

/**
 * Collecte byCharacteristicKey depuis le store.
 * @param {string[]} sourceGroups
 * @returns {Record<string, any>}
 */
function collectCharacteristicsByCharacteristicKey(sourceGroups = []) {
    const out = {};
    for (const group of sourceGroups) {
        const byKey = getByCharacteristicKeyMap(group);
        if (byKey && typeof byKey === "object") {
            Object.assign(out, byKey);
        }
    }
    return out;
}

/**
 * Collecte byDofusdbId depuis le store.
 * @param {string[]} sourceGroups
 * @returns {Record<string, any>}
 */
function collectCharacteristicsByDofusdbId(sourceGroups = []) {
    const out = {};
    for (const group of sourceGroups) {
        const byId = getByDofusdbIdMap(group);
        if (byId && typeof byId === "object") {
            Object.assign(out, byId);
        }
    }
    return out;
}

/**
 * @param {Object} args
 * @param {Array<unknown>} args.rawValues
 * @param {Object} [args.options]
 * @param {string[]} [args.sourceGroups]
 * @param {Object} [args.format]
 * @param {string} [args.size]
 * @param {Object} [args.chipsLayout]
 * @returns {Object}
 */
export function buildCharacteristicEffectCell({
    rawValues = [],
    options = {},
    sourceGroups = [],
    format = {},
    size = "md",
    chipsLayout = {},
} = {}) {
    const context = options?.context || "table";
    const labelMode =
        context === "minimal" ? "icon-only" :
        context === "compact" ? "short" :
        "full";

    const rawTextParts = rawValues
        .map((v) => (v == null ? "" : String(v).trim()))
        .filter(Boolean);

    const parsedEntries = rawValues
        .flatMap((v) => extractEffectEntries(parseJsonPayload(v)));

    const byDb = collectCharacteristicsByDb(sourceGroups);
    const byCharacteristicKey = collectCharacteristicsByCharacteristicKey(sourceGroups);
    const byDofusdbId = collectCharacteristicsByDofusdbId(sourceGroups);

    if (parsedEntries.length > 0) {
        const seenCanonicalKeys = new Set();
        const items = [];
        for (const entry of parsedEntries) {
            const { key, value } = entry;
            const pieceCount = Number(entry.pieceCount);
            const hasPiece = Number.isFinite(pieceCount) && pieceCount > 0;
            const def =
                byDb?.[key] ||
                byDb?.[key.replace(/_object$/, "")] ||
                byCharacteristicKey?.[key] ||
                byCharacteristicKey?.[key.replace(/_object$/, "") + "_object"] ||
                byDofusdbId?.[key] ||
                (key && /^\d+$/.test(String(key)) ? byDofusdbId?.[String(Number(key))] : null);
            const canonicalKey = `${hasPiece ? `${pieceCount}:` : ""}${def?.db_column || def?.key || key}`;
            if (seenCanonicalKeys.has(canonicalKey)) continue;
            seenCanonicalKeys.add(canonicalKey);
            const renderedValue = String(value);
            const name = def?.name || key;
            const shortName = def?.short_name || name;
            const shortLabel = hasPiece ? `${pieceCount}p ${shortName}` : shortName;
            const unit = def?.unit ?? null;
            const valueWithUnit = unit ? `${renderedValue} ${unit}` : renderedValue;
            const pieceLabel = hasPiece ? `${pieceCount} pièce${pieceCount > 1 ? "s" : ""}` : "";
            items.push({
                icon: def?.icon || "fa-solid fa-circle-info",
                color: def?.color || null,
                value: renderedValue,
                unit,
                name: pieceLabel ? `${pieceLabel} — ${name}` : name,
                shortLabel,
                label: pieceLabel ? `${pieceLabel} — ${name}` : name,
                tooltip: pieceLabel ? `${pieceLabel} : ${name}: ${valueWithUnit}` : `${name}: ${valueWithUnit}`,
            });
        }

        const searchValue = items.map((it) => `${it.tooltip} ${it.value}`).join(" ").trim();
        const mergedText = [...rawTextParts, searchValue].filter(Boolean).join(" ").trim();

        return {
            type: "chips",
            value: "",
            params: {
                items,
                sortValue: mergedText,
                searchValue: mergedText,
                filterValue: mergedText,
                chipsLayout: { ...chipsLayout, labelMode },
            },
        };
    }

    const mergedText = rawTextParts.join(" ").trim();
    const maxLength = format.truncate || (size === "xs" || size === "sm" ? 20 : 40);
    const truncated = mergedText.length > maxLength ? `${mergedText.slice(0, maxLength - 1)}…` : mergedText;

    return {
        type: "text",
        value: truncated || "-",
        params: {
            tooltip: mergedText || "",
            sortValue: mergedText,
            searchValue: mergedText,
            filterValue: mergedText || null,
        },
    };
}


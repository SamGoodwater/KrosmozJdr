/**
 * Outils de formatage des effets/bonus vers cellules "chips".
 *
 * @description
 * - Parse des payloads JSON (objet/array) provenant de `effect` / `bonus`
 * - Résolution des caractéristiques via useCharacteristicsStore (Inertia share)
 * - Génération d'une cellule `chips` (icon + color) avec fallback texte
 *
 * @example
 * // Pour Item/Consumable/Resource : n'utiliser que effect (bonus = format brut DofusDB, doublon)
 * const cell = buildCharacteristicEffectCell({
 *   rawValues: [entity.effect],
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
 * @param {Object|Array|null} payload
 * @returns {Array<{key: string, value: unknown}>}
 */
function extractEffectEntries(payload) {
    if (!payload) return [];

    if (!Array.isArray(payload) && typeof payload === "object") {
        return Object.entries(payload).map(([key, value]) => ({ key: String(key), value }));
    }

    if (Array.isArray(payload)) {
        return payload
            .map((row) => {
                if (!row || typeof row !== "object") return null;
                const key = row.db_column ?? row.key ?? row.characteristic ?? row.stat ?? row.name ?? row.label ?? null;
                const value = row.value ?? row.amount ?? row.val ?? row.to ?? row.max ?? row.min ?? null;
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
        for (const { key, value } of parsedEntries) {
            const def =
                byDb?.[key] ||
                byDb?.[key.replace(/_object$/, "")] ||
                byCharacteristicKey?.[key] ||
                byCharacteristicKey?.[key.replace(/_object$/, "") + "_object"] ||
                byDofusdbId?.[key] ||
                (key && /^\d+$/.test(String(key)) ? byDofusdbId?.[String(Number(key))] : null);
            const canonicalKey = def?.db_column || def?.key || key;
            if (seenCanonicalKeys.has(canonicalKey)) continue;
            seenCanonicalKeys.add(canonicalKey);
            const renderedValue = String(value);
            const name = def?.name || key;
            const shortLabel = def?.short_name || name;
            const unit = def?.unit ?? null;
            const valueWithUnit = unit ? `${renderedValue} ${unit}` : renderedValue;
            items.push({
                icon: def?.icon || "fa-solid fa-circle-info",
                color: def?.color || null,
                value: renderedValue,
                unit,
                name,
                shortLabel,
                label: name,
                tooltip: `${name}: ${valueWithUnit}`,
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


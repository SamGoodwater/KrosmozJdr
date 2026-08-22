/**
 * Parse / sérialise le JSON `panoplies.bonus` (paliers de pièces → caractéristiques).
 *
 * @example
 * parsePanoplyBonus('{"2":{"strength":1}}');
 * // [{ pieceCount: 2, rows: [{ key: 'strength', value: '1' }] }]
 */

/**
 * @param {string} key
 * @returns {string}
 */
export function shortBonusKey(key) {
    const raw = String(key || "").trim();
    if (raw.endsWith("_object")) {
        return raw.slice(0, -7);
    }
    return raw;
}

/**
 * @param {unknown} payload
 * @returns {boolean}
 */
export function isPanoplyPieceBonusMap(payload) {
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
 * @param {unknown} raw
 * @returns {Array<{ pieceCount: number, rows: Array<{ key: string, value: string }> }>}
 *
 * @example
 * parsePanoplyBonus({ 2: { strength: 1 } });
 */
export function parsePanoplyBonus(raw) {
    const payload = decodeBonusPayload(raw);
    if (!payload || typeof payload !== "object" || Array.isArray(payload)) {
        return [];
    }

    if (isPanoplyPieceBonusMap(payload)) {
        return Object.entries(payload)
            .map(([piece, stats]) => ({
                pieceCount: Number(piece),
                rows: Object.entries(stats)
                    .filter(([, value]) => value !== null && value !== undefined && value !== "")
                    .map(([key, value]) => ({
                        key: String(key),
                        value: String(value),
                    })),
            }))
            .filter((tier) => Number.isFinite(tier.pieceCount) && tier.pieceCount >= 1)
            .sort((a, b) => a.pieceCount - b.pieceCount);
    }

    const rows = Object.entries(payload)
        .filter(([, value]) => value !== null && typeof value !== "object")
        .map(([key, value]) => ({ key: String(key), value: String(value) }));

    if (rows.length === 0) {
        return [];
    }

    return [{ pieceCount: 2, rows }];
}

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isBonusValueEmpty(value) {
    if (value === null || typeof value === "undefined" || value === "") {
        return true;
    }
    const n = Number(value);
    return Number.isFinite(n) && n === 0;
}

/**
 * Paliers de bonus avec au moins une valeur non nulle (les paliers vides sont omis).
 *
 * @param {unknown} raw
 * @returns {Array<{ pieceCount: number, rows: Array<{ key: string, value: string }> }>}
 *
 * @example
 * visiblePanoplyBonusTiers({ 2: { strength: 1 }, 3: { vitality: 0 } });
 * // [{ pieceCount: 2, rows: [{ key: 'strength', value: '1' }] }]
 */
export function visiblePanoplyBonusTiers(raw) {
    return parsePanoplyBonus(raw)
        .map((tier) => ({
            pieceCount: tier.pieceCount,
            rows: (tier.rows || []).filter((row) => !isBonusValueEmpty(row?.value)),
        }))
        .filter((tier) => tier.rows.length > 0);
}

/**
 * @param {{ rows?: Array<{ key?: string, value?: unknown }> }} tier
 * @returns {Record<string, string>}
 */
export function panoplyTierStatMap(tier) {
    const out = {};
    for (const row of Array.isArray(tier?.rows) ? tier.rows : []) {
        const key = String(row?.key || "").trim();
        if (!key || isBonusValueEmpty(row?.value)) {
            continue;
        }
        out[key] = String(row.value);
    }
    return out;
}

/**
 * @param {Array<{ pieceCount?: number, rows?: Array<{ key?: string, value?: unknown }> }>} tiers
 * @returns {string|null}
 *
 * @example
 * serializePanoplyBonus([{ pieceCount: 2, rows: [{ key: 'strength', value: 1 }] }]);
 */
export function serializePanoplyBonus(tiers) {
    const out = {};
    for (const tier of Array.isArray(tiers) ? tiers : []) {
        const piece = Number(tier?.pieceCount);
        if (!Number.isFinite(piece) || piece < 1) {
            continue;
        }
        const stats = {};
        for (const row of Array.isArray(tier.rows) ? tier.rows : []) {
            const key = String(row?.key || "").trim();
            if (!key) {
                continue;
            }
            const n = Number(row.value);
            if (!Number.isFinite(n) || n === 0) {
                continue;
            }
            stats[key] = n;
        }
        if (Object.keys(stats).length > 0) {
            out[String(Math.floor(piece))] = stats;
        }
    }

    if (Object.keys(out).length === 0) {
        return null;
    }

    return JSON.stringify(out);
}

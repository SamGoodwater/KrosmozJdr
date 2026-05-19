/**
 * Regroupe des relations N:N par niveau de pivot (0 = sans niveau).
 *
 * @param {object[]} items - Entités liées avec pivot.level
 * @param {string} [pivotKey='level'] - Clé du pivot
 * @returns {{ withLevel: Array<{ level: number, items: object[] }>, withoutLevel: object[] }}
 *
 * @example
 * groupRelationsByLevel([{ id: 1, pivot: { level: 2 } }])
 * // { withLevel: [{ level: 2, items: [...] }], withoutLevel: [] }
 */
export function groupRelationsByLevel(items, pivotKey = "level") {
    const list = Array.isArray(items) ? items : [];
    const map = new Map();

    for (const item of list) {
        const raw = item?.pivot?.[pivotKey] ?? item?.[pivotKey];
        const level = Number(raw);
        const key = Number.isFinite(level) && level >= 0 ? Math.floor(level) : 0;
        if (!map.has(key)) {
            map.set(key, []);
        }
        map.get(key).push(item);
    }

    const levels = [...map.keys()].sort((a, b) => a - b);
    const withoutLevel = map.get(0) ?? [];
    const withLevel = levels
        .filter((l) => l > 0)
        .map((level) => ({
            level,
            items: [...(map.get(level) ?? [])].sort((a, b) =>
                String(a?.name ?? "").localeCompare(String(b?.name ?? ""))
            ),
        }));

    return { withLevel, withoutLevel };
}

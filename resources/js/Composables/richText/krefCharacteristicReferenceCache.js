const cache = new Map();
const inflight = new Map();

function canonicalKey(rawKey) {
    return String(rawKey || "").replace(/_(creature|object|spell)$/i, "");
}

function buildReferenceTableUrl(key) {
    try {
        return route("api.characteristics.reference-table", {
            group: "all",
            entity: "*",
            search: key,
            sort_by: "group",
            sort_dir: "asc",
        });
    } catch {
        const q = new URLSearchParams({
            group: "all",
            entity: "*",
            search: key,
            sort_by: "group",
            sort_dir: "asc",
        });
        return `/api/characteristics/reference-table?${q.toString()}`;
    }
}

export async function loadKrefCharacteristicReferenceMeta(rawKey) {
    const key = canonicalKey(rawKey);
    if (!key) return null;
    if (cache.has(key)) return cache.get(key);
    if (inflight.has(key)) return inflight.get(key);

    const promise = fetch(buildReferenceTableUrl(key), {
        method: "GET",
        credentials: "same-origin",
        headers: { Accept: "application/json" },
    })
        .then(async (res) => {
            if (!res.ok) return null;
            const data = await res.json();
            const rows = Array.isArray(data?.rows) ? data.rows : [];
            const creature = rows.find((row) => canonicalKey(row?.key) === key && row?.group === "creature") || null;
            const object = rows.find((row) => canonicalKey(row?.key) === key && row?.group === "object") || null;
            return { creature, object };
        })
        .catch(() => null)
        .finally(() => {
            inflight.delete(key);
        });

    inflight.set(key, promise);
    const resolved = await promise;
    cache.set(key, resolved);
    return resolved;
}

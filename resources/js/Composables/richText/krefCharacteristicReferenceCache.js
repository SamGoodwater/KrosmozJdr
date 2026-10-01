import { canonicalCharacteristicKey } from "@/Utils/characteristic/characteristicScopeSummary";

const cache = new Map();
const inflight = new Map();

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
    const key = canonicalCharacteristicKey(rawKey);
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
            const rows = (Array.isArray(data?.rows) ? data.rows : []).filter(
                (row) => canonicalCharacteristicKey(row?.key) === key,
            );
            return { rows };
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

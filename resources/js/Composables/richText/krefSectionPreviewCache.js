const cache = new Map();
const inflight = new Map();

function toKey(info) {
    const pageSlug = String(info?.payload?.pageSlug || info?.pageSlug || "").trim();
    const sectionSlug = String(info?.payload?.sectionSlug || info?.sectionSlug || "").trim();
    const sectionId = info?.payload?.sectionId ?? info?.sectionId ?? "";
    if (!pageSlug || (!sectionSlug && (sectionId === null || sectionId === undefined || String(sectionId).trim() === ""))) {
        return "";
    }
    return `${pageSlug}:${sectionSlug}:${String(sectionId)}`;
}

export function getCachedKrefSectionPreview(info) {
    const key = toKey(info);
    return key && cache.has(key) ? cache.get(key) : null;
}

export function loadKrefSectionPreview(info, loader) {
    const key = toKey(info);
    if (!key || typeof loader !== "function") {
        return Promise.reject(new Error("kref section preview: paramètres invalides"));
    }
    if (cache.has(key)) {
        return Promise.resolve(cache.get(key));
    }
    if (inflight.has(key)) {
        return inflight.get(key);
    }
    const promise = Promise.resolve()
        .then(() => loader())
        .then((data) => {
            cache.set(key, data);
            inflight.delete(key);
            return data;
        })
        .catch((err) => {
            inflight.delete(key);
            throw err;
        });
    inflight.set(key, promise);
    return promise;
}

export function clearKrefSectionPreviewCache() {
    cache.clear();
    inflight.clear();
}

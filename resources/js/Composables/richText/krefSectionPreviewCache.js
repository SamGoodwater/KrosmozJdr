const cache = new Map();
const inflight = new Map();

function readPayloadValue(info, camelKey, snakeKey) {
    const payload = info?.payload && typeof info.payload === "object" ? info.payload : null;
    const fromPayload = payload?.[camelKey] ?? payload?.[snakeKey];
    if (fromPayload != null && String(fromPayload).trim() !== "") return fromPayload;
    const fromRoot = info?.[camelKey] ?? info?.[snakeKey];
    if (fromRoot != null && String(fromRoot).trim() !== "") return fromRoot;
    return null;
}

function toKey(info) {
    const sectionId = readPayloadValue(info, "sectionId", "section_id");
    if (sectionId != null && String(sectionId).trim() !== "") {
        return `id:${String(sectionId).trim()}`;
    }

    const pageSlug = readPayloadValue(info, "pageSlug", "page_slug");
    const sectionSlug = readPayloadValue(info, "sectionSlug", "section_slug");
    if (!pageSlug || !sectionSlug) {
        return "";
    }
    return `slug:${String(pageSlug).trim()}:${String(sectionSlug).trim()}`;
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

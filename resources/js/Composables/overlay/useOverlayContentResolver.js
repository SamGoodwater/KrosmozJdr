import { computed, ref } from "vue";
import { DEFAULT_OVERLAY_OPTIONS, OVERLAY_CONTENT_KIND } from "@/Composables/overlay/overlayConstants";

const cacheStore = new Map();
const inflight = new Map();

function nowMs() {
    return Date.now();
}

function cleanupCache(maxEntries) {
    if (cacheStore.size <= maxEntries) return;
    const entries = [...cacheStore.entries()].sort((a, b) => a[1].touchedAt - b[1].touchedAt);
    const extra = cacheStore.size - maxEntries;
    for (let i = 0; i < extra; i += 1) {
        cacheStore.delete(entries[i][0]);
    }
}

/**
 * @param {{
 * content: any,
 * cache?: { key?: string, ttlMs?: number, maxEntries?: number }|boolean
 * }} options
 */
export function useOverlayContentResolver(options) {
    const loading = ref(false);
    const error = ref(null);
    const resolved = ref(null);
    const resolvedKind = ref(OVERLAY_CONTENT_KIND.TEXT);
    let abortController = null;

    const cacheConfig = computed(() => {
        const raw = options?.cache;
        if (raw === false) return null;
        if (raw === true || raw == null) {
            return {
                key: "",
                ttlMs: DEFAULT_OVERLAY_OPTIONS.cacheTtlMs,
                maxEntries: DEFAULT_OVERLAY_OPTIONS.cacheMaxEntries,
            };
        }
        return {
            key: String(raw.key || ""),
            ttlMs: Number(raw.ttlMs ?? DEFAULT_OVERLAY_OPTIONS.cacheTtlMs),
            maxEntries: Number(raw.maxEntries ?? DEFAULT_OVERLAY_OPTIONS.cacheMaxEntries),
        };
    });

    function normalizeResult(result) {
        if (result == null) return { kind: OVERLAY_CONTENT_KIND.TEXT, value: "" };
        if (typeof result === "string") return { kind: OVERLAY_CONTENT_KIND.TEXT, value: result };
        if (typeof result === "object" && result.kind && Object.values(OVERLAY_CONTENT_KIND).includes(result.kind)) {
            return {
                kind: result.kind,
                value: result.value,
            };
        }
        return { kind: OVERLAY_CONTENT_KIND.COMPONENT, value: result };
    }

    function getCacheKey() {
        const cfg = cacheConfig.value;
        if (!cfg) return "";
        if (cfg.key) return cfg.key;
        const c = options?.content;
        if (typeof c === "object" && c && typeof c.key === "string") return c.key;
        return "";
    }

    function readFromCache() {
        const cfg = cacheConfig.value;
        if (!cfg) return null;
        const key = getCacheKey();
        if (!key) return null;
        const entry = cacheStore.get(key);
        if (!entry) return null;
        if (entry.expiresAt < nowMs()) {
            cacheStore.delete(key);
            return null;
        }
        entry.touchedAt = nowMs();
        return { kind: entry.kind, value: entry.value };
    }

    function writeToCache(payload) {
        const cfg = cacheConfig.value;
        if (!cfg) return;
        const key = getCacheKey();
        if (!key) return;
        cacheStore.set(key, {
            kind: payload.kind,
            value: payload.value,
            touchedAt: nowMs(),
            expiresAt: nowMs() + Math.max(1, cfg.ttlMs),
        });
        cleanupCache(Math.max(1, cfg.maxEntries));
    }

    async function resolve() {
        error.value = null;
        const cached = readFromCache();
        if (cached) {
            resolvedKind.value = cached.kind;
            resolved.value = cached.value;
            return cached;
        }

        const content = options?.content;
        if (content == null) {
            resolvedKind.value = OVERLAY_CONTENT_KIND.TEXT;
            resolved.value = "";
            return { kind: resolvedKind.value, value: resolved.value };
        }

        if (typeof content === "string") {
            resolvedKind.value = OVERLAY_CONTENT_KIND.TEXT;
            resolved.value = content;
            writeToCache({ kind: resolvedKind.value, value: resolved.value });
            return { kind: resolvedKind.value, value: resolved.value };
        }

        if (typeof content === "object" && content !== null) {
            if (content.component) {
                resolvedKind.value = OVERLAY_CONTENT_KIND.COMPONENT;
                resolved.value = {
                    component: content.component,
                    props: content.props || {},
                };
                writeToCache({ kind: resolvedKind.value, value: resolved.value });
                return { kind: resolvedKind.value, value: resolved.value };
            }
            if (typeof content.html === "string") {
                resolvedKind.value = OVERLAY_CONTENT_KIND.HTML;
                resolved.value = content.html;
                writeToCache({ kind: resolvedKind.value, value: resolved.value });
                return { kind: resolvedKind.value, value: resolved.value };
            }
        }

        const loader =
            (typeof content === "object" && content !== null && typeof content.loader === "function" && content.loader) ||
            (typeof content === "function" ? content : null);

        if (!loader) {
            resolvedKind.value = OVERLAY_CONTENT_KIND.TEXT;
            resolved.value = String(content);
            return { kind: resolvedKind.value, value: resolved.value };
        }

        const key = getCacheKey();
        if (abortController) abortController.abort();
        abortController = new AbortController();
        loading.value = true;
        try {
            let promise = null;
            if (key) {
                promise = inflight.get(key) || null;
            }
            if (!promise) {
                promise = Promise.resolve(loader({ signal: abortController.signal }));
                if (key) inflight.set(key, promise);
            }
            const result = await promise;
            const normalized = normalizeResult(result);
            resolvedKind.value = normalized.kind;
            resolved.value = normalized.value;
            writeToCache(normalized);
            return normalized;
        } catch (e) {
            if (e?.name === "AbortError") return null;
            error.value = e;
            return null;
        } finally {
            loading.value = false;
            if (key) inflight.delete(key);
        }
    }

    function clearCache() {
        const key = getCacheKey();
        if (!key) return;
        cacheStore.delete(key);
    }

    function dispose() {
        if (abortController) {
            abortController.abort();
            abortController = null;
        }
    }

    return {
        loading,
        error,
        resolved,
        resolvedKind,
        resolve,
        clearCache,
        dispose,
    };
}

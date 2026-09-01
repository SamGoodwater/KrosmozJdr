/**
 * useTableServerParams
 *
 * @description
 * Gestion centralisée des paramètres serveur pour les tableaux (Option A).
 * Source unique de vérité pour page, filters, sort, search en mode serverSide.
 *
 * @example
 * const { serverParams, mergeParams, buildFetchUrl } = useTableServerParams();
 * const effectiveUrl = computed(() => buildFetchUrl(serverParams.value, baseUrl, refreshToken));
 * // Sur emit du tableau : mergeParams(incomingParams)
 */

import { ref } from "vue";

const DEFAULT_PARAMS = {
    page: 1,
    pageSize: 25,
    filters: {},
    search: "",
    sort: "id",
    order: "desc",
    /** @type {{ field: string, dir: string }[]|null} */
    sorts: null,
};

/**
 * Construit l'URL de fetch avec les query params (fonction pure, testable).
 *
 * @param {Object} params - { page, pageSize, filters, search, sort, order }
 * @param {string} baseUrl - URL de base sans query
 * @param {number|string} [refreshToken=0] - Clé anti-cache
 * @returns {string} URL complète avec query string
 */
export function buildFetchUrl(params, baseUrl, refreshToken = 0) {
    const base = String(baseUrl || "").trim();
    if (!base) return "";
    const p = { ...DEFAULT_PARAMS, ...params };
    const searchParams = new URLSearchParams();
    searchParams.set("format", "entities");
    searchParams.set("limit", String(p.pageSize || 25));
    searchParams.set("page", String(p.page || 1));
    const sorts = Array.isArray(p.sorts) ? p.sorts : [];
    if (sorts.length > 0) {
        sorts.forEach((item, i) => {
            const field = item?.field ?? item?.id;
            if (!field) return;
            const dir = item?.dir === "desc" ? "desc" : "asc";
            searchParams.set(`sorts[${i}][field]`, String(field));
            searchParams.set(`sorts[${i}][dir]`, dir);
        });
    }
    searchParams.set("sort", String(p.sort || "id"));
    searchParams.set("order", String(p.order || "desc"));
    if (String(p.search || "").trim()) {
        searchParams.set("search", String(p.search).trim());
    }
    const filters = p.filters || {};
    for (const [key, value] of Object.entries(filters)) {
        if (value === null || typeof value === "undefined" || value === "") continue;
        if (Array.isArray(value)) {
            const vals = value.map((v) => String(v)).filter((v) => v !== "");
            if (vals.length === 0) continue;
            for (const v of vals) {
                searchParams.append(`filters[${key}][]`, v);
            }
            continue;
        }
        if (typeof value === "object") {
            const min = value.min;
            const max = value.max;
            const hasMin = min !== null && typeof min !== "undefined" && min !== "";
            const hasMax = max !== null && typeof max !== "undefined" && max !== "";
            if (!hasMin && !hasMax) continue;
            if (hasMin) searchParams.set(`filters[${key}][min]`, String(min));
            if (hasMax) searchParams.set(`filters[${key}][max]`, String(max));
            continue;
        }
        const normalized = typeof value === "boolean"
            ? (value ? "1" : "0")
            : String(value);
        if (normalized === "") continue;
        searchParams.set(`filters[${key}]`, normalized);
    }
    if (refreshToken !== null && refreshToken !== undefined && refreshToken !== 0 && refreshToken !== "0") {
        searchParams.set("_t", String(refreshToken));
    }
    return `${base}?${searchParams.toString()}`;
}

export function useTableServerParams(initial = {}) {
    const serverParams = ref({ ...DEFAULT_PARAMS, ...initial });

    const mergeParams = (partial) => {
        if (!partial || typeof partial !== "object") return;
        serverParams.value = { ...serverParams.value, ...partial };
    };

    return {
        serverParams,
        mergeParams,
        buildFetchUrl,
    };
}

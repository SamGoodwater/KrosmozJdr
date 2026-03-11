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
    searchParams.set("sort", String(p.sort || "id"));
    searchParams.set("order", String(p.order || "desc"));
    if (String(p.search || "").trim()) {
        searchParams.set("search", String(p.search).trim());
    }
    const filters = p.filters || {};
    for (const [key, value] of Object.entries(filters)) {
        if (value === null || typeof value === "undefined" || value === "") continue;
        const normalized = Array.isArray(value)
            ? value.map((v) => String(v)).filter(Boolean).join(",")
            : typeof value === "boolean"
                ? (value ? "1" : "0")
                : String(value);
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

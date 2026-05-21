/**
 * Filtres d’index entité (query Inertia → API tableau).
 *
 * @description
 * Les pages Index reçoivent `filters` depuis le contrôleur Laravel (query string).
 * EntityTanStackTable charge les données via l’API tableau sans repasser ces filtres :
 * ce composable normalise les filtres et construit l’URL de fetch.
 *
 * @example
 * const serverUrl = useEntityIndexTableApiUrl('api.tables.monsters', () => props.filters, refreshToken);
 */

import { computed, unref } from "vue";

/**
 * @param {Record<string, unknown>|import('vue').Ref<Record<string, unknown>>} filters
 * @returns {Record<string, string|number|boolean>}
 */
export function normalizeIndexTableFilters(filters) {
    const raw = unref(filters) || {};
    /** @type {Record<string, string|number|boolean>} */
    const out = {};

    for (const [key, value] of Object.entries(raw)) {
        if (value === null || value === undefined || value === "") {
            continue;
        }
        out[key] = value;
    }

    return out;
}

/**
 * @param {string} ziggyRouteName
 * @param {Record<string, unknown>|import('vue').Ref<Record<string, unknown>>} filters
 * @param {import('vue').Ref<number|string>|number|string} refreshToken
 * @param {{ limit?: number }} [options]
 */
export function useEntityIndexTableApiUrl(ziggyRouteName, filters, refreshToken, options = {}) {
    const limit = options.limit ?? 5000;

    return computed(() => {
        const params = new URLSearchParams();
        params.set("format", "entities");
        params.set("limit", String(limit));
        params.set("_t", String(unref(refreshToken) ?? 0));

        for (const [key, value] of Object.entries(normalizeIndexTableFilters(filters))) {
            params.set(key, String(value));
        }

        return `${route(ziggyRouteName)}?${params.toString()}`;
    });
}

/**
 * useEntitySearch — Composable générique pour rechercher des entités via les endpoints api.tables.*
 *
 * @description
 * Encapsule l'appel aux endpoints `api.tables.{entityType}` avec le contrat commun :
 * - search, filters, limit, sort, order, format=entities
 * - whitelist / ids[], blacklist / exclude[]
 *
 * Ce composable est destiné à être utilisé par les pickers d'entités (EntityPickerCore, etc.).
 */
import { ref, computed, watch } from 'vue';

/**
 * @param {Object} options
 * @param {string} options.entityType - Clé d'entité (resources, monsters, spells, etc.) correspondant à route('api.tables.{entityType}')
 * @param {Object} [options.initialFilters={}] - Filtres initiaux (filters[...])
 * @param {string} [options.initialSort='id'] - Colonne de tri initiale
 * @param {string} [options.initialOrder='asc'] - Ordre de tri initial
 * @param {number} [options.limit=20] - Limite de résultats par requête
 * @param {number} [options.debounce=250] - Délai (ms) avant de lancer la recherche après saisie
 * @param {Array<number|string>} [options.whitelist] - Liste d'ids à inclure
 * @param {Array<number|string>} [options.blacklist] - Liste d'ids à exclure
 */
export function useEntitySearch(options = {}) {
    const {
        entityType,
        initialFilters = {},
        initialSort = 'id',
        initialOrder = 'asc',
        limit = 20,
        debounce = 250,
        whitelist = undefined,
        blacklist = undefined,
    } = options;

    if (!entityType) {
        // On préfère un warning explicite plutôt qu'un plantage silencieux.
        console.warn('[useEntitySearch] "entityType" est requis.');
    }

    const query = ref('');
    const loading = ref(false);
    const error = ref(null);
    const results = ref([]);
    const filterOptions = ref({});

    const currentFilters = ref({ ...(initialFilters || {}) });
    const currentSort = ref(initialSort || 'id');
    const currentOrder = ref(initialOrder === 'desc' ? 'desc' : 'asc');

    const currentWhitelist = ref(whitelist ? [...whitelist] : []);
    const currentBlacklist = ref(blacklist ? [...blacklist] : []);

    let debounceTimer = null;

    const hasFilters = computed(() => {
        const f = currentFilters.value || {};
        return Object.keys(f).some((k) => f[k] !== '' && f[k] !== null && f[k] !== undefined);
    });

    const buildParams = () => {
        const params = {
            format: 'entities',
            limit,
            search: query.value || '',
            sort: currentSort.value,
            order: currentOrder.value,
        };

        const filters = currentFilters.value || {};
        if (Object.keys(filters).length > 0) {
            Object.entries(filters).forEach(([key, value]) => {
                if (value !== '' && value !== null && value !== undefined) {
                    params[`filters[${key}]`] = value;
                }
            });
        }

        if (currentWhitelist.value && currentWhitelist.value.length > 0) {
            params.whitelist = currentWhitelist.value.join(',');
        }

        if (currentBlacklist.value && currentBlacklist.value.length > 0) {
            params.blacklist = currentBlacklist.value.join(',');
        }

        return params;
    };

    const search = async (override = {}) => {
        if (!entityType) return;
        loading.value = true;
        error.value = null;

        const baseParams = buildParams();
        const params = { ...baseParams, ...(override || {}) };

        try {
            const url = route(`api.tables.${entityType}`, params);
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            const meta = data?.meta || {};

            results.value = Array.isArray(data?.entities) ? data.entities : [];
            filterOptions.value = meta.filterOptions || {};
        } catch (e) {
            console.error('[useEntitySearch] Erreur lors de la recherche :', e);
            error.value = e?.message || 'Erreur lors de la recherche.';
        } finally {
            loading.value = false;
        }
    };

    const debouncedSearch = () => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(() => {
            search();
        }, debounce);
    };

    const setFilters = (partial) => {
        currentFilters.value = {
            ...(currentFilters.value || {}),
            ...(partial || {}),
        };
        search();
    };

    const setSort = (sort, order = 'asc') => {
        if (sort) {
            currentSort.value = sort;
        }
        if (order === 'asc' || order === 'desc') {
            currentOrder.value = order;
        }
        search();
    };

    const setWhitelist = (ids) => {
        currentWhitelist.value = Array.isArray(ids) ? [...ids] : [];
        search();
    };

    const setBlacklist = (ids) => {
        currentBlacklist.value = Array.isArray(ids) ? [...ids] : [];
        search();
    };

    watch(
        () => query.value,
        () => {
            debouncedSearch();
        }
    );

    return {
        query,
        results,
        loading,
        error,
        filterOptions,
        currentFilters,
        currentSort,
        currentOrder,
        hasFilters,
        search,
        setFilters,
        setSort,
        setWhitelist,
        setBlacklist,
    };
}


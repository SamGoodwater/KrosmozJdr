/**
 * useTableSearch
 *
 * @description
 * Logique de recherche pour TanStackTable (client et serveur).
 * - Mode client : searchText local, utilisé pour le filtrage.
 * - Mode serveur : searchDisplayValue pour l'affichage, emit debouncé vers le parent.
 *
 * @example
 * const { effectiveSearchDisplayValue, handleSearchInput, applySearchValue, clearSearch } = useTableSearch({
 *   serverSide: toRef(props, 'serverSide'),
 *   activeFilters,
 *   debounceMs: 150,
 *   onServerParamsChange: (params) => emit('update:serverParams', params),
 * });
 */

import { computed, ref, toValue } from "vue";

export function useTableSearch(options = {}) {
    const {
        serverSide = computed(() => false),
        activeFilters = ref({}),
        debounceMs = 150,
        onServerParamsChange = () => {},
    } = options;

    const getDebounceMs = () => Math.max(0, Number(toValue(debounceMs)) || 150);

    let _searchTimeout = null;

    const searchText = ref("");
    const searchDisplayValue = ref("");

    const effectiveSearchDisplayValue = computed(() =>
        serverSide.value ? searchDisplayValue.value : searchText.value,
    );

    const handleSearchInput = (value) => {
        const v = String(value ?? "");
        if (serverSide.value) {
            searchDisplayValue.value = v;
        } else {
            searchText.value = v;
        }
        if (_searchTimeout) clearTimeout(_searchTimeout);
        _searchTimeout = setTimeout(() => {
            if (serverSide.value) {
                onServerParamsChange({
                    search: v.trim(),
                    filters: { ...(activeFilters.value || {}) },
                    page: 1,
                });
            } else {
                searchText.value = v;
            }
        }, getDebounceMs());
    };

    const applySearchValue = (value) => {
        const next = String(value ?? "");
        if (serverSide.value) {
            searchDisplayValue.value = next;
            onServerParamsChange({
                search: next.trim(),
                filters: { ...(activeFilters.value || {}) },
                page: 1,
            });
        } else {
            searchText.value = next;
            handleSearchInput(next);
        }
    };

    const clearSearch = () => {
        applySearchValue("");
    };

    /** Valeur courante pour comparaison preset / emptyState. */
    const getCurrentSearch = () =>
        serverSide.value
            ? String(searchDisplayValue.value || "").trim()
            : String(searchText.value || "").trim();

    return {
        searchText,
        searchDisplayValue,
        effectiveSearchDisplayValue,
        handleSearchInput,
        applySearchValue,
        clearSearch,
        getCurrentSearch,
    };
}

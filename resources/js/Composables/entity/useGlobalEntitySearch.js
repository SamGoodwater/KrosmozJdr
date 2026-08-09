/**
 * useGlobalEntitySearch — endpoint unique `/api/global-search` (entités + pages + sections), Gate `view`.
 *
 * @description
 * Requête GET avec session web ; filtres optionnels `types[]` et `states[]` ; pagination via `limit` croissant.
 *
 * @example
 * const { query, groupedResults, loadMore, hasMore } = useGlobalEntitySearch();
 */

import { ref, computed, watch } from "vue";
import { globalSearchEntityLabelKey } from "@/Utils/entity/globalSearchEntityLabel";
import { isEntityFavorite, useFavoriteEntityVersion } from "@/Composables/entity/useFavoriteEntityIds";
import { rankResultsWithFavoritesFirst } from "@/Utils/entity/rankResultsWithFavoritesFirst";

/** Ordre d'affichage des groupes (aligné sur GlobalSearchService::SEARCH_TYPE_ORDER). */
export const GLOBAL_SEARCH_TYPE_ORDER = Object.freeze([
    "breeds",
    "specializations",
    "monsters",
    "spells",
    "capabilities",
    "creature-traits",
    "conditions",
    "items",
    "consumables",
    "resources",
    "panoplies",
    "campaigns",
    "scenarios",
    "npcs",
    "shops",
    "pages",
    "sections",
]);

/** Types recherchés en arrière-plan mais sans bouton filtre dans le header. */
const GLOBAL_SEARCH_HIDDEN_FILTER_TYPES = new Set([
    "item-types",
    "consumable-types",
    "spell-types",
    "monster-races",
    "resource-types",
]);

const GLOBAL_SEARCH_TYPE_LABELS = Object.freeze({
    breeds: "Classes",
    specializations: "Spécialisations",
    monsters: "Monstres",
    spells: "Sorts",
    capabilities: "Capacités",
    "creature-traits": "Traits de créature",
    conditions: "États",
    items: "Équipements",
    consumables: "Consommables",
    resources: "Ressources",
    panoplies: "Panoplies",
    campaigns: "Campagnes",
    scenarios: "Scénarios",
    npcs: "PNJ",
    shops: "Boutiques",
    pages: "Pages",
    sections: "Sections",
});

/** Filtres « catégories » affichés dans le header (sous-ensemble ordonné). */
export const GLOBAL_SEARCH_TYPE_FILTERS = Object.freeze(
    GLOBAL_SEARCH_TYPE_ORDER.filter((value) => !GLOBAL_SEARCH_HIDDEN_FILTER_TYPES.has(value)).map(
        (value) => ({
            value,
            label: GLOBAL_SEARCH_TYPE_LABELS[value] ?? value,
        })
    )
);

/** Filtres d’état de publication (valeurs = GlobalSearchService::ALLOWED_STATES). */
export const GLOBAL_SEARCH_STATE_FILTERS = Object.freeze([
    { value: "playable", label: "Jouable" },
    { value: "draft", label: "Brouillon" },
    { value: "raw", label: "Brut" },
    { value: "archived", label: "Archivé" },
]);

const typeOrderIndex = Object.fromEntries(
    GLOBAL_SEARCH_TYPE_ORDER.map((type, index) => [type, index])
);

/**
 * @param {Object} [options]
 * @param {number} [options.minQueryLength=2]
 * @param {number} [options.debounce=250]
 * @param {number} [options.pageSize=40]
 * @param {import('vue').Ref<string[]>} [options.selectedTypes]
 * @param {import('vue').Ref<string[]>} [options.selectedStates]
 */
export function useGlobalEntitySearch(options = {}) {
    const {
        minQueryLength = 2,
        debounce = 250,
        pageSize = 40,
        selectedTypes = ref([]),
        selectedStates = ref([]),
    } = options;

    const favoriteVersion = useFavoriteEntityVersion();

    const query = ref("");
    const loading = ref(false);
    const loadingMore = ref(false);
    const error = ref(null);
    const isOpen = ref(false);
    const fetchLimit = ref(pageSize);
    const hasMore = ref(false);

    /** @type {import('vue').Ref<Array<{ id:number|string, entityType:string, group:string, title:string, subtitle?:string, href:string, icon?:string, iconUrl?:string }>>} */
    const flatResults = ref([]);

    let debounceTimer = null;
    let abortController = null;
    let lastSearchKey = "";

    const hasResults = computed(() => flatResults.value.length > 0);

    const groupedResults = computed(() => {
        /** @type {Map<string, { entityType: string, group: string, labelKey: string, items: typeof flatResults.value }>} */
        const map = new Map();

        for (const row of flatResults.value) {
            const key = row.entityType || "unknown";
            if (!map.has(key)) {
                map.set(key, {
                    entityType: key,
                    group: row.group || key,
                    labelKey: globalSearchEntityLabelKey(key),
                    items: [],
                });
            }
            map.get(key).items.push(row);
        }

        return [...map.values()].sort((a, b) => {
            const ai = typeOrderIndex[a.entityType] ?? 999;
            const bi = typeOrderIndex[b.entityType] ?? 999;
            return ai - bi;
        });
    });

    const clearResults = () => {
        flatResults.value = [];
        error.value = null;
        hasMore.value = false;
    };

    const setQuery = (value) => {
        query.value = value ?? "";
    };

    const close = () => {
        isOpen.value = false;
    };

    const resetPagination = () => {
        fetchLimit.value = pageSize;
        hasMore.value = false;
    };

    const buildSearchUrl = (searchText) => {
        const params = new URLSearchParams();
        params.set("q", searchText);
        params.set("limit", String(fetchLimit.value));

        const types = Array.isArray(selectedTypes?.value) ? selectedTypes.value : [];
        const states = Array.isArray(selectedStates?.value) ? selectedStates.value : [];

        for (const t of types) {
            if (t) {
                params.append("types[]", t);
            }
        }
        for (const s of states) {
            if (s) {
                params.append("states[]", s);
            }
        }

        try {
            const base = route("api.global-search");

            return `${base}?${params.toString()}`;
        } catch (e) {
            // eslint-disable-next-line no-console
            console.warn("[useGlobalEntitySearch] Route Ziggy manquante : api.global-search");
            return null;
        }
    };

    /**
     * @param {{ append?: boolean }} [opts]
     */
    const searchNow = async (opts = {}) => {
        const append = Boolean(opts.append);
        const q = (query.value || "").trim();

        if (q.length < minQueryLength) {
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            loading.value = false;
            loadingMore.value = false;
            clearResults();
            isOpen.value = false;
            lastSearchKey = "";
            return;
        }

        const searchKey = JSON.stringify({
            q,
            types: selectedTypes?.value ?? [],
            states: selectedStates?.value ?? [],
        });

        if (!append && searchKey !== lastSearchKey) {
            resetPagination();
        }
        lastSearchKey = searchKey;

        if (abortController) {
            abortController.abort();
        }
        abortController = typeof AbortController !== "undefined" ? new AbortController() : null;

        if (append) {
            loadingMore.value = true;
        } else {
            loading.value = true;
        }
        error.value = null;
        isOpen.value = true;

        try {
            const controller = abortController;
            const url = buildSearchUrl(q);
            if (!url) {
                if (!append) {
                    flatResults.value = [];
                }
                return;
            }

            const res = await fetch(url, {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
                signal: controller?.signal,
            });

            if (!res.ok) {
                if (!append) {
                    flatResults.value = [];
                }
                error.value = "Recherche indisponible pour le moment.";
                return;
            }

            const data = await res.json();
            const list = Array.isArray(data?.results) ? data.results : [];
            const filtered = list.filter((row) => row && row.href);
            favoriteVersion.value;
            flatResults.value = rankResultsWithFavoritesFirst(filtered, isEntityFavorite);
            hasMore.value = Boolean(data?.meta?.hasMore);
        } catch (e) {
            if (e?.name === "AbortError") {
                return;
            }
            // eslint-disable-next-line no-console
            console.error("[useGlobalEntitySearch]", e);
            error.value = e?.message || "Erreur lors de la recherche.";
        } finally {
            loading.value = false;
            loadingMore.value = false;
        }
    };

    const loadMore = () => {
        if (!hasMore.value || loading.value || loadingMore.value) {
            return;
        }
        fetchLimit.value += pageSize;
        return searchNow({ append: true });
    };

    const debouncedSearch = () => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(() => {
            searchNow();
        }, debounce);
    };

    watch(
        () => query.value,
        () => {
            debouncedSearch();
        }
    );

    watch(
        () => [selectedTypes?.value, selectedStates?.value],
        () => {
            debouncedSearch();
        },
        { deep: true }
    );

    return {
        query,
        results: flatResults,
        groupedResults,
        loading,
        loadingMore,
        error,
        isOpen,
        hasResults,
        hasMore,
        setQuery,
        searchNow,
        debouncedSearch,
        clearResults,
        loadMore,
        close,
    };
}

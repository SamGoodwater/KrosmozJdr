/**
 * Composable : recherche + pagination + rawItems.
 * Responsabilité unique : requête GET search et pagination. Ne gère pas les statuts ni le preview.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { computed, ref } from "vue";
import { getJson } from "@/utils/scrapping/api";
import { parseIdsFilter } from "@/utils/scrapping/parseIdsFilter";

/**
 * @param {{
 *   entityTypeRef: import('vue').Ref<string>,
 *   configRef: import('vue').Ref<Record<string, { filters?: { supported?: Array<{ key?: string }> } }>>,
 *   filterRefs: {
 *     filterIds: import('vue').Ref<string>,
 *     filterName: import('vue').Ref<string>,
 *     optSkipCache: import('vue').Ref<boolean>,
 *     typeMode: import('vue').Ref<string>,
 *     filterTypeIds: import('vue').Ref<number[]>,
 *     filterTypeIdsNot: import('vue').Ref<number[]>,
 *     raceMode: import('vue').Ref<string>,
 *     filterRaceIds: import('vue').Ref<number[]>,
 *     filterRaceId: import('vue').Ref<string>,
 *     filterBreedId: import('vue').Ref<string>,
 *     filterLevelMin: import('vue').Ref<string>,
 *     filterLevelMax: import('vue').Ref<string>,
 *     pageNumber: import('vue').Ref<number>,
 *     perPage: import('vue').Ref<number>
 *   },
 *   notifyError: (msg: string) => void,
 *   onSearchDone?: (ids: number[]) => void
 * }} options
 */
export function useScrappingSearch(options) {
    const { entityTypeRef, configRef, filterRefs, notifyError, onSearchDone } = options;
    const rawItems = ref([]);
    const lastMeta = ref(null);
    const searching = ref(false);

    function supports(key) {
        const supported = configRef.value?.[entityTypeRef.value]?.filters?.supported;
        return Array.isArray(supported) && supported.some((f) => String(f?.key || "") === key);
    }

    function buildSearchQuery() {
        const q = new URLSearchParams();
        const r = filterRefs;
        if (r.optSkipCache.value) q.set("skip_cache", "true");

        const idsPart = parseIdsFilter(r.filterIds.value || "");
        for (const [k, v] of Object.entries(idsPart)) q.set(k, String(v));

        if (String(r.filterName.value || "").trim() !== "") q.set("name", String(r.filterName.value).trim());

        if (supports("typeIds") || supports("typeIdsNot")) {
            q.set("type_mode", String(r.typeMode.value || "allowed"));
            if (String(r.typeMode.value) === "selected") {
                const includeTypeIds = supports("typeIds") ? (r.filterTypeIds.value || []) : [];
                const excludeTypeIds = supports("typeIdsNot") ? (r.filterTypeIdsNot.value || []) : [];
                if (Array.isArray(includeTypeIds) && includeTypeIds.length) q.set("typeIds", includeTypeIds.join(","));
                if (Array.isArray(excludeTypeIds) && excludeTypeIds.length) q.set("typeIdsNot", excludeTypeIds.join(","));
            }
        }

        if (supports("raceId")) {
            q.set("race_mode", String(r.raceMode.value || "allowed"));
            if (String(r.raceMode.value) === "selected") {
                const ids = r.filterRaceIds.value || [];
                if (Array.isArray(ids) && ids.length) q.set("raceIds", ids.join(","));
            } else if (String(r.filterRaceId.value || "").trim() !== "") {
                q.set("raceId", String(r.filterRaceId.value).trim());
            }
        }
        if (supports("breedId") && String(r.filterBreedId.value || "").trim() !== "") q.set("breedId", String(r.filterBreedId.value).trim());
        if (supports("levelMin") && String(r.filterLevelMin.value || "").trim() !== "") q.set("levelMin", String(r.filterLevelMin.value).trim());
        if (supports("levelMax") && String(r.filterLevelMax.value || "").trim() !== "") q.set("levelMax", String(r.filterLevelMax.value).trim());

        q.set("page", String(Math.max(1, Math.floor(Number(r.pageNumber.value) || 1))));
        q.set("per_page", String(Math.max(1, Math.min(200, Math.floor(Number(r.perPage.value) || 100)))));

        return q.toString();
    }

    async function runSearch() {
        searching.value = true;
        try {
            const qs = buildSearchQuery();
            const entityStr = entityTypeRef.value;
            const url = `/api/scrapping/search/${entityStr}${qs ? `?${qs}` : ""}`;
            const result = await getJson(url);
            if (result.ok && result.data?.success) {
                rawItems.value = result.data.data?.items || [];
                lastMeta.value = result.data.data?.meta || null;
                const ids = (rawItems.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n) && n > 0);
                if (onSearchDone) onSearchDone(ids);
                return;
            }
            notifyError(result.error || result.data?.message || "Erreur lors de la recherche");
        } catch (e) {
            notifyError("Erreur lors de la recherche : " + (e?.message ?? "erreur"));
        } finally {
            searching.value = false;
        }
    }

    const totalPages = computed(() => {
        const t = Number(lastMeta.value?.total_pages);
        if (Number.isFinite(t) && t > 0) return Math.floor(t);
        const total = Number(lastMeta.value?.total);
        const pp = Math.max(1, Math.floor(Number(filterRefs.perPage.value) || 100));
        if (Number.isFinite(total) && total > 0) return Math.ceil(total / pp);
        return null;
    });

    const canPrev = computed(() => Number(filterRefs.pageNumber.value) > 1 && !searching.value);
    const canNext = computed(() => {
        const tp = totalPages.value;
        if (tp === null) return !searching.value;
        return Number(filterRefs.pageNumber.value) < tp && !searching.value;
    });

    const totalRows = computed(() => {
        const t = Number(lastMeta.value?.total);
        return Number.isFinite(t) && t > 0 ? Math.floor(t) : (rawItems.value?.length || 0);
    });

    async function goPrev() {
        if (!canPrev.value) return;
        filterRefs.pageNumber.value = Math.max(1, Number(filterRefs.pageNumber.value) - 1);
        await runSearch();
    }

    async function goNext() {
        if (!canNext.value) return;
        filterRefs.pageNumber.value = Number(filterRefs.pageNumber.value) + 1;
        await runSearch();
    }

    async function goToPage(n) {
        const next = Math.max(0, Math.floor(Number(n) || 0));
        filterRefs.pageNumber.value = next + 1;
        await runSearch();
    }

    async function setPageSize(v) {
        const n = Math.max(1, Math.min(200, Math.floor(Number(v) || 100)));
        filterRefs.perPage.value = n;
        filterRefs.pageNumber.value = 1;
        await runSearch();
    }

    return {
        rawItems,
        lastMeta,
        searching,
        pageNumber: filterRefs.pageNumber,
        perPage: filterRefs.perPage,
        totalPages,
        totalRows,
        canPrev,
        canNext,
        buildSearchQuery,
        runSearch,
        goPrev,
        goNext,
        goToPage,
        setPageSize,
    };
}

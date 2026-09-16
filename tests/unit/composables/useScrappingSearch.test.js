import { describe, expect, it, vi } from "vitest";
import { nextTick, ref } from "vue";
import { useScrappingSearch } from "@/Composables/scrapping/useScrappingSearch";

vi.mock("@/utils/scrapping/api", () => ({
    DOFUSDB_API_PREFIX: "/api/dofusdb",
    getJson: vi.fn(),
}));

import { getJson } from "@/utils/scrapping/api";

function makeSearch(onlyMissing) {
    return useScrappingSearch({
        entityTypeRef: ref("monster"),
        configRef: ref({
            monster: { filters: { supported: [{ key: "name" }, { key: "raceId" }] } },
        }),
        filterRefs: {
            filterIds: ref(""),
            filterName: ref(""),
            optSkipCache: ref(false),
            typeMode: ref("allowed"),
            filterTypeIds: ref([]),
            filterTypeIdsNot: ref([]),
            raceMode: ref("allowed"),
            filterRaceIds: ref([]),
            filterRaceId: ref(""),
            filterBreedId: ref(""),
            filterLevelMin: ref(""),
            filterLevelMax: ref(""),
            pageNumber: ref(1),
            perPage: ref(50),
            onlyMissing,
        },
        notifyError: vi.fn(),
    });
}

describe("useScrappingSearch", () => {
    it("ajoute only_missing=1 à la requête en mode Compléter", async () => {
        getJson.mockResolvedValue({
            ok: true,
            data: { success: true, data: { items: [{ id: 32 }], meta: { total: 1, only_missing: true } } },
        });
        const search = makeSearch(ref(true));
        expect(search.buildSearchQuery()).toContain("only_missing=1");
        await search.runSearch();
        expect(getJson).toHaveBeenCalled();
        const url = String(getJson.mock.calls[0][0]);
        expect(url).toContain("/api/dofusdb/search/monster");
        expect(url).toContain("only_missing=1");
        await nextTick();
        expect(search.lastMeta.value?.total).toBe(1);
    });

    it("n’ajoute pas only_missing hors mode Compléter", () => {
        const search = makeSearch(ref(false));
        expect(search.buildSearchQuery()).not.toContain("only_missing");
    });
});

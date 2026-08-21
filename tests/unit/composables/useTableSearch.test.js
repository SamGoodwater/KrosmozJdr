import { describe, it, expect, vi } from "vitest";
import { ref } from "vue";
import { useTableSearch } from "@/Composables/table/useTableSearch.js";

describe("useTableSearch", () => {
    it("émet search + page 1 en mode serveur (toValue)", async () => {
        vi.useFakeTimers();
        const emitted = [];
        const { handleSearchInput } = useTableSearch({
            serverSide: ref(true),
            activeFilters: ref({ rarity: "1" }),
            debounceMs: 10,
            onServerParamsChange: (params) => emitted.push(params),
        });

        handleSearchInput("  dragon ");
        expect(emitted).toHaveLength(0);
        vi.advanceTimersByTime(20);

        expect(emitted).toHaveLength(1);
        expect(emitted[0]).toEqual({
            search: "dragon",
            filters: { rarity: "1" },
            page: 1,
        });
        vi.useRealTimers();
    });
});

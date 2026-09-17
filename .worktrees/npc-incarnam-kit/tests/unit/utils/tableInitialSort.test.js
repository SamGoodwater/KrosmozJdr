import { describe, expect, it } from "vitest";
import { tableInitialServerSort, tableInitialSortingState } from "@/Utils/table/tableInitialSort.js";

describe("tableInitialSort", () => {
    const config = {
        features: { sort: { initial: { field: "level", dir: "asc" } } },
    };

    it("hydrate l’état TanStack [{ id, desc }]", () => {
        expect(tableInitialSortingState(config)).toEqual([{ id: "level", desc: false }]);
        expect(tableInitialSortingState(config, new Set(["name"]))).toEqual([]);
        expect(tableInitialSortingState({})).toEqual([]);
    });

    it("produit les params serveur sort / order / sorts", () => {
        expect(tableInitialServerSort(config)).toEqual({
            sort: "level",
            order: "asc",
            sorts: [{ field: "level", dir: "asc" }],
        });
        expect(tableInitialServerSort({})).toEqual({});
    });
});

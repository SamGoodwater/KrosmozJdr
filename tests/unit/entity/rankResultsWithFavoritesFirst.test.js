import { describe, it, expect } from "vitest";
import { rankResultsWithFavoritesFirst } from "@/Utils/entity/rankResultsWithFavoritesFirst";

describe("rankResultsWithFavoritesFirst", () => {
    it("place les favoris en tête en conservant l’ordre relatif", () => {
        const rows = [
            { id: 1, entityType: "spells", title: "A" },
            { id: 2, entityType: "items", title: "B" },
            { id: 3, entityType: "spells", title: "C" },
        ];
        const isFavorite = (type, id) => type === "spells" && String(id) === "3";
        expect(rankResultsWithFavoritesFirst(rows, isFavorite).map((r) => r.id)).toEqual([3, 1, 2]);
    });
});

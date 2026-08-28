import { describe, expect, it } from "vitest";
import {
    resolveTableFilterLayout,
    shouldShowTableFilterMenuSearch,
} from "@/Utils/table/resolveTableFilterLayout.js";

describe("resolveTableFilterLayout", () => {
    it("met les booléens en toggle", () => {
        expect(resolveTableFilterLayout({ type: "boolean" })).toBe("toggle");
        expect(resolveTableFilterLayout({ type: "toggle" })).toBe("toggle");
        expect(resolveTableFilterLayout({ type: "select", isBooleanSelect: true })).toBe("toggle");
    });

    it("met listes multi et select en menu", () => {
        expect(resolveTableFilterLayout({ type: "multi" })).toBe("menu");
        expect(resolveTableFilterLayout({ type: "select" })).toBe("menu");
    });

    it("classe le texte à part", () => {
        expect(resolveTableFilterLayout({ type: "text" })).toBe("text");
    });
});

describe("shouldShowTableFilterMenuSearch", () => {
    it("n’affiche la recherche que pour les listes longues", () => {
        expect(shouldShowTableFilterMenuSearch({ optionCount: 6 })).toBe(false);
        expect(shouldShowTableFilterMenuSearch({ optionCount: 8 })).toBe(false);
        expect(shouldShowTableFilterMenuSearch({ optionCount: 9 })).toBe(true);
        expect(shouldShowTableFilterMenuSearch({ optionCount: 40, searchable: false })).toBe(false);
    });
});

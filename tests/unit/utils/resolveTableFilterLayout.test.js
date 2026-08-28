import { describe, expect, it } from "vitest";
import { resolveTableFilterLayout } from "@/Utils/table/resolveTableFilterLayout.js";

describe("resolveTableFilterLayout", () => {
    it("met les booléens en toggle", () => {
        expect(resolveTableFilterLayout({ type: "boolean" })).toBe("toggle");
        expect(resolveTableFilterLayout({ type: "toggle" })).toBe("toggle");
        expect(resolveTableFilterLayout({ type: "select", isBooleanSelect: true, optionCount: 2 })).toBe("toggle");
    });

    it("met un petit multi en chips", () => {
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 6 })).toBe("chips");
        expect(resolveTableFilterLayout({ type: "select", optionCount: 5 })).toBe("chips");
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 8 })).toBe("chips");
    });

    it("met une liste longue ou vide en menu", () => {
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 9 })).toBe("menu");
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 40 })).toBe("menu");
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 0 })).toBe("menu");
    });

    it("honore filter.ui.layout", () => {
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 3, uiLayout: "menu" })).toBe("menu");
        expect(resolveTableFilterLayout({ type: "multi", optionCount: 40, uiLayout: "chips" })).toBe("chips");
    });

    it("classe le texte à part", () => {
        expect(resolveTableFilterLayout({ type: "text" })).toBe("text");
    });
});

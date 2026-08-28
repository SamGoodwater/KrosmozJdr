import { describe, expect, it } from "vitest";
import {
    normalizeTableRowId,
    toSelectedIdSet,
    selectedSetHas,
} from "@/Utils/table/normalizeTableRowId.js";

describe("normalizeTableRowId", () => {
    it("convertit une chaîne numérique en nombre", () => {
        expect(normalizeTableRowId("12")).toBe(12);
        expect(normalizeTableRowId(12)).toBe(12);
    });

    it("rejette les valeurs vides", () => {
        expect(normalizeTableRowId(null)).toBeNull();
        expect(normalizeTableRowId("")).toBeNull();
        expect(normalizeTableRowId(undefined)).toBeNull();
    });

    it("conserve un identifiant non numérique", () => {
        expect(normalizeTableRowId("abc")).toBe("abc");
    });
});

describe("toSelectedIdSet / selectedSetHas", () => {
    it("unifie string et number dans le Set", () => {
        const set = toSelectedIdSet(["3", 5]);
        expect(selectedSetHas(set, "3")).toBe(true);
        expect(selectedSetHas(set, 3)).toBe(true);
        expect(selectedSetHas(set, "5")).toBe(true);
        expect(selectedSetHas(set, 99)).toBe(false);
    });
});

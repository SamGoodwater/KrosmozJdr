import { describe, expect, it } from "vitest";
import {
    isTableRangeActive,
    minFormulaInteger,
    normalizeTableRangeValue,
    rangeBoundsFromFilterOption,
} from "@/Utils/table/tableRangeFilter.js";

describe("tableRangeFilter", () => {
    it("normalise min/max et inverse si besoin", () => {
        expect(normalizeTableRangeValue({ min: 12, max: 3 }, { min: 0, max: 20 })).toEqual({
            min: 3,
            max: 12,
        });
        expect(normalizeTableRangeValue(null, { min: 1, max: 200 })).toEqual({
            min: 1,
            max: 200,
        });
    });

    it("considère inactive une plage égale aux bornes du slider", () => {
        const bounds = { min: 1, max: 200 };
        expect(isTableRangeActive({ min: 1, max: 200 }, bounds)).toBe(false);
        expect(isTableRangeActive({ min: 5, max: 50 }, bounds)).toBe(true);
        expect(isTableRangeActive("", bounds)).toBe(false);
    });

    it("lit les bornes depuis filterOptions ou ui", () => {
        expect(rangeBoundsFromFilterOption({ min: 2, max: 40 })).toEqual({ min: 2, max: 40 });
        expect(rangeBoundsFromFilterOption([], { min: 0, max: 20 }, { min: 1, max: 12 })).toEqual({
            min: 1,
            max: 12,
        });
    });

    it("élargit une plage collapsée avec ui / fallback", () => {
        expect(
            rangeBoundsFromFilterOption({ min: 50, max: 50 }, { min: 0, max: 20 }, { min: 1, max: 200 }),
        ).toEqual({ min: 1, max: 200 });
    });

    it("prend l’entier min d’une formule au niveau 1", () => {
        expect(minFormulaInteger("3")).toBe(3);
        expect(minFormulaInteger("{2+[level]}")).toBe(3);
    });
});

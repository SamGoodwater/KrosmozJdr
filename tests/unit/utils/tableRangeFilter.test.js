import { describe, expect, it } from "vitest";
import {
    isTableRangeActive,
    normalizeTableRangeValue,
    rangeBoundsFromFilterOption,
    rangeValuePercent,
} from "@/Utils/table/tableRangeFilter.js";
import { minFormulaInteger } from "@/Utils/table/minFormulaInteger.js";

describe("tableRangeFilter", () => {
    it("normalise min/max même inversés", () => {
        expect(normalizeTableRangeValue({ min: 12, max: 3 })).toEqual({ min: 3, max: 12 });
        expect(normalizeTableRangeValue(["1", "10"])).toBeNull();
    });

    it("considère inactif une plage égale aux bornes", () => {
        expect(isTableRangeActive({ min: 1, max: 20 }, { min: 1, max: 20 })).toBe(false);
        expect(isTableRangeActive({ min: 3, max: 20 }, { min: 1, max: 20 })).toBe(true);
    });

    it("lit les bornes serveur {min,max}", () => {
        expect(rangeBoundsFromFilterOption({ min: 0, max: 12 })).toEqual({ min: 0, max: 12 });
        expect(rangeBoundsFromFilterOption([{ value: "1" }])).toBeNull();
    });

    it("place un curseur en pourcentage", () => {
        expect(rangeValuePercent(5, 0, 10)).toBe(50);
        expect(rangeValuePercent(0, 0, 10)).toBe(0);
        expect(rangeValuePercent(10, 0, 10)).toBe(100);
        expect(rangeValuePercent(3, 3, 3)).toBe(0);
    });
});

describe("minFormulaInteger", () => {
    it("prend l’entier d’une saisie simple", () => {
        expect(minFormulaInteger("10")).toBe(10);
        expect(minFormulaInteger("")).toBeNull();
    });

    it("évalue une formule au niveau 1", () => {
        expect(minFormulaInteger("{[niveau]}", 1)).toBe(1);
    });
});

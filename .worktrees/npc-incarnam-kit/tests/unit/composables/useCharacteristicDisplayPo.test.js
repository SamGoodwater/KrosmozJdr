/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import {
    formatPoRangeDisplay,
    isPoCac,
    normalizePoPart,
    trimTrailingPoSeparators,
} from "@/Composables/entity/useCharacteristicDisplay";

describe("formatPoRangeDisplay / isPoCac (PO sorts)", () => {
    it("normalizePoPart : null / vide / trim", () => {
        expect(normalizePoPart(null)).toBeNull();
        expect(normalizePoPart(undefined)).toBeNull();
        expect(normalizePoPart("")).toBeNull();
        expect(normalizePoPart("   ")).toBeNull();
        expect(normalizePoPart("  3  ")).toBe("3");
    });

    it("deux bornes égales → une seule valeur sans tiret", () => {
        expect(formatPoRangeDisplay("2", "2")).toBe("2");
    });

    it("deux bornes différentes → tiret avec espaces", () => {
        expect(formatPoRangeDisplay("2", "6")).toBe("2 - 6");
    });

    it("une seule borne renseignée → pas de tiret", () => {
        expect(formatPoRangeDisplay("3", null)).toBe("3");
        expect(formatPoRangeDisplay("3", "")).toBe("3");
        expect(formatPoRangeDisplay(null, "5")).toBe("5");
        expect(formatPoRangeDisplay("", "[level]*2")).toBe("[level]*2");
    });

    it("les deux vides → null", () => {
        expect(formatPoRangeDisplay(null, null)).toBeNull();
        expect(formatPoRangeDisplay("", "")).toBeNull();
    });

    it("trimTrailingPoSeparators : enlève les tirets de fin parasites", () => {
        expect(trimTrailingPoSeparators("0 - 6 - ")).toBe("0 - 6");
        expect(trimTrailingPoSeparators("0 - 6 -")).toBe("0 - 6");
        expect(trimTrailingPoSeparators("3")).toBe("3");
        expect(trimTrailingPoSeparators("  ")).toBeNull();
    });

    it("CAC : littéral 1 (seul ou plage 1-1 legacy)", () => {
        expect(isPoCac("1")).toBe(true);
        expect(isPoCac("1-1")).toBe(true);
        expect(isPoCac(formatPoRangeDisplay("1", null))).toBe(true);
        expect(isPoCac(formatPoRangeDisplay(null, "1"))).toBe(true);
        expect(isPoCac(formatPoRangeDisplay("1", "1"))).toBe(true);
        expect(isPoCac("[level]")).toBe(false);
        expect(isPoCac("1+[sta]")).toBe(false);
    });

    it("UI CAC : le libellé affiché est « CAC » (pas une chaîne vide)", () => {
        expect(formatPoRangeDisplay("1", "1")).toBe("1");
        expect(isPoCac(formatPoRangeDisplay("1", "1"))).toBe(true);
    });
});

// @vitest-environment node
import { describe, expect, it } from "vitest";
import {
    METERS_PER_CASE,
    formatDisplacementForDisplay,
    metersFromCases,
    parseLiteralDisplacementNumber,
    previewMetersFromCellsFormula,
} from "../../../resources/js/Utils/Entity/displacementFormat.js";

describe("displacementFormat", () => {
    it("expose 1,5 m par case", () => {
        expect(METERS_PER_CASE).toBe(1.5);
    });

    it("convertit les cases en mètres (1 décimale)", () => {
        expect(metersFromCases(2)).toBe(3);
        expect(metersFromCases(1)).toBe(1.5);
        expect(metersFromCases(0.33)).toBeCloseTo(0.5, 1);
    });

    it("parse les littéraux numériques", () => {
        expect(parseLiteralDisplacementNumber("3")).toBe(3);
        expect(parseLiteralDisplacementNumber("0,33")).toBeCloseTo(0.33, 5);
        expect(parseLiteralDisplacementNumber("1d3")).toBeNull();
        expect(parseLiteralDisplacementNumber("[level]")).toBeNull();
    });

    it("formate l’affichage cases + mètres pour un littéral", () => {
        expect(formatDisplacementForDisplay("3")).toBe("3 cases (4,5 m)");
        expect(formatDisplacementForDisplay("1")).toBe("1 case (1,5 m)");
    });

    it("affiche les formules sans conversion mètres", () => {
        expect(formatDisplacementForDisplay("1d3+1")).toBe("1d3+1 cases");
    });

    it("aperçu éditeur pour littéral uniquement", () => {
        expect(previewMetersFromCellsFormula("2")).toContain("3");
        expect(previewMetersFromCellsFormula("2")).toContain("m");
        expect(previewMetersFromCellsFormula("1d3")).toBeNull();
    });
});

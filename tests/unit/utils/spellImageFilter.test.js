/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import {
    SPELL_IMAGE_FILTER_PRESETS,
    averageSaturationFromImageData,
    paramsToCssFilter,
    resolveSpellImageFilterParams,
    shouldHintBeigeFilter,
    spellImageFilterCss,
    spellImageFilterStyle,
    spellLooksElementNeutral,
} from "@/Utils/Entity/spellImageFilter";

describe("spellImageFilter", () => {
    it("null / absent → pas de filtre", () => {
        expect(spellImageFilterCss(null)).toBe("");
        expect(spellImageFilterCss({})).toBe("");
        expect(spellImageFilterCss({ image_filter: null })).toBe("");
        expect(spellImageFilterStyle(null)).toBeUndefined();
    });

    it("préréglage beige et violet", () => {
        expect(spellImageFilterCss({ image_filter: { preset: "beige" } })).toBe(
            paramsToCssFilter(SPELL_IMAGE_FILTER_PRESETS.beige),
        );
        expect(spellImageFilterCss({ image_filter: { preset: "violet" } })).toContain("hue-rotate(220deg)");
        expect(spellImageFilterStyle({ image_filter: { preset: "beige" } })).toEqual({
            filter: paramsToCssFilter(SPELL_IMAGE_FILTER_PRESETS.beige),
        });
    });

    it("custom borne les valeurs", () => {
        const params = resolveSpellImageFilterParams({
            preset: "custom",
            grayscale: 99,
            sepia: -1,
            hue: 400,
            saturate: 10,
            brightness: undefined,
            contrast: "x",
        });
        expect(params).toEqual({
            grayscale: 2,
            sepia: 0,
            hue: 360,
            saturate: 3,
            brightness: 1.05,
            contrast: 0.95,
        });
    });

    it("spellLooksElementNeutral", () => {
        expect(spellLooksElementNeutral({ element: null })).toBe(true);
        expect(spellLooksElementNeutral({ element: 0 })).toBe(true);
        expect(spellLooksElementNeutral({ element: 1 })).toBe(true);
        expect(spellLooksElementNeutral({ element: 2 })).toBe(false);
        expect(spellLooksElementNeutral({ element: 16 })).toBe(false);
    });

    it("shouldHintBeigeFilter + averageSaturationFromImageData", () => {
        expect(shouldHintBeigeFilter(0.2)).toBe(true);
        expect(shouldHintBeigeFilter(0.01)).toBe(false);
        const gray = { data: Uint8ClampedArray.from([128, 128, 128, 255, 200, 200, 200, 255]) };
        expect(averageSaturationFromImageData(gray, 1)).toBe(0);
        const red = { data: Uint8ClampedArray.from([255, 0, 0, 255]) };
        expect(averageSaturationFromImageData(red, 1)).toBe(1);
    });
});

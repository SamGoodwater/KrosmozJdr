import { describe, expect, it } from "vitest";
import {
    ELEMENT_PRIMARY_CSS_VARS,
    getElementGlassSurfaceStyle,
    getElementSurfaceRingClass,
    primariesToMask,
    resolveEntityElementValue,
} from "@/Utils/Entity/Elements.js";

describe("resolveEntityElementValue", () => {
    it("lit element puis _data.element", () => {
        expect(resolveEntityElementValue({ element: 4 })).toBe(4);
        expect(resolveEntityElementValue({ _data: { element: 2 } })).toBe(2);
        expect(resolveEntityElementValue(null)).toBeNull();
        expect(resolveEntityElementValue({})).toBeNull();
    });
});

describe("getElementGlassSurfaceStyle", () => {
    it("reste vide sans élément", () => {
        expect(getElementGlassSurfaceStyle(null)).toEqual({});
        expect(getElementGlassSurfaceStyle("")).toEqual({});
        expect(getElementGlassSurfaceStyle(undefined)).toEqual({});
    });

    it("reste vide pour le masque 0", () => {
        expect(getElementGlassSurfaceStyle(0)).toEqual({});
    });

    it("pose une bordure pour un primaire (Terre, Feu, Air, Eau…)", () => {
        expect(getElementGlassSurfaceStyle(primariesToMask([4]))).toEqual({
            "--element-border-color": ELEMENT_PRIMARY_CSS_VARS[4],
        });
        expect(getElementGlassSurfaceStyle(1)).toEqual({
            "--element-border-color": ELEMENT_PRIMARY_CSS_VARS[0],
        });
        expect(getElementGlassSurfaceStyle(4)).toEqual({
            "--element-border-color": ELEMENT_PRIMARY_CSS_VARS[2],
        });
        expect(getElementGlassSurfaceStyle(8)).toEqual({
            "--element-border-color": ELEMENT_PRIMARY_CSS_VARS[3],
        });
    });

    it("n’écrit pas --bg-color (le glass reste le thème)", () => {
        expect(getElementGlassSurfaceStyle(primariesToMask([4]))["--bg-color"]).toBeUndefined();
        expect(getElementGlassSurfaceStyle(primariesToMask([1, 2]))["--bg-color"]).toBeUndefined();
    });

    it("ajoute un dégradé de bordure pour plusieurs primaires", () => {
        const terreFeuSagesse = primariesToMask([1, 2, 5]);
        const style = getElementGlassSurfaceStyle(terreFeuSagesse);
        expect(style["--element-border-color"]).toBe(ELEMENT_PRIMARY_CSS_VARS[1]);
        expect(style["--element-border-image"]).toContain("linear-gradient(90deg");
        expect(style["--element-border-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[1]);
        expect(style["--element-border-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[2]);
        expect(style["--element-border-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[5]);
        expect(style["background-image"]).toBeUndefined();
    });
});

describe("getElementSurfaceRingClass", () => {
    it("pose la classe seulement si une couleur de bordure est définie", () => {
        expect(getElementSurfaceRingClass({})).toBe("");
        expect(getElementSurfaceRingClass(null)).toBe("");
        expect(getElementSurfaceRingClass({ "--element-border-color": ELEMENT_PRIMARY_CSS_VARS[2] }))
            .toBe("entity-element-ring");
    });
});

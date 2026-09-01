import { describe, expect, it } from "vitest";
import {
    ELEMENT_PRIMARY_CSS_VARS,
    getElementGlassSurfaceStyle,
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

    it("traite 0 comme Neutre (ancien code)", () => {
        expect(getElementGlassSurfaceStyle(0)).toEqual({
            "--bg-color": ELEMENT_PRIMARY_CSS_VARS[0],
        });
    });

    it("teinte --bg-color pour un primaire", () => {
        expect(getElementGlassSurfaceStyle(4)).toEqual({
            "--bg-color": ELEMENT_PRIMARY_CSS_VARS[4],
        });
    });

    it("ajoute un dégradé pour plusieurs primaires", () => {
        const terreFeuSagesse = primariesToMask([1, 2, 5]);
        const style = getElementGlassSurfaceStyle(terreFeuSagesse);
        expect(style["--bg-color"]).toBe(ELEMENT_PRIMARY_CSS_VARS[1]);
        expect(style["background-image"]).toContain("linear-gradient(90deg");
        expect(style["background-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[1]);
        expect(style["background-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[2]);
        expect(style["background-image"]).toContain(ELEMENT_PRIMARY_CSS_VARS[5]);
    });
});

import { describe, expect, it } from "vitest";
import {
    aptitudeGlyphFill,
    normalizeAptitudeName,
    resolveAptitudeGlyph,
} from "@/Utils/entity/aptitudeGlyphs";

describe("aptitudeGlyphs", () => {
    it("normalise accents et ponctuation", () => {
        expect(normalizeAptitudeName("Frères d’armes")).toBe("freres d armes");
        expect(normalizeAptitudeName("Œil de l'éclaireur·euse")).toBe(
            "oeil de l eclaireur euse",
        );
    });

    it("mappe chaque aptitude seedée connue vers un glyphe distinct", () => {
        const names = [
            "Politicien",
            "Expertise en Wakfu",
            "Frères d’armes",
            "Furtivité suprême",
            "Médecine de terrain",
            "Légende vivante",
        ];
        const icons = names.map((n) => resolveAptitudeGlyph({ name: n }).icon);
        expect(new Set(icons).size).toBe(icons.length);
        expect(icons[0]).toContain("landmark");
    });

    it("reste stable pour un même nom (hash de repli)", () => {
        const a = resolveAptitudeGlyph({ name: "Aptitude inventée XYZ" });
        const b = resolveAptitudeGlyph({ name: "Aptitude inventée XYZ" });
        expect(a).toEqual(b);
        expect(a.icon).toMatch(/^fa-solid /);
    });

    it("produit une couleur oklch", () => {
        const glyph = resolveAptitudeGlyph({ name: "Politicien" });
        expect(aptitudeGlyphFill(glyph)).toMatch(/^oklch\(/);
    });
});

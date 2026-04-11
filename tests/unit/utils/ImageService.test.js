/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import { ImageService } from "@/Utils/file/ImageService";

describe("ImageService.normalizeIconsSubpath", () => {
    it("réécrit characteristics (anglais) vers le dossier réel caracteristics", () => {
        expect(ImageService.normalizeIconsSubpath("icons/characteristics/cac.webp")).toBe(
            "icons/caracteristics/cac.webp",
        );
        expect(ImageService.normalizeIconsSubpath("icons/CHARACTERISTICS/range.webp")).toBe(
            "icons/caracteristics/range.webp",
        );
    });

    it("laisse le chemin projet inchangé", () => {
        expect(ImageService.normalizeIconsSubpath("icons/caracteristics/cac.webp")).toBe(
            "icons/caracteristics/cac.webp",
        );
    });

    it("normalise l’ancienne typo caracteristiques", () => {
        expect(ImageService.normalizeIconsSubpath("icons/caracteristiques/foo.webp")).toBe(
            "icons/caracteristics/foo.webp",
        );
    });
});

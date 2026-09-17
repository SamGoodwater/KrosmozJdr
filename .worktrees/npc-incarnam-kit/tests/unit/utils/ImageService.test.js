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

describe("ImageService.getImageUrl", () => {
    it("retourne les URL http(s) sans les préfixer", async () => {
        await expect(
            ImageService.getImageUrl("https://app.test/storage/1/breed.webp"),
        ).resolves.toBe("https://app.test/storage/1/breed.webp");
    });

    it("retourne les chemins absolus tels quels", async () => {
        await expect(ImageService.getImageUrl("/storage/1/file.webp")).resolves.toBe("/storage/1/file.webp");
    });

    it("mappe un chemin relatif sous public/storage/images/", async () => {
        await expect(ImageService.getImageUrl("entity/breeds/x.webp")).resolves.toBe(
            "/storage/images/entity/breeds/x.webp",
        );
    });
});

describe("ImageService.getThumbnailUrl", () => {
    it("construit l’URL de la route media.thumbnail avec w, h, q, fit, fm", async () => {
        const url = await ImageService.getThumbnailUrl("entity/breeds/photo.jpg", {
            width: 64,
            height: 64,
            fit: "contain",
            quality: 85,
            format: "webp",
        });
        expect(url).toMatch(/^\/media\/thumbnails\//);
        expect(url).toContain("/media/thumbnails/entity/breeds/photo.jpg");
        expect(url).toContain("w=64");
        expect(url).toContain("h=64");
        expect(url).toContain("fit=contain");
        expect(url).toContain("q=85");
        expect(url).toContain("fm=webp");
    });

    it("retourne une URL http(s) inchangée", async () => {
        await expect(
            ImageService.getThumbnailUrl("https://cdn.example/a.webp", { width: 10 }),
        ).resolves.toBe("https://cdn.example/a.webp");
    });

    it("retire le préfixe /storage/images/ pour le chemin disque", async () => {
        const url = await ImageService.getThumbnailUrl("/storage/images/foo/bar.png");
        expect(url).toContain("/media/thumbnails/foo/bar.png");
    });
});

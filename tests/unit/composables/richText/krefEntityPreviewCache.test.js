import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import {
    clearKrefEntityPreviewCache,
    loadKrefEntityPreview,
    invalidateKrefEntityPreviewCache,
    toKrefPreviewApiEntityType,
    getCachedKrefEntityPreview,
    KREF_PREVIEW_API_ENTITY_TYPES,
} from "@/Composables/richText/krefEntityPreviewCache";

describe("toKrefPreviewApiEntityType", () => {
    it("mappe spell, panoply, capability et le pluriel générique", () => {
        expect(toKrefPreviewApiEntityType("spell")).toBe("spells");
        expect(toKrefPreviewApiEntityType("monster")).toBe("monsters");
        expect(toKrefPreviewApiEntityType("panoply")).toBe("panoplies");
        expect(toKrefPreviewApiEntityType("capability")).toBe("capabilities");
        expect(toKrefPreviewApiEntityType("capabilities")).toBe("capabilities");
    });

    it("laisse inchangé un type déjà au format API", () => {
        expect(toKrefPreviewApiEntityType("spells")).toBe("spells");
        expect(toKrefPreviewApiEntityType("items")).toBe("items");
    });

    it("retourne chaîne vide si entrée vide", () => {
        expect(toKrefPreviewApiEntityType("")).toBe("");
    });
});

describe("KREF_PREVIEW_API_ENTITY_TYPES", () => {
    it("contient les types servis par l’API d’aperçu", () => {
        expect(KREF_PREVIEW_API_ENTITY_TYPES.has("spells")).toBe(true);
        expect(KREF_PREVIEW_API_ENTITY_TYPES.has("panoplies")).toBe(true);
        expect(KREF_PREVIEW_API_ENTITY_TYPES.has("capabilities")).toBe(true);
    });
});

describe("krefEntityPreviewCache", () => {
    beforeEach(() => {
        clearKrefEntityPreviewCache();
    });

    afterEach(() => {
        clearKrefEntityPreviewCache();
        vi.restoreAllMocks();
    });

    it("rejette un type ou un id non supportés avant tout fetch", async () => {
        await expect(loadKrefEntityPreview("unknown-type", 1)).rejects.toThrow(/non supporté/);
        await expect(loadKrefEntityPreview("spells", "")).rejects.toThrow(/id manquant/);
        await expect(loadKrefEntityPreview("spells", null)).rejects.toThrow(/id manquant/);
    });

    it("met en cache le résultat et évite un second fetch", async () => {
        const json = { entityType: "spells", name: "Test", image: null, meta: [] };
        const fetchMock = vi.spyOn(globalThis, "fetch").mockResolvedValue({
            ok: true,
            status: 200,
            json: async () => json,
        });

        const a = await loadKrefEntityPreview("spells", 1);
        const b = await loadKrefEntityPreview("spells", 1);

        expect(a).toEqual(json);
        expect(b).toEqual(json);
        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it("dédoublonne deux appels parallèles (une seule requête)", async () => {
        const json = { entityType: "spells", name: "X", image: null, meta: [] };
        const fetchMock = vi.spyOn(globalThis, "fetch").mockImplementation(() =>
            Promise.resolve({
                ok: true,
                status: 200,
                json: async () => json,
            }),
        );

        const p1 = loadKrefEntityPreview("spells", 7);
        const p2 = loadKrefEntityPreview("spells", 7);
        const [r1, r2] = await Promise.all([p1, p2]);
        expect(r1).toEqual(json);
        expect(r2).toEqual(json);
        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it("invalidateKrefEntityPreviewCache supprime l’entrée (refetch au prochain load)", async () => {
        const json = { entityType: "items", name: "Obj", image: null, meta: [] };
        const fetchMock = vi.spyOn(globalThis, "fetch").mockResolvedValue({
            ok: true,
            status: 200,
            json: async () => json,
        });

        await loadKrefEntityPreview("items", 3);
        expect(getCachedKrefEntityPreview("items", 3)).toEqual(json);

        invalidateKrefEntityPreviewCache("items", 3);
        expect(getCachedKrefEntityPreview("items", 3)).toBeNull();

        await loadKrefEntityPreview("items", 3);
        expect(fetchMock).toHaveBeenCalledTimes(2);
    });

    it("n’invalide pas pour un type inconnu de l’API", async () => {
        const json = { entityType: "spells", name: "S", image: null, meta: [] };
        vi.spyOn(globalThis, "fetch").mockResolvedValue({
            ok: true,
            status: 200,
            json: async () => json,
        });

        await loadKrefEntityPreview("spells", 9);
        invalidateKrefEntityPreviewCache("resource-types", 9);
        expect(getCachedKrefEntityPreview("spells", 9)).toEqual(json);
    });
});

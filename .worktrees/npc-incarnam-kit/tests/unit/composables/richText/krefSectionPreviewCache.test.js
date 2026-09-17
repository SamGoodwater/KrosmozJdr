import { describe, it, expect, beforeEach } from "vitest";
import {
    clearKrefSectionPreviewCache,
    getCachedKrefSectionPreview,
    loadKrefSectionPreview,
} from "@/Composables/richText/krefSectionPreviewCache";

describe("krefSectionPreviewCache", () => {
    const info = { payload: { pageSlug: "regles", sectionId: 42 } };

    beforeEach(() => {
        clearKrefSectionPreviewCache();
    });

    it("met en cache le résultat du loader", async () => {
        const first = await loadKrefSectionPreview(info, async () => ({ title: "A", html: "<p>a</p>" }));
        const second = await loadKrefSectionPreview(info, async () => ({ title: "B", html: "<p>b</p>" }));
        expect(first.title).toBe("A");
        expect(second.title).toBe("A");
        expect(getCachedKrefSectionPreview(info)?.title).toBe("A");
    });

    it("dédoublonne les chargements concurrents", async () => {
        let callCount = 0;
        const loader = async () => {
            callCount += 1;
            return { title: "Shared", html: "<p>shared</p>" };
        };
        const [a, b] = await Promise.all([
            loadKrefSectionPreview(info, loader),
            loadKrefSectionPreview(info, loader),
        ]);
        expect(a.title).toBe("Shared");
        expect(b.title).toBe("Shared");
        expect(callCount).toBe(1);
    });
});

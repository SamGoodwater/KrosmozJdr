import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

vi.mock("@/Composables/characteristic/useCharacteristicKeySuggestions", () => ({
    buildCharacteristicKeySuggestionsFromStore: () => [
        { id: "bonus_creature", name: "Bonus", short_name: "Bou" },
        { id: "bow_creature", name: "Arc", short_name: "Bow" },
    ],
}));

vi.mock("@/Composables/entity/entityRouteRegistry", () => ({
    resolveEntityRouteHref: () => "/entities/spells/1",
}));

vi.mock("@/Composables/richText/krefEntityRegistry", () => ({
    KREF_ENTITY_CONFIGS: [
        {
            entityType: "spells",
            label: "Sort",
            icon: "fa-solid fa-wand-sparkles",
            atPrefix: "sort",
        },
    ],
}));

import { searchRichReferenceItems } from "@/Composables/richText/useRichReferenceSearch";

describe("searchRichReferenceItems", () => {
    beforeEach(() => {
        global.route = vi.fn((name) => {
            if (name === "api.tables.spells") return "/api/tables/spells";
            if (name === "api.cms.page-section-picker") return "/api/cms/picker";
            if (name === "api.cms.sections.preview-snippet") return "/api/cms/preview";
            return `/${name}`;
        });

        vi.stubGlobal(
            "fetch",
            vi.fn(async (url) => {
                if (String(url).includes("/api/tables/")) {
                    return {
                        ok: true,
                        json: async () => ({
                            entities: [{ id: 1, name: "Bouftou spell", slug: "bouftou" }],
                        }),
                    };
                }
                if (String(url).includes("/api/cms/picker")) {
                    return {
                        ok: true,
                        json: async () => ({
                            pages: [{ pageSlug: "regles", title: "Règles", href: "/pages/regles" }],
                            sections: [
                                {
                                    sectionId: 42,
                                    sectionTitle: "Bouclier",
                                    pageTitle: "Règles",
                                    pageSlug: "regles",
                                    href: "/pages/regles#s",
                                },
                            ],
                        }),
                    };
                }
                return { ok: false, json: async () => ({}) };
            }),
        );
    });

    afterEach(() => {
        vi.unstubAllGlobals();
    });

    it("ordonne caractéristiques → sections → pages → entités (mode all)", async () => {
        const results = await searchRichReferenceItems("bo", { maxResults: 12 });
        const kinds = results.map((r) => r.kind);

        expect(kinds).toContain("characteristic");
        expect(kinds).toContain("pageSection");
        expect(kinds).toContain("entity");

        const charIdx = kinds.indexOf("characteristic");
        const sectionIdx = kinds.indexOf("pageSection");
        const pageIdx = kinds.indexOf("page");
        const entityIdx = kinds.indexOf("entity");

        expect(charIdx).toBeLessThan(sectionIdx);
        expect(sectionIdx).toBeLessThan(entityIdx);
        if (pageIdx >= 0) {
            expect(sectionIdx).toBeLessThan(pageIdx);
            expect(pageIdx).toBeLessThan(entityIdx);
        }
    });

    it("respecte le plafond global maxResults", async () => {
        const results = await searchRichReferenceItems("bo", { maxResults: 2 });
        expect(results.length).toBe(2);
        expect(results[0].kind).toBe("characteristic");
        expect(results[1].kind).toBe("characteristic");
    });

});

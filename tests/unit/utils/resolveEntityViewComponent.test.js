import { describe, expect, it, vi } from "vitest";

vi.mock("@/Entities/entity-registry", () => ({
    normalizeEntityType: (t) => (t === "spells" ? "spells" : t),
}));

describe("resolveEntityViewComponent", () => {
    it("charge SpellViewFull pour spells + full", async () => {
        const { resolveEntityViewComponent } = await import(
            "@/Utils/entity/resolveEntityViewComponent"
        );
        const component = await resolveEntityViewComponent("spells", "full");
        expect(component).toBeTruthy();
        expect(String(component?.__name || component?.name || "")).toMatch(/SpellViewFull|default/i);
    }, 20000);
});

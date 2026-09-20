/** @vitest-environment node */
import { beforeEach, describe, expect, it, vi } from "vitest";

const resolveEntityViewComponent = vi.fn();
const fetchEntityModelById = vi.fn();
const supportsEntityCatalogViews = vi.fn();
const entityViewPropName = vi.fn();

vi.mock("@/Utils/entity/resolveEntityViewComponent", () => ({
    resolveEntityViewComponent: (...args) => resolveEntityViewComponent(...args),
}));

vi.mock("@/Composables/entity/useEntityTableFetch", () => ({
    entityViewPropName: (...args) => entityViewPropName(...args),
    fetchEntityModelById: (...args) => fetchEntityModelById(...args),
    supportsEntityCatalogViews: (...args) => supportsEntityCatalogViews(...args),
}));

describe("loadKrefEntityMinimalOverlay", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        supportsEntityCatalogViews.mockReturnValue(true);
        entityViewPropName.mockReturnValue("capability");
    });

    it("retourne CapabilityViewMinimal étendue pour une capacité", async () => {
        const component = { name: "CapabilityViewMinimal" };
        const model = { id: 12, name: "Analyse" };
        resolveEntityViewComponent.mockResolvedValue(component);
        fetchEntityModelById.mockResolvedValue(model);

        const { loadKrefEntityMinimalOverlay } = await import(
            "@/Composables/richText/krefEntityMinimalOverlay"
        );
        const overlay = await loadKrefEntityMinimalOverlay("capabilities", 12);

        expect(resolveEntityViewComponent).toHaveBeenCalledWith("capabilities", "minimal");
        expect(fetchEntityModelById).toHaveBeenCalledWith("capabilities", 12);
        expect(overlay).toEqual({
            component,
            props: {
                capability: model,
                showActions: true,
                displayMode: "extended",
            },
        });
    });

    it("retourne null si le type n’a pas de vue catalogue", async () => {
        supportsEntityCatalogViews.mockReturnValue(false);
        const { loadKrefEntityMinimalOverlay } = await import(
            "@/Composables/richText/krefEntityMinimalOverlay"
        );
        const overlay = await loadKrefEntityMinimalOverlay("creatures", 1);

        expect(overlay).toBeNull();
        expect(resolveEntityViewComponent).not.toHaveBeenCalled();
        expect(fetchEntityModelById).not.toHaveBeenCalled();
    });

    it("retourne null si le fetch échoue", async () => {
        resolveEntityViewComponent.mockResolvedValue({ name: "CapabilityViewMinimal" });
        fetchEntityModelById.mockRejectedValue(new Error("HTTP 404"));

        const { loadKrefEntityMinimalOverlay } = await import(
            "@/Composables/richText/krefEntityMinimalOverlay"
        );
        await expect(loadKrefEntityMinimalOverlay("capabilities", 99)).resolves.toBeNull();
    });
});

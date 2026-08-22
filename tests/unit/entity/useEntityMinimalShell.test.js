import { describe, it, expect } from "vitest";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { MINIMAL_EXPANDED_ACTION_KEYS } from "@/Entities/entity-actions-config";

describe("useEntityMinimalShell", () => {
    it("expose la whitelist et le contexte minimal", () => {
        const emitted = [];
        const emit = (...args) => emitted.push(args);
        const shell = useEntityMinimalShell({
            entityTypePlural: "items",
            showRoute: "entities.items.show",
            editRoute: "entities.items.edit",
            routeParam: "item",
            emit,
            getEntity: () => ({ id: 5, name: "Épée" }),
        });
        expect(shell.minimalActionWhitelist).toEqual(MINIMAL_EXPANDED_ACTION_KEYS);
        expect(shell.minimalActionsContext.inMinimal).toBe(true);
        shell.openQuickView();
        expect(emitted[0][0]).toBe("quick-view");
        expect(emitted[1][0]).toBe("action");
        expect(emitted[1][1]).toBe("quick-view");
    });

    it("traite view comme Afficher (modal), pas comme navigation page", () => {
        const emitted = [];
        const emit = (...args) => emitted.push(args);
        const shell = useEntityMinimalShell({
            entityTypePlural: "items",
            showRoute: "entities.items.show",
            editRoute: "entities.items.edit",
            routeParam: "item",
            emit,
            getEntity: () => ({ id: 5, name: "Épée" }),
        });
        shell.handleMinimalAction("view");
        expect(emitted[0][0]).toBe("quick-view");
        expect(emitted[1]).toEqual(["action", "quick-view", { id: 5, name: "Épée" }]);
    });
});

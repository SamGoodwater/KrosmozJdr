/**
 * Tests clés de routes entités (Phase F — Ziggy capability / conditions).
 */
import { describe, it, expect, beforeEach, vi } from "vitest";
import { getEntitySingularRouteKey, resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";

describe("entityRouteRegistry", () => {
    beforeEach(() => {
        global.route = vi.fn((name, params) => {
            if (params != null && typeof params === "object") {
                const [key, id] = Object.entries(params)[0];
                return `/entities/${name.split(".")[1]}/${id}?${key}`;
            }
            return `/entities/${name.split(".")[1]}/${params}`;
        });
    });
    it("retourne capability pour le pluriel capabilities", () => {
        expect(getEntitySingularRouteKey("capabilities")).toBe("capability");
    });

    it("résout show conditions avec le param condition", () => {
        const href = resolveEntityRouteHref("conditions", "show", 42, {
            show: { name: "entities.conditions.show", paramsMode: "object", paramKey: "condition" },
        });
        expect(href).toContain("42");
        expect(href).not.toMatch(/\/conditions\/42\/show/);
    });

    it("résout show creature-traits avec creatureTrait", () => {
        const href = resolveEntityRouteHref("creature-traits", "show", 7, {
            show: {
                name: "entities.creature-traits.show",
                paramsMode: "object",
                paramKey: "creatureTrait",
            },
        });
        expect(href).toContain("7");
    });
});

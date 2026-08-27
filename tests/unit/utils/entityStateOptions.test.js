import { describe, expect, it } from "vitest";
import {
    ENTITY_STATE_OPTIONS,
    getEntityStateBadgeColor,
    getEntityStateDisplayLabel,
    getEntityStateDotClass,
} from "@/Utils/Entity/SharedConstants";
import { UsableFormatter } from "@/Utils/Formatters/UsableFormatter.js";

describe("entity state options", () => {
    it("place Auto entre Brouillon et Jouable", () => {
        expect(ENTITY_STATE_OPTIONS.map((o) => o.value)).toEqual([
            "raw",
            "draft",
            "auto",
            "playable",
            "archived",
        ]);
        expect(getEntityStateDisplayLabel("auto")).toBe("Auto");
        expect(getEntityStateDotClass("auto")).toBe("bg-secondary");
        expect(getEntityStateBadgeColor("auto")).toBe("secondary");
    });

    it("expose auto dans UsableFormatter", () => {
        expect(UsableFormatter.options.map((o) => o.value)).toContain("auto");
        expect(UsableFormatter.format("auto")).toBe("Auto");
    });
});

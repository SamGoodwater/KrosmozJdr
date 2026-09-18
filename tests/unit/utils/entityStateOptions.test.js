import { describe, expect, it } from "vitest";
import {
    ENTITY_STATE_OPTIONS,
    getEntityStateActionLabel,
    getEntityStateBadgeColor,
    getEntityStateDisplayLabel,
    getEntityStateChipClass,
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
        expect(getEntityStateChipClass("playable", false)).toContain("bg-base-300/55");
        expect(getEntityStateChipClass("playable", true)).toContain("bg-success/35");
        expect(getEntityStateChipClass("draft", true)).toContain("bg-warning/35");
        expect(getEntityStateChipClass("raw", true)).toContain("bg-error/35");
        expect(getEntityStateChipClass("auto", true)).toContain("bg-secondary/35");
        expect(getEntityStateChipClass("archived", true)).toContain("bg-info/35");
        expect(getEntityStateBadgeColor("auto")).toBe("secondary");
        expect(getEntityStateDisplayLabel("playable")).toBe("Jouable");
        expect(getEntityStateActionLabel("playable")).toBe("Jouable");
    });

    it("expose auto dans UsableFormatter", () => {
        expect(UsableFormatter.options.map((o) => o.value)).toContain("auto");
        expect(UsableFormatter.format("auto")).toBe("Auto");
    });
});

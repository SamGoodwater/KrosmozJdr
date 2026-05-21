import { describe, expect, it } from "vitest";
import { globalSearchEntityLabelKey } from "@/Utils/entity/globalSearchEntityLabel";

describe("globalSearchEntityLabelKey", () => {
    it("mappe les clés API vers EntityLabel", () => {
        expect(globalSearchEntityLabelKey("spells")).toBe("spell");
        expect(globalSearchEntityLabelKey("pages")).toBe("page");
        expect(globalSearchEntityLabelKey("creature-traits")).toBe("creature-trait");
        expect(globalSearchEntityLabelKey("resource-types")).toBe("resource");
    });

    it("mappe les types de taxonomie (équipement, consommable, sort, race)", () => {
        expect(globalSearchEntityLabelKey("item-types")).toBe("item");
        expect(globalSearchEntityLabelKey("consumable-types")).toBe("consumable");
        expect(globalSearchEntityLabelKey("spell-types")).toBe("spell");
        expect(globalSearchEntityLabelKey("monster-races")).toBe("monster");
    });
});

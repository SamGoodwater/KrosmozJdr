import { describe, expect, it } from "vitest";
import { globalSearchEntityLabelKey } from "@/Utils/entity/globalSearchEntityLabel";

describe("globalSearchEntityLabelKey", () => {
    it("mappe les clés API vers EntityLabel", () => {
        expect(globalSearchEntityLabelKey("spells")).toBe("spell");
        expect(globalSearchEntityLabelKey("pages")).toBe("page");
        expect(globalSearchEntityLabelKey("creature-traits")).toBe("creature-trait");
        expect(globalSearchEntityLabelKey("resource-types")).toBe("resource");
    });
});

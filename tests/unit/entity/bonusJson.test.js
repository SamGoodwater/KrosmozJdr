import { describe, expect, it } from "vitest";
import { bonusNumericForKey } from "@/Utils/entity/bonusJson.js";
import {
    isPickedRangeActive,
    pickedRangeEntries,
} from "@/Utils/table/bonusFilter.js";

describe("bonusJson", () => {
    it("lit une valeur plate et somme les paliers de panoplie", () => {
        expect(bonusNumericForKey('{"strength":3}', "strength")).toBe(3);
        expect(bonusNumericForKey({ 2: { strength: 1 }, 3: { strength: 2 } }, "strength")).toBe(3);
        expect(bonusNumericForKey({ vitality: 10 }, "vitality_object")).toBe(10);
        expect(bonusNumericForKey({ strength: 1 }, "vitality")).toBeNull();
    });
});

describe("bonusFilter picked-range", () => {
    it("traite une clé sans bornes comme active", () => {
        expect(isPickedRangeActive({ strength: { on: "1" } })).toBe(true);
        expect(pickedRangeEntries({ strength: { min: 2, max: 6 } })).toEqual([
            { key: "strength", bounds: { min: 2, max: 6 } },
        ]);
        expect(isPickedRangeActive({})).toBe(false);
        expect(isPickedRangeActive({ min: 1, max: 5 })).toBe(false);
    });
});

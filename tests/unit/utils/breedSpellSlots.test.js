// @vitest-environment node
import { describe, expect, it } from "vitest";
import {
    CLASS_SPELL_UNLOCK_LEVELS,
    getStandardBreedSlotDefinitions,
} from "@/Utils/entity/breedSpellSlots.js";

describe("breedSpellSlots", () => {
    it("pose 3 emplacements au niveau 1 puis les paliers 2–12", () => {
        expect(CLASS_SPELL_UNLOCK_LEVELS).toEqual([1, 2, 4, 5, 6, 7, 8, 10, 11, 12]);

        const slots = getStandardBreedSlotDefinitions();
        expect(slots.filter((s) => s.character_level === 1)).toHaveLength(3);
        expect(slots.map((s) => s.character_level)).toEqual([
            1, 1, 1, 2, 4, 5, 6, 7, 8, 10, 11, 12,
        ]);
        expect(slots.some((s) => s.character_level === 3)).toBe(false);
        expect(slots.some((s) => s.character_level === 13)).toBe(false);
        expect(slots.some((s) => s.character_level === 14)).toBe(false);
    });
});

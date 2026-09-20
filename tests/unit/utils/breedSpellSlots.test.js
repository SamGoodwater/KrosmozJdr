// @vitest-environment node
import { describe, expect, it } from "vitest";
import {
    CLASS_SPELL_UNLOCK_LEVELS,
    breedVariantGroupTitle,
    getBreedChoiceNumber,
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
        expect(slots.map((s) => s.choice_number)).toEqual([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        expect(slots[0].label).toBe("Choix 1 · Niveau 1");
        expect(slots[4].label).toBe("Choix 5 · Niveau 4");
        expect(getBreedChoiceNumber(1, 1)).toBe(1);
        expect(getBreedChoiceNumber(4, 1)).toBe(5);
        expect(
            breedVariantGroupTitle({ character_level: 1, slot_index: 1, spells: [{}, {}, {}, {}] })
        ).toBe("Choix 1 · Niveau 1 · 4 variantes");
        expect(breedVariantGroupTitle({ character_level: 1, slot_index: 2, spells: [{}] })).toBe(
            "Choix 2 · Niveau 1 · 1 variante"
        );
    });
});

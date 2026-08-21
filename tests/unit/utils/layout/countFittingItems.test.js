import { describe, expect, it } from "vitest";
import { countFittingItems } from "@/Utils/layout/countFittingItems";

describe("countFittingItems", () => {
    it("place tous les items si la largeur suffit, en réservant le bouton more", () => {
        expect(countFittingItems([24, 24, 24], 24, 2, 200, true)).toBe(3);
    });

    it("garde le bouton more et masque les items qui dépassent", () => {
        // 24+2+24 + 2+24 (more) = 76 → 2 items ; 3 items = 102
        expect(countFittingItems([24, 24, 24], 24, 2, 80, true)).toBe(2);
    });

    it("n’affiche que le bouton more si rien d’autre ne tient", () => {
        expect(countFittingItems([24, 24], 24, 2, 24, true)).toBe(0);
    });

    it("sans alwaysReserveMore, omet le bouton more quand tout tient", () => {
        expect(countFittingItems([24, 24], 24, 2, 50, false)).toBe(2);
        expect(countFittingItems([24, 24, 24], 24, 2, 50, false)).toBe(1);
    });

    it("retourne 0 si la largeur utile est nulle ou sans items", () => {
        expect(countFittingItems([], 24, 2, 80, true)).toBe(0);
        expect(countFittingItems([24], 24, 2, 0, true)).toBe(0);
    });
});

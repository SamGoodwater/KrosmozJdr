import { describe, expect, it } from "vitest";
import { countFittingItems } from "@/Utils/layout/countFittingItems";

describe("countFittingItems", () => {
    it("n’affiche rien si la largeur utile est nulle", () => {
        expect(countFittingItems([24, 24], 24, 2, 0, true)).toBe(0);
    });

    it("réserve le bouton more et n’affiche que ce qui tient", () => {
        expect(countFittingItems([24, 24, 24], 24, 2, 80, true)).toBe(2);
    });

    it("affiche tout et masque le more si alwaysReserveMore=false", () => {
        expect(countFittingItems([24, 24], 24, 2, 50, false)).toBe(2);
    });

    it("n’affiche que le more si rien d’autre ne tient", () => {
        expect(countFittingItems([24, 24], 24, 2, 24, true)).toBe(0);
    });
});

import { describe, expect, it } from "vitest";
import { getTailwindTokenFromLabel } from "@/Utils/color/Color";

describe("getTailwindTokenFromLabel scheme level", () => {
    it("mappe 1..30 vers level-N", () => {
        expect(getTailwindTokenFromLabel("1", { scheme: "level" })).toBe("level-1");
        expect(getTailwindTokenFromLabel("12", { scheme: "level" })).toBe("level-12");
        expect(getTailwindTokenFromLabel("30", { scheme: "level" })).toBe("level-30");
    });

    it("clamp hors plage", () => {
        expect(getTailwindTokenFromLabel("0", { scheme: "level" })).toBe("level-1");
        expect(getTailwindTokenFromLabel("99", { scheme: "level" })).toBe("level-30");
    });

    it("ignore tone (rampe fixe)", () => {
        expect(getTailwindTokenFromLabel("8", { scheme: "level", tone: "light" })).toBe("level-8");
        expect(getTailwindTokenFromLabel("8", { scheme: "level", tone: "dark" })).toBe("level-8");
    });
});

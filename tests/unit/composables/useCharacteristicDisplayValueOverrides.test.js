/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import { resolveValueOverride } from "@/Composables/entity/useCharacteristicDisplay";

describe("resolveValueOverride", () => {
    const overrides = [
        { value: 1, icon: "cac.webp", color: "#e53935", subtitle: "Corps à corps" },
        { value: 0, subtitle: "Auto-cible" },
        { value: true, icon: "sightLine.webp", subtitle: "Ligne de vue requise" },
        { value: false, icon: "noSightLine.webp", subtitle: "Pas de ligne de vue" },
    ];

    it("retourne null si overrides est null, undefined ou vide", () => {
        expect(resolveValueOverride(null, 1)).toBeNull();
        expect(resolveValueOverride(undefined, 1)).toBeNull();
        expect(resolveValueOverride([], 1)).toBeNull();
    });

    it("retourne null si value est undefined", () => {
        expect(resolveValueOverride(overrides, undefined)).toBeNull();
    });

    it("match strict numérique", () => {
        const result = resolveValueOverride(overrides, 1);
        expect(result).not.toBeNull();
        expect(result.icon).toBe("cac.webp");
        expect(result.subtitle).toBe("Corps à corps");
    });

    it("match numérique 0", () => {
        const result = resolveValueOverride(overrides, 0);
        expect(result).not.toBeNull();
        expect(result.subtitle).toBe("Auto-cible");
        expect(result.icon).toBeUndefined();
    });

    it("match booléen true", () => {
        const result = resolveValueOverride(overrides, true);
        expect(result).not.toBeNull();
        expect(result.icon).toBe("sightLine.webp");
    });

    it("match booléen false", () => {
        const result = resolveValueOverride(overrides, false);
        expect(result).not.toBeNull();
        expect(result.icon).toBe("noSightLine.webp");
    });

    it("match souple : true matche value=1 si pas de true explicite", () => {
        const numOnly = [
            { value: 1, icon: "one.webp" },
            { value: 0, icon: "zero.webp" },
        ];
        expect(resolveValueOverride(numOnly, true)?.icon).toBe("one.webp");
        expect(resolveValueOverride(numOnly, false)?.icon).toBe("zero.webp");
    });

    it("match souple : '1' (string) matche value=1", () => {
        const numOnly = [{ value: 1, icon: "one.webp" }];
        expect(resolveValueOverride(numOnly, "1")?.icon).toBe("one.webp");
    });

    it("match string strict", () => {
        const stringOverrides = [
            { value: "fire", icon: "fire.webp" },
            { value: "water", icon: "water.webp" },
        ];
        expect(resolveValueOverride(stringOverrides, "fire")?.icon).toBe("fire.webp");
        expect(resolveValueOverride(stringOverrides, "earth")).toBeNull();
    });

    it("retourne null si aucune entrée ne matche", () => {
        expect(resolveValueOverride(overrides, 42)).toBeNull();
        expect(resolveValueOverride(overrides, "unknown")).toBeNull();
    });
});

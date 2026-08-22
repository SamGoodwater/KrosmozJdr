import { describe, expect, it } from "vitest";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";

describe("buildCharacteristicEffectCell — bonus de panoplie", () => {
    it("aplatit les bonus par nombre de pièces et ignore les zéros", () => {
        const cell = buildCharacteristicEffectCell({
            rawValues: [
                {
                    2: { strength: 0, vitality: 1 },
                    3: { strength: 2 },
                },
            ],
            sourceGroups: ["panoply", "item"],
        });

        expect(cell.type).toBe("chips");
        const values = (cell.params?.items || []).map((item) => String(item.value));
        expect(values).not.toContain("[object Object]");
        expect(values.sort()).toEqual(["1", "2"]);
        const labels = (cell.params?.items || []).map((item) => String(item.shortLabel));
        expect(labels.some((label) => label.startsWith("2p "))).toBe(true);
        expect(labels.some((label) => label.startsWith("3p "))).toBe(true);
    });

    it("garde un objet plat d’équipement (sans paliers de pièces)", () => {
        const cell = buildCharacteristicEffectCell({
            rawValues: [{ strength: 1, vitality: 0 }],
            sourceGroups: ["item"],
        });

        expect(cell.type).toBe("chips");
        const items = cell.params?.items || [];
        expect(items).toHaveLength(1);
        expect(String(items[0].value)).toBe("1");
    });
});

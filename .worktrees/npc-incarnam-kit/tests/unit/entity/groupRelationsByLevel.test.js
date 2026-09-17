/**
 * Regroupement des relations par niveau (Phase G — spécialisations).
 */
import { describe, it, expect } from "vitest";
import { groupRelationsByLevel } from "@/Utils/entity/groupRelationsByLevel";

describe("groupRelationsByLevel", () => {
    it("sépare les niveaux > 0 et le sans niveau (0)", () => {
        const items = [
            { id: 1, name: "A", pivot: { level: 2 } },
            { id: 2, name: "B", pivot: { level: 0 } },
            { id: 3, name: "C", pivot: { level: 2 } },
        ];
        const { withLevel, withoutLevel } = groupRelationsByLevel(items);
        expect(withLevel).toHaveLength(1);
        expect(withLevel[0].level).toBe(2);
        expect(withLevel[0].items).toHaveLength(2);
        expect(withoutLevel).toHaveLength(1);
        expect(withoutLevel[0].id).toBe(2);
    });
});

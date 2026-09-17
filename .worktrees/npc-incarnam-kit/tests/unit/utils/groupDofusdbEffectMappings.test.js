import { describe, expect, it } from "vitest";
import {
    filterDofusdbEffectMappings,
    groupDofusdbEffectMappings,
} from "@/Utils/effects/groupDofusdbEffectMappings";

describe("filterDofusdbEffectMappings", () => {
    const rows = [
        { id: 1, dofusdb_effect_id: 96, sub_effect_slug: "frapper", sub_effect_label: "Dégâts" },
        { id: 2, dofusdb_effect_id: 792, sub_effect_slug: "autre", sub_effect_label: "Autre" },
        { id: 3, dofusdb_effect_id: 150, sub_effect_slug: "appliquer-etat", characteristic_key: "invisible" },
    ];

    it("masque autre par défaut", () => {
        expect(filterDofusdbEffectMappings(rows, { showAutre: false }).map((r) => r.id)).toEqual([1, 3]);
    });

    it("garde un autre prérempli (analyse)", () => {
        expect(
            filterDofusdbEffectMappings(rows, { showAutre: false, prefillEffectId: "792" }).map((r) => r.id),
        ).toEqual([1, 2, 3]);
    });

    it("recherche par effectId et libellé", () => {
        expect(filterDofusdbEffectMappings(rows, { query: "96" }).map((r) => r.id)).toEqual([1]);
        expect(filterDofusdbEffectMappings(rows, { query: "dégâts" }).map((r) => r.id)).toEqual([1]);
        expect(filterDofusdbEffectMappings(rows, { query: "792" }).map((r) => r.id)).toEqual([2]);
    });
});

describe("groupDofusdbEffectMappings", () => {
    it("groupe par sub_effect_slug", () => {
        const grouped = groupDofusdbEffectMappings([
            { id: 1, sub_effect_slug: "frapper", sub_effect_label: "Dégâts" },
            { id: 2, sub_effect_slug: "frapper", sub_effect_label: "Dégâts" },
            { id: 3, sub_effect_slug: "autre", sub_effect_label: "Autre" },
        ]);
        expect(grouped.map((g) => g.slug)).toEqual(["frapper", "autre"]);
        expect(grouped[0].rows).toHaveLength(2);
        expect(grouped[0].label).toBe("Dégâts");
    });
});

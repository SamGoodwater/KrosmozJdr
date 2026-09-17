import { describe, expect, it } from "vitest";
import {
    GAMEPLAY_RESOURCE_TYPE_LABELS,
    hasResourceTypeFilter,
    resolveGameplayResourceTypeIds,
} from "@/Utils/Entity/gameplayResourceTypes";
import { resolveFilterDefaultValue } from "@/Utils/table/resolveFilterDefaultValue";

describe("gameplayResourceTypes", () => {
    it("résout les ids par nom et par dofusdb_type_id", () => {
        const types = [
            { id: 1, name: "Souvenir", dofusdb_type_id: 121 },
            { id: 2, name: "Bois", dofusdb_type_id: 38 },
            { id: 3, name: "Quêtes principales", dofusdb_type_id: 125 },
            { value: 4, label: "Minerai", dofusdb_type_id: 39 },
            { id: 5, name: "Cereale", dofusdb_type_id: 34 },
        ];
        expect(resolveGameplayResourceTypeIds(types)).toEqual(["2", "4", "5"]);
    });

    it("ignore les types hors métier même si le libellé ressemble", () => {
        const types = [
            { id: 1, name: "Essence de gardien de donjon", dofusdb_type_id: 167 },
            { id: 2, name: "Plante", dofusdb_type_id: 36 },
        ];
        expect(resolveGameplayResourceTypeIds(types)).toEqual(["2"]);
    });

    it("détecte un filtre type déjà présent", () => {
        expect(hasResourceTypeFilter({})).toBe(false);
        expect(hasResourceTypeFilter({ resource_type_id: [] })).toBe(false);
        expect(hasResourceTypeFilter({ resource_type_id: ["2"] })).toBe(true);
        expect(GAMEPLAY_RESOURCE_TYPE_LABELS).toContain("Bois");
        expect(GAMEPLAY_RESOURCE_TYPE_LABELS).not.toContain("Souvenir");
    });
});

describe("resolveFilterDefaultValue (ressources)", () => {
    const options = [
        { value: "1", label: "Souvenir", dofusdb_type_id: 121 },
        { value: "2", label: "Bois", dofusdb_type_id: 38 },
        { value: "3", label: "Minerai", dofusdb_type_id: 39 },
    ];

    it("résout defaultByLabel et defaultByDofusTypeId", () => {
        expect(
            resolveFilterDefaultValue(
                { defaultByLabel: ["Bois"], defaultByDofusTypeId: [39] },
                options,
            ),
        ).toEqual(["2", "3"]);
    });
});

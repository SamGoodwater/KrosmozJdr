import { describe, expect, it } from "vitest";
import {
    GAMEPLAY_ITEM_TYPE_LABELS,
    hasItemTypeFilter,
    resolveGameplayItemTypeIds,
} from "@/Utils/Entity/gameplayItemTypes";
import { resolveFilterDefaultValue } from "@/Utils/table/resolveFilterDefaultValue";

describe("gameplayItemTypes", () => {
    it("résout les ids par nom et par dofusdb_type_id", () => {
        const types = [
            { id: 10, name: "Costume", dofusdb_type_id: 199 },
            { id: 11, name: "Amulette", dofusdb_type_id: 1 },
            { id: 12, name: "Cape d'apparat", dofusdb_type_id: 247 },
            { value: 13, label: "Epee", dofusdb_type_id: 6 },
            { id: 14, name: "Lance", dofusdb_type_id: 271 },
        ];
        expect(resolveGameplayItemTypeIds(types)).toEqual(["11", "13", "14"]);
    });

    it("ignore les cosmétiques même si le libellé contient un type de jeu", () => {
        const types = [
            { id: 1, name: "Bouclier d'apparat", dofusdb_type_id: 248 },
            { id: 2, name: "Bouclier", dofusdb_type_id: 82 },
        ];
        expect(resolveGameplayItemTypeIds(types)).toEqual(["2"]);
    });

    it("détecte un filtre type déjà présent", () => {
        expect(hasItemTypeFilter({})).toBe(false);
        expect(hasItemTypeFilter({ item_type_id: [] })).toBe(false);
        expect(hasItemTypeFilter({ item_type_id: ["11"] })).toBe(true);
        expect(GAMEPLAY_ITEM_TYPE_LABELS).toContain("Trophée");
        expect(GAMEPLAY_ITEM_TYPE_LABELS).not.toContain("Costume");
    });
});

describe("resolveFilterDefaultValue", () => {
    const options = [
        { value: "10", label: "Costume", dofusdb_type_id: 199 },
        { value: "11", label: "Amulette", dofusdb_type_id: 1 },
        { value: "12", label: "Épée", dofusdb_type_id: 6 },
    ];

    it("préfère defaultValue brut", () => {
        expect(resolveFilterDefaultValue({ defaultValue: ["99"] }, options)).toEqual(["99"]);
    });

    it("résout defaultByLabel et defaultByDofusTypeId", () => {
        expect(
            resolveFilterDefaultValue(
                { defaultByLabel: ["Amulette"], defaultByDofusTypeId: [6] },
                options,
            ),
        ).toEqual(["11", "12"]);
    });

    it("attend les options si la liste est vide", () => {
        expect(
            resolveFilterDefaultValue({ defaultByLabel: ["Amulette"] }, []),
        ).toBeUndefined();
    });
});

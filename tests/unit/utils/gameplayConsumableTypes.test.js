import { describe, expect, it } from "vitest";
import {
    GAMEPLAY_CONSUMABLE_TYPE_LABELS,
    hasConsumableTypeFilter,
    resolveGameplayConsumableTypeIds,
} from "@/Utils/Entity/gameplayConsumableTypes";
import { normalizeItemTypeLabel } from "@/Utils/Entity/gameplayItemTypes";
import { resolveFilterDefaultValue } from "@/Utils/table/resolveFilterDefaultValue";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";

describe("gameplayConsumableTypes", () => {
    it("résout les ids par nom et par dofusdb_type_id", () => {
        const types = [
            { id: 1, name: "Fée d'artifice", dofusdb_type_id: 74 },
            { id: 2, name: "Potion", dofusdb_type_id: 12 },
            { id: 3, name: "Coffre", dofusdb_type_id: 172 },
            { value: 4, label: "Parchemin d’attitude", dofusdb_type_id: 173 },
            { id: 5, name: "Éclats", dofusdb_type_id: 322 },
        ];
        expect(resolveGameplayConsumableTypeIds(types)).toEqual(["2", "4", "5"]);
    });

    it("ignore les types hors liste même si le libellé se ressemble", () => {
        const types = [
            { id: 1, name: "Objet utilisable de Temporis", dofusdb_type_id: 226 },
            { id: 2, name: "Objet utilisable", dofusdb_type_id: 94 },
        ];
        expect(resolveGameplayConsumableTypeIds(types)).toEqual(["2"]);
    });

    it("détecte un filtre type déjà présent", () => {
        expect(hasConsumableTypeFilter({})).toBe(false);
        expect(hasConsumableTypeFilter({ consumable_type_id: [] })).toBe(false);
        expect(hasConsumableTypeFilter({ consumable_type_id: ["2"] })).toBe(true);
        expect(GAMEPLAY_CONSUMABLE_TYPE_LABELS).toContain("Potion");
        expect(GAMEPLAY_CONSUMABLE_TYPE_LABELS).not.toContain("Coffre");
    });
});

describe("normalizeItemTypeLabel", () => {
    it("aligne apostrophes typographiques", () => {
        expect(normalizeItemTypeLabel("Parchemin d’attitude")).toBe(
            normalizeItemTypeLabel("Parchemin d'attitude"),
        );
    });
});

describe("filtre Type consommable", () => {
    it("déclare defaultByLabel / defaultByDofusTypeId", () => {
        const filterable = getConsumableFieldDescriptors().consumable_type?.table?.filterable;
        expect(filterable?.defaultByLabel).toEqual([...GAMEPLAY_CONSUMABLE_TYPE_LABELS]);
        expect(Array.isArray(filterable?.defaultByDofusTypeId)).toBe(true);
        expect(filterable.defaultByDofusTypeId.length).toBe(GAMEPLAY_CONSUMABLE_TYPE_LABELS.length);
    });

    it("résout les défauts via les options API", () => {
        const filterable = getConsumableFieldDescriptors().consumable_type?.table?.filterable;
        const ids = resolveFilterDefaultValue(filterable, [
            { value: "9", label: "Cadeau", dofusdb_type_id: 89 },
            { value: "10", label: "Potion", dofusdb_type_id: 12 },
            { value: "11", label: "Pain", dofusdb_type_id: 33 },
        ]);
        expect(ids).toEqual(["10", "11"]);
    });
});

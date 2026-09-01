import { describe, expect, it } from "vitest";
import {
    characteristicLookupKey,
    sourceGroupsForTableEntityType,
    resolveFilterCharacteristicMeta,
} from "@/Utils/table/filterCharacteristicMeta.js";

describe("characteristicLookupKey", () => {
    it("retire le préfixe creature_", () => {
        expect(characteristicLookupKey("creature_pa")).toBe("pa");
        expect(characteristicLookupKey("creature_level")).toBe("level");
    });

    it("laisse les ids de sorts / capacités inchangés", () => {
        expect(characteristicLookupKey("pa")).toBe("pa");
        expect(characteristicLookupKey("sight_line")).toBe("sight_line");
        expect(characteristicLookupKey("po_editable")).toBe("po_editable");
    });

    it("ignore les valeurs vides", () => {
        expect(characteristicLookupKey("")).toBe("");
        expect(characteristicLookupKey(null)).toBe("");
    });
});

describe("sourceGroupsForTableEntityType", () => {
    it("cible le groupe spell / capability / creature", () => {
        expect(sourceGroupsForTableEntityType("spell")).toEqual(["spell"]);
        expect(sourceGroupsForTableEntityType("spells")).toEqual(["spell"]);
        expect(sourceGroupsForTableEntityType("capability")).toEqual(["capability"]);
        expect(sourceGroupsForTableEntityType("capabilities")).toEqual(["capability"]);
        expect(sourceGroupsForTableEntityType("monster")).toEqual(["creature"]);
        expect(sourceGroupsForTableEntityType("monsters")).toEqual(["creature"]);
        expect(sourceGroupsForTableEntityType("npc")).toEqual(["creature"]);
        expect(sourceGroupsForTableEntityType("npcs")).toEqual(["creature"]);
    });

    it("cible item pour les catalogues objets", () => {
        expect(sourceGroupsForTableEntityType("item")).toEqual(["item"]);
        expect(sourceGroupsForTableEntityType("items")).toEqual(["item"]);
        expect(sourceGroupsForTableEntityType("consumable")).toEqual(["item", "consumable"]);
        expect(sourceGroupsForTableEntityType("consumables")).toEqual(["item", "consumable"]);
        expect(sourceGroupsForTableEntityType("resources")).toEqual(["item", "resource"]);
        expect(sourceGroupsForTableEntityType("breed")).toEqual(["creature"]);
        expect(sourceGroupsForTableEntityType("breeds")).toEqual(["creature"]);
        expect(sourceGroupsForTableEntityType("panoplies")).toEqual(["item", "panoply"]);
    });
});

describe("resolveFilterCharacteristicMeta", () => {
    it("renvoie null sans définition dans le store", () => {
        expect(resolveFilterCharacteristicMeta("types", { entityType: "spell" })).toBeNull();
        expect(resolveFilterCharacteristicMeta("")).toBeNull();
    });
});

import { describe, expect, it } from "vitest";
import {
    entitySupportsQuickCreate,
    getEntityCreateAllowFieldKeys,
    getEntityCreateCoreFieldKeys,
    getEntityCreateLabel,
} from "@/Utils/entity/entity-create-config";

describe("getEntityCreateAllowFieldKeys", () => {
    it("retourne les clés spell", () => {
        expect(getEntityCreateAllowFieldKeys("spells")).toEqual(
            expect.arrayContaining(["dofusdb_id", "auto_update"]),
        );
    });

    it("retourne item_type_id pour items", () => {
        expect(getEntityCreateAllowFieldKeys("item")).toContain("item_type_id");
    });

    it("fallback dofusdb pour type inconnu", () => {
        expect(getEntityCreateAllowFieldKeys("unknown-type")).toEqual([
            "dofusdb_id",
            "auto_update",
        ]);
    });
});

describe("getEntityCreateCoreFieldKeys", () => {
    it("inclut nom, description et rôle pour un PNJ", () => {
        expect(getEntityCreateCoreFieldKeys("npcs")).toEqual([
            "name",
            "description",
            "level",
            "npc_role",
            "location",
        ]);
    });

    it("normalise le singulier spell", () => {
        expect(getEntityCreateCoreFieldKeys("spell")).toEqual(
            expect.arrayContaining(["name", "description", "pa"]),
        );
    });

    it("fallback nom + description pour un type inconnu", () => {
        expect(getEntityCreateCoreFieldKeys("unknown-type")).toEqual(["name", "description"]);
    });
});

describe("getEntityCreateLabel", () => {
    it("libellé français pour un PNJ", () => {
        expect(getEntityCreateLabel("npc")).toBe("un PNJ");
    });
});

describe("entitySupportsQuickCreate", () => {
    it("autorise sorts et PNJ", () => {
        expect(entitySupportsQuickCreate("spells")).toBe(true);
        expect(entitySupportsQuickCreate("npc")).toBe(true);
    });

    it("refuse les types dont le store est encore un stub", () => {
        expect(entitySupportsQuickCreate("panoplies")).toBe(false);
        expect(entitySupportsQuickCreate("shop")).toBe(false);
        expect(entitySupportsQuickCreate("scenarios")).toBe(false);
    });
});

import { describe, expect, it } from "vitest";
import { getEntityCreateAllowFieldKeys } from "@/Utils/entity/entity-create-config";

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

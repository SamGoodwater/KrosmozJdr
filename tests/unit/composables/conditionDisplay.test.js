import { describe, expect, it } from "vitest";
import {
    CONDITION_CATALOG_STATE_DEFAULT,
    listActiveMechanicalFlags,
    readConditionFlag,
} from "@/Composables/condition/conditionDisplay";

describe("conditionDisplay mechanical flags", () => {
    it("précoché seulement Jouable dans le catalogue", () => {
        expect(CONDITION_CATALOG_STATE_DEFAULT).toEqual(["playable"]);
        expect(CONDITION_CATALOG_STATE_DEFAULT).not.toContain("raw");
    });

    it("lit un flag sur instance ou payload", () => {
        expect(readConditionFlag({ cant_be_moved: true }, "cant_be_moved")).toBe(true);
        expect(readConditionFlag({ _data: { invulnerable: true } }, "invulnerable")).toBe(true);
        expect(readConditionFlag({}, "cant_be_moved")).toBe(false);
    });

    it("liste seulement les flags actifs", () => {
        const flags = listActiveMechanicalFlags({
            cant_be_moved: true,
            invulnerable: false,
            prevents_spell_cast: true,
        });
        expect(flags.map((f) => f.key)).toEqual(["prevents_spell_cast", "cant_be_moved"]);
        expect(flags[1].label).toBe("Ne peut pas être déplacé");
    });
});

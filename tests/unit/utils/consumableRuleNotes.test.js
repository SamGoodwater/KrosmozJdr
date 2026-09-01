/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import {
    CONSUMABLE_STACK_NOTE,
    LEARN_SCROLL_NOTE,
    consumableRuleNotes,
} from "@/Utils/Entity/consumableRuleNotes";

describe("consumableRuleNotes", () => {
    it("ne renvoie rien sans type", () => {
        expect(consumableRuleNotes(null)).toEqual([]);
        expect(consumableRuleNotes({})).toEqual([]);
        expect(consumableRuleNotes({ consumableType: { name: "Certificat" } })).toEqual([]);
    });

    it("buff : potion, nourriture, pain (accents ignorés)", () => {
        expect(consumableRuleNotes({ consumableType: { name: "Potion" } })).toEqual([
            CONSUMABLE_STACK_NOTE,
        ]);
        expect(consumableRuleNotes({ consumable_type: { name: "Nourriture boost" } })).toEqual([
            CONSUMABLE_STACK_NOTE,
        ]);
        expect(consumableRuleNotes({ _data: { consumableType: { name: "Bière" } } })).toEqual([
            CONSUMABLE_STACK_NOTE,
        ]);
    });

    it("parchemin de sortilège : détruit si le sort réussit, pas de note de cumul", () => {
        expect(
            consumableRuleNotes({ consumableType: { name: "Parchemin de sortilège" } }),
        ).toEqual([LEARN_SCROLL_NOTE]);
        expect(
            consumableRuleNotes({ consumableType: { name: "Parchemin de sortilege" } }),
        ).toEqual([LEARN_SCROLL_NOTE]);
    });
});

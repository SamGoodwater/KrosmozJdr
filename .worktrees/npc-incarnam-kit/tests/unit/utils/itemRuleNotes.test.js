/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import { MOUNT_RULE_NOTE, itemRuleNotes } from "@/Utils/Entity/itemRuleNotes";

describe("itemRuleNotes", () => {
    it("ne renvoie rien hors monture", () => {
        expect(itemRuleNotes(null)).toEqual([]);
        expect(itemRuleNotes({})).toEqual([]);
        expect(itemRuleNotes({ itemType: { name: "Épée" } })).toEqual([]);
    });

    it("monture et dragodinde", () => {
        expect(itemRuleNotes({ itemType: { name: "Monture" } })).toEqual([MOUNT_RULE_NOTE]);
        expect(itemRuleNotes({ item_type: { name: "Dragodinde" } })).toEqual([MOUNT_RULE_NOTE]);
        expect(itemRuleNotes({ _data: { itemType: "Monture volante" } })).toEqual([MOUNT_RULE_NOTE]);
    });
});

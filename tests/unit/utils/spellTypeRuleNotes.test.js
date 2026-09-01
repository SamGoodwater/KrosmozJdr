/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import {
    GLYPH_DISPEL_NOTE,
    GLYPH_RULE_NOTE,
    HEAL_COMBAT_PA_NOTE,
    INVOCATION_RULE_NOTE,
    LIFE_STEAL_RULE_NOTE,
    REACTION_RULE_NOTE,
    SHIELD_RULE_NOTE,
    TEMP_HP_RULE_NOTE,
    TRAP_GLYPH_CAP_NOTE,
    TRAP_RULE_NOTE,
    WILLING_TARGET_RULE_NOTE,
    spellTypeRuleNotes,
} from "@/Utils/Entity/spellTypeRuleNotes";

describe("spellTypeRuleNotes", () => {
    it("ne renvoie rien sans signal", () => {
        expect(spellTypeRuleNotes(null)).toEqual([]);
        expect(spellTypeRuleNotes({})).toEqual([]);
        expect(spellTypeRuleNotes({ spellTypes: [{ name: "Offensif" }] })).toEqual([]);
    });

    it("soin : type ou effet, pas le vol de vie seul", () => {
        expect(spellTypeRuleNotes({ spellTypes: [{ name: "Soin" }] })).toEqual([HEAL_COMBAT_PA_NOTE]);
        expect(spellTypeRuleNotes({ spell_types: [{ name: "Heal" }] })).toEqual([HEAL_COMBAT_PA_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ action_slug: "soigner" }],
            }),
        ).toEqual([HEAL_COMBAT_PA_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ characteristic: "soin_spell" }],
            }),
        ).toEqual([HEAL_COMBAT_PA_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ life_steal_formula: "50%" }],
            }),
        ).toEqual([LIFE_STEAL_RULE_NOTE]);
    });

    it("invocation : type ou action", () => {
        expect(spellTypeRuleNotes({ spellTypes: [{ name: "Invocation" }] })).toEqual([
            INVOCATION_RULE_NOTE,
        ]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ action_slug: "invoquer" }],
            }),
        ).toEqual([INVOCATION_RULE_NOTE]);
    });

    it("piège et glyphe via target_type (chip ou sort)", () => {
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ target_type: "trap" }],
            }),
        ).toEqual([TRAP_RULE_NOTE, TRAP_GLYPH_CAP_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ target_type: "glyph" }],
            }),
        ).toEqual([GLYPH_RULE_NOTE, GLYPH_DISPEL_NOTE, TRAP_GLYPH_CAP_NOTE]);
        expect(spellTypeRuleNotes({ target_type: "piège" })).toEqual([
            TRAP_RULE_NOTE,
            TRAP_GLYPH_CAP_NOTE,
        ]);
    });

    it("bouclier : slug protéger / characteristic bouclier_spell", () => {
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ action_slug: "protéger" }],
            }),
        ).toEqual([SHIELD_RULE_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ characteristic: "bouclier_spell" }],
            }),
        ).toEqual([SHIELD_RULE_NOTE]);
    });

    it("cible consentante, réaction, PV temporaires", () => {
        expect(spellTypeRuleNotes({ auto_success_if_willing_target: true })).toEqual([
            WILLING_TARGET_RULE_NOTE,
        ]);
        expect(spellTypeRuleNotes({ allowsReaction: 1 })).toEqual([REACTION_RULE_NOTE]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ action_slug: "donner-pv-temporaires" }],
            }),
        ).toEqual([TEMP_HP_RULE_NOTE]);
    });

    it("cumul + payload _data (modèle Spell)", () => {
        const notes = spellTypeRuleNotes({
            _data: {
                spellTypes: [{ name: "Soin" }],
                allows_reaction: true,
                auto_success_if_willing_target: true,
                effect_usages_chips: [
                    { action_slug: "soigner", life_steal_formula: "1d4" },
                    { action_slug: "donner-pv-temporaires", target_type: "glyph" },
                ],
            },
        });
        expect(notes).toEqual([
            HEAL_COMBAT_PA_NOTE,
            GLYPH_RULE_NOTE,
            GLYPH_DISPEL_NOTE,
            TRAP_GLYPH_CAP_NOTE,
            LIFE_STEAL_RULE_NOTE,
            WILLING_TARGET_RULE_NOTE,
            REACTION_RULE_NOTE,
            TEMP_HP_RULE_NOTE,
        ]);
    });
});

/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import {
    GLYPH_DISPEL_NOTE,
    GLYPH_RULE_NOTE,
    HEAL_COMBAT_PA_NOTE,
    INVOCATION_RULE_NOTE,
    TRAP_GLYPH_CAP_NOTE,
    TRAP_RULE_NOTE,
    spellHasGlyphEffect,
    spellHasHealEffect,
    spellHasHealType,
    spellHasInvocationType,
    spellHasTrapEffect,
    spellHealCombatPaNote,
    spellTypeRuleNotes,
} from "@/Utils/Entity/spellTypeRuleNotes";

describe("spellHealCombatPaNote", () => {
    it("détecte le type de sort Soin même sans effet soigner", () => {
        const entity = { spellTypes: [{ id: 3, name: "Soin" }] };
        expect(spellHasHealType(entity)).toBe(true);
        expect(spellHealCombatPaNote(entity)).toBe(HEAL_COMBAT_PA_NOTE);
    });

    it("accepte spell_types et _data, ignore Offensif", () => {
        expect(spellHasHealType({ spell_types: [{ name: "soin" }] })).toBe(true);
        expect(
            spellHasHealType({ _data: { spellTypes: [{ name: "Soin" }] } }),
        ).toBe(true);
        expect(spellHasHealType({ spellTypes: [{ name: "Offensif" }] })).toBe(false);
        expect(spellHealCombatPaNote({ spellTypes: [{ name: "Offensif" }] })).toBeNull();
    });

    it("détecte un chip soigner", () => {
        const entity = { effect_usages_chips: [{ action_slug: "soigner" }] };
        expect(spellHasHealEffect(entity)).toBe(true);
        expect(spellHealCombatPaNote(entity)).toBe(HEAL_COMBAT_PA_NOTE);
    });

    it("détecte heal / heal_percent et soin_spell", () => {
        expect(spellHasHealEffect({ effect_usages_chips: [{ action_slug: "heal" }] })).toBe(true);
        expect(spellHasHealEffect({ effect_usages_chips: [{ action_slug: "heal_percent" }] })).toBe(
            true,
        );
        expect(spellHasHealEffect({ effect_usages_chips: [{ characteristic: "soin_spell" }] })).toBe(
            true,
        );
    });

    it("ignore le vol de vie et les sorts sans soin", () => {
        expect(
            spellHasHealEffect({
                effect_usages_chips: [{ action_slug: "frapper", life_steal_formula: "50%" }],
            }),
        ).toBe(false);
        expect(spellHasHealEffect({ effect_usages_chips: [{ action_slug: "frapper" }] })).toBe(false);
        expect(spellHealCombatPaNote({ name: "Flèche magique" })).toBeNull();
        expect(spellHasHealEffect(null)).toBe(false);
    });

    it("détecte un soin dans effects_definitions (fiche Full)", () => {
        const entity = {
            effects_definitions: [
                {
                    degrees: [
                        {
                            rows: [{ sub_effect: { slug: "soigner", type_slug: "soigner" } }],
                        },
                    ],
                },
            ],
        };
        expect(spellHasHealEffect(entity)).toBe(true);
    });

    it("lit _data d’une instance Spell", () => {
        const entity = { _data: { effect_usages_chips: [{ action_slug: "soigner" }] } };
        expect(spellHasHealEffect(entity)).toBe(true);
    });

    it("détecte le type Invocation et l’effet invoquer", () => {
        expect(spellHasInvocationType({ spellTypes: [{ name: "Invocation" }] })).toBe(true);
        expect(spellTypeRuleNotes({ spellTypes: [{ name: "Invocation" }] })).toEqual([
            INVOCATION_RULE_NOTE,
        ]);
        expect(
            spellTypeRuleNotes({ effect_usages_chips: [{ action_slug: "invoquer" }] }),
        ).toEqual([INVOCATION_RULE_NOTE]);
        expect(spellTypeRuleNotes({ spellTypes: [{ name: "Offensif" }] })).toEqual([]);
    });

    it("cumule les notes Soin et Invocation", () => {
        expect(
            spellTypeRuleNotes({
                spellTypes: [{ name: "Soin" }, { name: "Invocation" }],
            }),
        ).toEqual([HEAL_COMBAT_PA_NOTE, INVOCATION_RULE_NOTE]);
    });

    it("détecte un effet piège ou glyphe", () => {
        expect(spellHasTrapEffect({ effect_usages_chips: [{ target_type: "trap" }] })).toBe(true);
        expect(spellHasGlyphEffect({ effect_usages_chips: [{ target_type: "glyph" }] })).toBe(true);
        expect(spellTypeRuleNotes({ effect_usages_chips: [{ target_type: "trap" }] })).toEqual([
            TRAP_RULE_NOTE,
            TRAP_GLYPH_CAP_NOTE,
        ]);
        expect(
            spellTypeRuleNotes({
                effects_definitions: [{ target_type: "glyph" }],
            }),
        ).toEqual([GLYPH_RULE_NOTE, GLYPH_DISPEL_NOTE, TRAP_GLYPH_CAP_NOTE]);
        expect(spellTypeRuleNotes({ effect_usages_chips: [{ target_type: "direct" }] })).toEqual([]);
        expect(
            spellTypeRuleNotes({
                effect_usages_chips: [{ target_type: "trap" }, { target_type: "glyph" }],
            }),
        ).toEqual([
            TRAP_RULE_NOTE,
            GLYPH_RULE_NOTE,
            GLYPH_DISPEL_NOTE,
            TRAP_GLYPH_CAP_NOTE,
        ]);
    });
});

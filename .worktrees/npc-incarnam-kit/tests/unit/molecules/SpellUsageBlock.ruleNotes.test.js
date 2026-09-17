import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import SpellUsageBlock from "@/Pages/Molecules/entity/spell/SpellUsageBlock.vue";
import { HEAL_COMBAT_PA_NOTE, REACTION_RULE_NOTE } from "@/Utils/Entity/spellTypeRuleNotes";

function mountBlock(props = {}) {
    return mount(SpellUsageBlock, {
        props: {
            entity: {
                spellTypes: [{ name: "Soin" }],
                allows_reaction: true,
            },
            canShowField: () => true,
            ...props,
        },
        global: {
            stubs: {
                SpellMinimalUsageMetaRow: true,
                CellRenderer: true,
            },
        },
    });
}

describe("SpellUsageBlock notes de règles", () => {
    it("affiche les notes en parts=meta (Minimal / Line / Full utilisation)", () => {
        const wrapper = mountBlock({ parts: "meta" });
        const notes = wrapper.get("[data-cy=spell-rule-notes]");
        expect(notes.text()).toContain(HEAL_COMBAT_PA_NOTE);
        expect(notes.text()).toContain(REACTION_RULE_NOTE);
    });

    it("affiche aussi les notes en parts=effects (Full effets)", () => {
        const wrapper = mountBlock({ parts: "effects" });
        expect(wrapper.get("[data-cy=spell-rule-notes]").text()).toContain(HEAL_COMBAT_PA_NOTE);
    });

    it("coupe les notes quand showRuleNotes est false (effets Minimal / Line)", () => {
        const wrapper = mountBlock({ parts: "effects", showRuleNotes: false });
        expect(wrapper.find("[data-cy=spell-rule-notes]").exists()).toBe(false);
    });
});

import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import PanoplyBonusTiers from "@/Pages/Molecules/entity/panoply/PanoplyBonusTiers.vue";

const bonus = {
    4: { life_points_max: 1 },
    8: { strength: 1, intelligence: 1 },
};

describe("PanoplyBonusTiers", () => {
    it("affiche chaque palier sur sa propre ligne, sans onglets", () => {
        const wrapper = mount(PanoplyBonusTiers, {
            props: { bonus, layout: "stack" },
            global: {
                stubs: {
                    CharacteristicChip: true,
                    CharacteristicInlineGroup: true,
                    Badge: { props: ["content", "title"], template: "<span>{{ content }}</span>" },
                },
            },
        });

        const rows = wrapper.findAll('[data-cy="panoply-bonus-tier"]');
        expect(rows).toHaveLength(2);
        expect(rows[0].attributes("data-piece-count")).toBe("4");
        expect(rows[1].attributes("data-piece-count")).toBe("8");
        expect(wrapper.find('[role="tablist"]').exists()).toBe(false);
        expect(wrapper.text()).toContain("4");
        expect(wrapper.text()).toContain("8");
    });

    it("en colonnes, aligne un palier par colonne", () => {
        const wrapper = mount(PanoplyBonusTiers, {
            props: { bonus, layout: "columns", labelMode: "full" },
            global: {
                stubs: {
                    CharacteristicChip: true,
                    CharacteristicInlineGroup: true,
                    Badge: { props: ["content"], template: "<span>{{ content }}</span>" },
                },
            },
        });

        expect(wrapper.find('[data-cy="panoply-bonus-tiers"]').classes()).toContain("flex-wrap");
        expect(wrapper.findAll('[data-cy="panoply-bonus-tier"]')).toHaveLength(2);
    });
});

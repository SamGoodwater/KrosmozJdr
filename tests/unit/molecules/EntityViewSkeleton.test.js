import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import EntityViewSkeleton from "@/Pages/Molecules/entity/shared/EntityViewSkeleton.vue";
import SectionContentSkeleton from "@/Pages/Molecules/section/SectionContentSkeleton.vue";

describe("EntityViewSkeleton", () => {
    it("affiche plusieurs cartes image + texte en variant compact", () => {
        const wrapper = mount(EntityViewSkeleton, {
            props: { variant: "compact", count: 6 },
        });

        const cards = wrapper.findAll('[aria-hidden="true"]');
        expect(cards.length).toBe(6);
        expect(wrapper.classes().join(" ")).toContain("flex-wrap");
        expect(wrapper.findAll(".skeleton").length).toBeGreaterThan(6);
    });

    it("affiche une pile de lignes en variant line", () => {
        const wrapper = mount(EntityViewSkeleton, {
            props: { variant: "line", count: 4 },
        });

        expect(wrapper.findAll('[aria-hidden="true"]').length).toBe(4);
        expect(wrapper.classes().join(" ")).toContain("flex-col");
    });
});

describe("SectionContentSkeleton", () => {
    it("utilise des cartes d’entités pour entity_table", () => {
        const wrapper = mount(SectionContentSkeleton, {
            props: { template: "entity_table", showHeader: false },
        });

        expect(wrapper.findComponent(EntityViewSkeleton).exists()).toBe(true);
    });

    it("utilise une grille de cellules pour un template tableau", () => {
        const wrapper = mount(SectionContentSkeleton, {
            props: { template: "forgemagie_rune_table", showHeader: false },
        });

        expect(wrapper.findComponent(EntityViewSkeleton).exists()).toBe(false);
        expect(wrapper.findAll(".skeleton").length).toBeGreaterThan(10);
    });

    it("utilise des lignes de texte pour une section texte", () => {
        const wrapper = mount(SectionContentSkeleton, {
            props: { template: "text", title: "Intro" },
        });

        expect(wrapper.text()).toContain("Chargement");
        expect(wrapper.findAll(".skeleton").length).toBeGreaterThan(3);
    });
});

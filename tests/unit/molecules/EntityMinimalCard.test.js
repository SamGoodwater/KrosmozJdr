import { describe, it, expect, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { nextTick } from "vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";

function mountCard(props = {}) {
    return mount(EntityMinimalCard, {
        props: {
            displayMode: "hover",
            ...props,
        },
        slots: {
            compact: '<div data-test="compact">compact</div>',
            expanded: '<div data-test="expanded">expanded</div>',
        },
    });
}

describe("EntityMinimalCard", () => {
    beforeEach(() => {
        localStorage.clear();
    });

    it("ouvre au survol et se referme à la sortie", async () => {
        const wrapper = mountCard();

        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);

        await wrapper.trigger("mouseenter");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        await wrapper.trigger("mouseleave");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);
    });

    it("reste ouvert après clic jusqu'au clic extérieur", async () => {
        const wrapper = mountCard();

        await wrapper.trigger("mouseenter");
        await wrapper.trigger("click");
        await wrapper.trigger("mouseleave");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        document.dispatchEvent(new MouseEvent("pointerdown", { bubbles: true }));
        await nextTick();
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);
    });

    it("se déverrouille aussi avec Escape", async () => {
        const wrapper = mountCard();

        await wrapper.trigger("mouseenter");
        await wrapper.trigger("click");
        await wrapper.trigger("mouseleave");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        document.dispatchEvent(new KeyboardEvent("keydown", { key: "Escape", bubbles: true }));
        await nextTick();
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);
    });

    it("émet open-quick-view au double-clic", async () => {
        const wrapper = mountCard();
        await wrapper.trigger("dblclick");
        expect(wrapper.emitted("open-quick-view")?.length).toBe(1);
    });

    it("en mode extended n'affiche que la carte expanded, sans overlay ni compact", () => {
        const wrapper = mountCard({ displayMode: "extended" });

        expect(wrapper.find('[data-test="compact"]').exists()).toBe(false);
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);
        expect(wrapper.find(".entity-minimal-card__expanded--overlay").exists()).toBe(false);
        expect(wrapper.find(".entity-minimal-card--extended").exists()).toBe(true);
    });
});

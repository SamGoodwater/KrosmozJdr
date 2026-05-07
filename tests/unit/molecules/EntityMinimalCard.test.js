import { describe, it, expect, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { nextTick } from "vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import { toggleEntityPin } from "@/Composables/entity/usePinnedEntityIds";

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

    it("reste étendu tant qu'il est épinglé, même au clic extérieur", async () => {
        toggleEntityPin("spells", 42);
        const wrapper = mountCard({ pinnedEntityType: "spells", pinnedEntityId: 42 });

        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        document.dispatchEvent(new MouseEvent("pointerdown", { bubbles: true }));
        document.dispatchEvent(new KeyboardEvent("keydown", { key: "Escape", bubbles: true }));
        await nextTick();
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);
    });

    it("se replie immédiatement après désépinglage", async () => {
        toggleEntityPin("spells", 77);
        const wrapper = mountCard({ pinnedEntityType: "spells", pinnedEntityId: 77 });

        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        toggleEntityPin("spells", 77);
        await nextTick();
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);
    });
});

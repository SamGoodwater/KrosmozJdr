import { describe, it, expect, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import { nextTick } from "vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import { useEntityMinimalCardOverlayHold } from "@/Composables/overlay/entityMinimalCardOverlayHold";

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

    it("applique surfaceStyle sur compact et expanded", async () => {
        const wrapper = mountCard({
            surfaceStyle: { "--element-border-color": "var(--color-error-600)" },
        });
        const compact = wrapper.find(".entity-minimal-card__compact");
        expect(compact.attributes("style")).toContain("--element-border-color: var(--color-error-600)");
        expect(compact.classes()).toContain("entity-element-ring");

        await wrapper.trigger("mouseenter");
        const expanded = wrapper.find(".entity-minimal-card__expanded");
        expect(expanded.attributes("style")).toContain("--element-border-color: var(--color-error-600)");
        expect(expanded.classes()).toContain("entity-element-ring");
    });

    it("reste déployé tant qu’un overlay issu de la carte est ouvert", async () => {
        const HoldChild = {
            setup() {
                const hold = useEntityMinimalCardOverlayHold();
                return {
                    openOverlay() {
                        hold?.acquire();
                    },
                    closeOverlay() {
                        hold?.release();
                    },
                };
            },
            template: "<span />",
        };

        const wrapper = mount({
            components: { EntityMinimalCard, HoldChild },
            template: `
                <EntityMinimalCard display-mode="hover">
                    <template #compact><div data-test="compact">compact</div></template>
                    <template #expanded>
                        <div data-test="expanded"><HoldChild ref="hold" /></div>
                    </template>
                </EntityMinimalCard>
            `,
        });

        await wrapper.find(".entity-minimal-card").trigger("mouseenter");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        wrapper.findComponent(HoldChild).vm.openOverlay();
        await nextTick();
        await wrapper.find(".entity-minimal-card").trigger("mouseleave");
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(true);

        wrapper.findComponent(HoldChild).vm.closeOverlay();
        await nextTick();
        expect(wrapper.find('[data-test="expanded"]').exists()).toBe(false);
    });
});

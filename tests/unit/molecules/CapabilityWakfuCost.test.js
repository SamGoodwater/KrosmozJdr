import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import CapabilityWakfuCost from "@/Pages/Molecules/entity/capability/CapabilityWakfuCost.vue";

const TooltipStub = {
    template: '<div><div data-cy="tooltip-content"><slot name="content" /></div><slot /></div>',
};

function mountCost(value) {
    return mount(CapabilityWakfuCost, {
        props: { value },
        global: { stubs: { Tooltip: TooltipStub, Image: true } },
    });
}

describe("CapabilityWakfuCost", () => {
    it("affiche « 2 PA » avec la réserve de Wakfu et l'explique au survol", () => {
        const wrapper = mountCost("2");
        const badge = wrapper.get('[aria-label="2 PA depuis la réserve de Wakfu"]');
        expect(badge.text().replace(/\s+/g, "")).toBe("2PA");
        expect(wrapper.get("[data-cy=tooltip-content]").text()).toContain("2 PA depuis la réserve de Wakfu");
        expect(wrapper.find("[data-testid=wakfu-reserve-icon]").exists()).toBe(true);
    });

    it("affiche « 0 PA » sans icône de réserve pour un coût nul ou vide", () => {
        for (const value of ["0", null, ""]) {
            const wrapper = mountCost(value);
            const badge = wrapper.get('[aria-label="0 PA : sans coût"]');
            expect(badge.text().replace(/\s+/g, "")).toBe("0PA");
            expect(wrapper.find("[data-testid=wakfu-reserve-icon]").exists()).toBe(false);
        }
    });
});

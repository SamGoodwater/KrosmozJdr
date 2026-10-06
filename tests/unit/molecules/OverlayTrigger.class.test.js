import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";

describe("OverlayTrigger", () => {
    it("pose une class héritée sur le nœud de référence, pas comme attribut orphelin", () => {
        const warn = vi.spyOn(console, "warn").mockImplementation(() => {});
        const wrapper = mount(OverlayTrigger, {
            attrs: { class: "min-w-0 flex-1" },
            props: { content: "Aperçu", trigger: "hover" },
            slots: { default: "<span>Sram</span>" },
        });

        const trigger = wrapper.get("span");
        expect(trigger.classes()).toContain("flex-1");
        expect(trigger.classes()).toContain("min-w-0");
        expect(warn.mock.calls.some((args) => String(args[0]).includes("Extraneous non-props"))).toBe(false);

        warn.mockRestore();
    });
});

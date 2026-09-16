import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import ScrappingPropertyCheckboxes from "@/Pages/Molecules/data-input/ScrappingPropertyCheckboxes.vue";

describe("ScrappingPropertyCheckboxes", () => {
    it("affiche image et permet tout / rien", async () => {
        const wrapper = mount(ScrappingPropertyCheckboxes, {
            props: {
                keys: ["name", "image"],
                selected: ["name", "image"],
            },
        });

        expect(wrapper.text()).toContain("Image");
        expect(wrapper.text()).toContain("Nom");
        expect(wrapper.text()).toContain("Tout");
        expect(wrapper.text()).toContain("Rien");

        const noneBtn = wrapper.findAll("button").find((b) => b.text() === "Rien");
        await noneBtn.trigger("click");
        expect(wrapper.emitted("update:selected")?.[0]?.[0]).toEqual([]);

        const allBtn = wrapper.findAll("button").find((b) => b.text() === "Tout");
        await allBtn.trigger("click");
        expect(wrapper.emitted("update:selected")?.[1]?.[0]).toEqual(["name", "image"]);
    });
});

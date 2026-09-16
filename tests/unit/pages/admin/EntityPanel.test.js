import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import EntityPanel from "@/Pages/Admin/Content/IaGeneration/EntityPanel.vue";

const modelValue = {
    has_dofus_source: true,
    frozen_fields: "*",
    writable_fields: [],
    frozen_characteristics: "*",
    writable_characteristics: [],
    example_ids: ["jdr:item:cape-piou"],
    few_shot_panoplies: ["Panoplie du Bouftou"],
    task_prompt: "",
};

describe("EntityPanel", () => {
    it("branche le sélecteur d’étalons et le filtre playable, sans champ texte d’ids", () => {
        const wrapper = mount(EntityPanel, {
            props: {
                entity: "item",
                label: "Objets",
                modelValue,
                characteristicOptions: [{ key: "intelligence_object", name: "Intelligence" }],
            },
            global: {
                stubs: {
                    CheckboxField: { template: "<div />", props: ["modelValue", "label"] },
                    TextareaField: {
                        props: ["modelValue", "label"],
                        template: "<label>{{ label }}<textarea /></label>",
                    },
                    InputField: { template: "<div />", props: ["modelValue", "label"] },
                    ExamplePicker: {
                        props: ["entity", "modelValue"],
                        template:
                            '<div class="example-picker-stub" :data-entity="entity">picker</div>',
                    },
                },
            },
        });

        expect(wrapper.get("[data-testid='ia-entity-panel']").exists()).toBe(true);
        expect(wrapper.text()).not.toContain("Clés d’exception");
        expect(wrapper.text()).toContain("Caracs que l’IA peut modifier");
        const pickers = wrapper.findAll(".example-picker-stub");
        expect(pickers).toHaveLength(2);
        expect(pickers[0].attributes("data-entity")).toBe("item");
        expect(pickers[1].attributes("data-entity")).toBe("panoply");
        expect(wrapper.get("[data-testid='ia-example-panoplies']").exists()).toBe(true);
        expect(wrapper.find("input[type='text']").exists()).toBe(false);
    });
});

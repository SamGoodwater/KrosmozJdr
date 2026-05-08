import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { nextTick } from "vue";
import EntityActionMenuList from "@/Pages/Molecules/entity/EntityActionMenuList.vue";

vi.mock("@/Composables/utils/useUxFeedback", () => ({
    useUxFeedback: () => ({ notifySuccess: vi.fn() }),
}));

const actions = [
    { key: "copy-link", label: "Copier", icon: "fa-solid fa-link", group: "tools" },
    { key: "delete", label: "Supprimer", icon: "fa-solid fa-trash", group: "destructive", variant: "error" },
];

describe("EntityActionMenuList", () => {
    it("affiche les labels dans le menu et émet l'action", async () => {
        const wrapper = mount(EntityActionMenuList, {
            props: {
                entityType: "items",
                entity: { id: 1, name: "Épée" },
                actions,
                groupedActions: { tools: [actions[0]], destructive: [actions[1]] },
                entityName: "Épée",
            },
            global: {
                stubs: {
                    Icon: { template: "<span />" },
                },
            },
        });

        expect(wrapper.text()).toContain("Copier");
        expect(wrapper.text()).toContain("Supprimer");
        await wrapper.findAll("button")[0].trigger("click");
        expect(wrapper.emitted("action")?.[0]).toEqual(["copy-link"]);
    });

    it("supporte la navigation clavier du menu", async () => {
        const wrapper = mount(EntityActionMenuList, {
            attachTo: document.body,
            props: {
                actions,
                groupedActions: { tools: [actions[0]], destructive: [actions[1]] },
                focusOnMount: true,
            },
            global: {
                stubs: {
                    Icon: { template: "<span />" },
                },
            },
        });

        await nextTick();
        const buttons = wrapper.findAll("button");
        expect(document.activeElement).toBe(buttons[0].element);

        await wrapper.find("ul").trigger("keydown", { key: "End" });
        expect(document.activeElement).toBe(buttons[1].element);

        await wrapper.find("ul").trigger("keydown", { key: "Home" });
        expect(document.activeElement).toBe(buttons[0].element);

        await wrapper.find("ul").trigger("keydown", { key: "Escape" });
        expect(wrapper.emitted("close")).toBeTruthy();

        wrapper.unmount();
    });
});

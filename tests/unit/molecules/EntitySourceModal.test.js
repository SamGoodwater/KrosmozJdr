import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import EntitySourceModal from "@/Pages/Molecules/entity/EntitySourceModal.vue";

vi.mock("@/Composables/permissions/usePermissions", () => ({
    usePermissions: () => ({ isAdmin: { value: true } }),
}));

const stubs = {
    Modal: {
        template: "<div class='modal-stub'><slot name='header' /><slot /></div>",
        props: ["open", "size", "placement"],
    },
    Btn: { template: "<button type='button' v-bind='$attrs'><slot /></button>" },
    Icon: { template: "<span />" },
    TextareaField: {
        props: ["modelValue", "label"],
        template: "<textarea :value='modelValue' @input=\"$emit('update:modelValue', $event.target.value)\" />",
    },
};

describe("EntitySourceModal", () => {
    it("affiche les volets DofusDB et Conversion IA puis émet convert", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                entityLabel: "Pression",
                aiActionLabel: "Sort (effets)",
                aiEstimate: { formatted: "~ 0,05 $" },
                aiUsage: { remaining_credits_usd: 12, remaining_hint: "≈ 120 rencontres ou 34 PNJ" },
            },
            global: { stubs },
        });

        expect(wrapper.text()).toContain("DofusDB");
        expect(wrapper.text()).toContain("Conversion IA");
        expect(wrapper.text()).toContain("Pression");

        const tabs = wrapper.findAll("button.tab");
        expect(tabs).toHaveLength(2);
        await tabs[1].trigger("click");
        expect(wrapper.text()).toContain("Sort (effets)");
        expect(wrapper.text()).toContain("auto");
        expect(wrapper.text()).toContain("~ 0,05 $");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("Crédit restant");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("120 rencontres");

        const primaryButtons = wrapper.findAll("button").filter((btn) => btn.text().includes("Lancer la conversion"));
        expect(primaryButtons.length).toBe(1);
        await primaryButtons[0].trigger("click");
        expect(wrapper.emitted("convert")).toBeTruthy();
    });

    it("ouvre directement le volet IA pour un PNJ sans DofusDB", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                entityLabel: "Ganymède",
                aiActionLabel: "PNJ (fiche complète)",
            },
            global: { stubs },
        });

        expect(wrapper.text()).not.toContain("DofusDB");
        expect(wrapper.text()).toContain("Ganymède");
        expect(wrapper.text()).toContain("PNJ (fiche complète)");
        expect(wrapper.text()).toContain("Lancer la conversion");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("Solde");
    });

    it("affiche les tokens locaux quand le crédit fournisseur est absent", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                entityLabel: "Barricade",
                aiEstimate: { formatted: "~ 0,12 $" },
                aiUsage: {
                    local_input_tokens: 120,
                    local_output_tokens: 40,
                    local_runs: 1,
                    remaining_credits_usd: null,
                },
            },
            global: { stubs },
        });

        expect(wrapper.text()).toContain("~ 0,12 $");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("Tokens ce mois");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("120");
    });
});

import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import EntityUpdateDiffView from "@/Pages/Molecules/entity/EntityUpdateDiffView.vue";

const stubs = {
    Btn: { template: "<button type='button' v-bind='$attrs'><slot /></button>" },
    Icon: { template: "<span />" },
};

const diff = {
    source: "ia",
    changed_count: 1,
    snapshot_id: "11111111-1111-1111-1111-111111111111",
    before: { preview: { name: "Cape brute", state: "raw" } },
    after: { preview: { name: "Cape auto", state: "auto" } },
    fields: [
        { key: "state", label: "État", before: "raw", after: "auto", changed: true },
        { key: "name", label: "Nom", before: "Cape brute", after: "Cape brute", changed: false },
    ],
};

describe("EntityUpdateDiffView", () => {
    it("met en avant les champs changés et émet enregistrer / rétablir", async () => {
        const wrapper = mount(EntityUpdateDiffView, {
            props: { diff },
            global: { stubs },
        });

        expect(wrapper.get("[data-testid='entity-update-diff']").text()).toContain("Version initiale");
        expect(wrapper.text()).toContain("Cape brute");
        expect(wrapper.text()).toContain("Cape auto");
        expect(wrapper.text()).toContain("État");
        expect(wrapper.text()).not.toContain("Nom");
        expect(wrapper.find("[data-changed='1']").exists()).toBe(true);

        await wrapper.get("[data-testid='entity-update-diff-save']").trigger("click");
        await wrapper.get("[data-testid='entity-update-diff-restore']").trigger("click");
        expect(wrapper.emitted("save")).toBeTruthy();
        expect(wrapper.emitted("save")[0][0]).toEqual({ restore_keys: [] });
        expect(wrapper.emitted("restore")).toBeTruthy();
    });

    it("permet de garder l’ancienne valeur d’une cellule puis toute la colonne Avant", async () => {
        const wrapper = mount(EntityUpdateDiffView, {
            props: { diff },
            global: { stubs },
        });

        await wrapper.get("[data-testid='entity-update-diff-cell-state-before']").trigger("click");
        await wrapper.get("[data-testid='entity-update-diff-save']").trigger("click");
        expect(wrapper.emitted("save")[0][0]).toEqual({ restore_keys: ["state"] });

        await wrapper.get("[data-testid='entity-update-diff-pick-before']").trigger("click");
        await wrapper.get("[data-testid='entity-update-diff-save']").trigger("click");
        expect(wrapper.emitted("save")[1][0]).toEqual({ restore_keys: ["state"] });

        await wrapper.get("[data-testid='entity-update-diff-pick-after']").trigger("click");
        await wrapper.get("[data-testid='entity-update-diff-save']").trigger("click");
        expect(wrapper.emitted("save")[2][0]).toEqual({ restore_keys: [] });
    });

    it("affiche aucun champ modifié quand le compteur est à zéro", () => {
        const wrapper = mount(EntityUpdateDiffView, {
            props: {
                diff: {
                    ...diff,
                    changed_count: 0,
                    fields: [{ key: "name", label: "Nom", before: "A", after: "A", changed: false }],
                },
            },
            global: { stubs },
        });

        expect(wrapper.get("[data-testid='entity-update-diff-empty']").text()).toContain("Aucun champ modifié");
    });
});

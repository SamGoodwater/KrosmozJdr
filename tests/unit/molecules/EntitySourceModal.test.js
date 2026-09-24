import { describe, expect, it, vi, beforeEach } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import axios from "axios";
import EntitySourceModal from "@/Pages/Molecules/entity/EntitySourceModal.vue";

const passwordMocks = vi.hoisted(() => ({
    unlocked: { value: true },
    requirePassword: vi.fn(),
}));

vi.mock("@/Composables/permissions/usePermissions", () => ({
    usePermissions: () => ({ isAdmin: { value: true } }),
}));

vi.mock("axios", () => ({
    default: {
        get: vi.fn(() => Promise.resolve({ data: { usage: null, estimates: [] } })),
        post: vi.fn(),
    },
}));

vi.mock("@/Composables/auth/useProtectedAdminAction", () => ({
    useProtectedAdminAction: () => ({
        isAdminUnlocked: passwordMocks.unlocked,
        showPasswordModal: { value: false },
        passwordModalTitle: { value: "Déverrouiller l’IA" },
        passwordModalMessage: { value: "" },
        passwordModalConfirmLabel: { value: "Déverrouiller" },
        requirePassword: (title, message, label, action) => {
            passwordMocks.requirePassword(title, message, label, action);
            if (passwordMocks.unlocked.value) {
                action?.();
            }
        },
        onPasswordConfirmed: vi.fn(),
        onPasswordModalCancel: vi.fn(),
    }),
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
    ConfirmPasswordModal: { template: "<div class='password-modal-stub' />", props: ["open", "title", "message"] },
};

describe("EntitySourceModal", () => {
    beforeEach(() => {
        passwordMocks.unlocked.value = true;
        passwordMocks.requirePassword.mockClear();
        axios.get.mockReset();
        axios.get.mockResolvedValue({ data: { usage: null, estimates: [] } });
    });

    it("affiche les volets DofusDB, Conversion IA et JSON puis émet convert", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                entityLabel: "Pression",
                entityType: "spells",
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
        expect(tabs).toHaveLength(3);
        expect(tabs[0].text()).toContain("Conversion DofusDB");
        expect(tabs[2].text()).toContain("JSON");
        expect(wrapper.get("[data-testid='entity-source-include-image']").element.checked).toBe(true);
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

    it("ouvre le volet JSON seul pour un type non convertible (campagne)", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: false,
                showJson: true,
                entityLabel: "Incarnam",
                entityType: "campaigns",
            },
            global: { stubs },
        });

        expect(wrapper.findAll("button.tab")).toHaveLength(0);
        expect(wrapper.get("[data-testid='entity-source-json-pane']").exists()).toBe(true);
        expect(wrapper.text()).toContain("Injecter le JSON");
    });

    it("émet inject depuis l’onglet JSON avec un objet valide", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                entityLabel: "Pression",
                entityType: "spells",
            },
            global: { stubs },
        });

        await wrapper.get("[data-testid='entity-source-tab-json']").trigger("click");
        expect(wrapper.get("[data-testid='entity-source-json-pane']").exists()).toBe(true);

        await wrapper.find("textarea").setValue('{"effect":"1d6 Terre"}');
        const injectBtn = wrapper.findAll("button").filter((btn) => btn.text().includes("Injecter le JSON"));
        expect(injectBtn.length).toBe(1);
        await injectBtn[0].trigger("click");

        expect(wrapper.emitted("inject")).toBeTruthy();
        expect(wrapper.emitted("inject")[0][0]).toEqual({ payload: { effect: "1d6 Terre" } });
    });

    it("refuse un JSON racine tableau et charge un exemple via l’API schéma", async () => {
        axios.get.mockImplementation((url) => {
            if (String(url).includes("/api/ia/schema/")) {
                return Promise.resolve({ data: { success: true, example: { effect: "1d4" } } });
            }
            return Promise.resolve({ data: { usage: null, estimates: [] } });
        });

        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                showJson: true,
                entityLabel: "Sort",
                entityType: "spells",
            },
            global: { stubs },
        });

        await wrapper.get("[data-testid='entity-source-tab-json']").trigger("click");
        await wrapper.find("textarea").setValue("[1,2]");
        const injectBtn = wrapper.findAll("button").filter((btn) => btn.text().includes("Injecter le JSON"));
        await injectBtn[0].trigger("click");
        expect(wrapper.emitted("inject")).toBeFalsy();
        expect(wrapper.text()).toContain("objet");

        await wrapper.get("[data-testid='entity-source-json-example']").trigger("click");
        await flushPromises();
        expect(axios.get).toHaveBeenCalledWith(
            "/api/ia/schema/spells",
            expect.objectContaining({ headers: { Accept: "application/json" } }),
        );
        expect(wrapper.find("textarea").element.value).toContain("1d4");
    });

    it("émet confirm DofusDB avec récupération d’image par défaut", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                entityLabel: "Cape du Piou",
            },
            global: { stubs },
        });

        const confirm = wrapper.findAll("button").filter((btn) => btn.text().includes("Confirmer DofusDB"));
        expect(confirm.length).toBe(1);
        await confirm[0].trigger("click");
        expect(wrapper.emitted("confirm")[0][0]).toEqual({
            mode: "full",
            includeImage: true,
            force: false,
        });
    });

    it("coche forcer par défaut sur une fiche jouable", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                playable: true,
                entityLabel: "Pression",
            },
            global: { stubs },
        });

        const confirm = wrapper.findAll("button").filter((btn) => btn.text().includes("Confirmer DofusDB"));
        await confirm[0].trigger("click");
        expect(wrapper.emitted("confirm")[0][0].force).toBe(true);
    });

    it("laisse confirmer DofusDB même si l’aperçu a échoué", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                error: "Le type de cette fiche n’autorise pas la mise à jour depuis DofusDB.",
                entityLabel: "Cape du Piou",
            },
            global: { stubs },
        });

        expect(wrapper.get("[data-testid='entity-source-include-image']").exists()).toBe(true);
        const confirm = wrapper.findAll("button").filter((btn) => btn.text().includes("Confirmer DofusDB"));
        expect(confirm[0].attributes("disabled")).toBeUndefined();
    });

    it("ouvre directement le volet IA pour un PNJ sans DofusDB", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                showJson: true,
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
                showJson: true,
                entityLabel: "Barricade",
                aiActionLabel: "PNJ (fiche complète)",
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

    it("bloque la conversion si la clé Anthropic est absente", () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                showJson: true,
                entityLabel: "Cape",
                aiUsage: { has_api_key: false, local_input_tokens: 0, local_output_tokens: 0, local_runs: 0 },
            },
            global: { stubs },
        });

        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("Clé Anthropic absente");
        const convert = wrapper.findAll("button").filter((btn) => btn.text().includes("Lancer la conversion"));
        expect(convert[0].attributes("disabled")).toBeDefined();
    });

    it("demande le mot de passe avant d’afficher le formulaire IA", async () => {
        passwordMocks.unlocked.value = false;
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: false,
                showAi: true,
                showJson: true,
                entityLabel: "Ganymède",
                aiActionLabel: "PNJ (fiche complète)",
            },
            global: { stubs },
        });

        expect(wrapper.get("[data-testid='ia-unlock']").text()).toContain("Déverrouiller l’IA");
        expect(wrapper.get("[data-testid='ia-source-remaining']").text()).toContain("Confirme ton mot de passe");
        expect(wrapper.text()).not.toContain("Lancer la conversion");
        expect(wrapper.emitted("convert")).toBeFalsy();
        expect(passwordMocks.requirePassword).toHaveBeenCalled();

        await wrapper.get("[data-testid='ia-unlock']").trigger("click");
        expect(passwordMocks.requirePassword).toHaveBeenCalledTimes(2);
        expect(wrapper.emitted("convert")).toBeFalsy();
    });

    it("affiche le tableau avant/après après une conversion", async () => {
        const wrapper = mount(EntitySourceModal, {
            props: {
                open: true,
                showDofusdb: true,
                showAi: true,
                showJson: true,
                diff: {
                    source: "ia",
                    changed_count: 0,
                    before: { preview: { name: "Bouftou", state: "raw" } },
                    after: { preview: { name: "Bouftou", state: "auto" } },
                    fields: [],
                },
            },
            global: { stubs },
        });

        expect(wrapper.text()).toContain("Avant / après");
        expect(wrapper.get("[data-testid='entity-update-diff-empty']").text()).toContain("Aucun champ modifié");
        await wrapper.get("[data-testid='entity-update-diff-save']").trigger("click");
        expect(wrapper.emitted("save-diff")).toBeTruthy();
    });
});

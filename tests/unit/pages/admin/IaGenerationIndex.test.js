import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { reactive } from "vue";
import Index from "@/Pages/Admin/Content/IaGeneration/Index.vue";

function emptyEntity() {
    return {
        has_dofus_source: false,
        frozen_fields: "*",
        writable_fields: [],
        frozen_characteristics: "*",
        writable_characteristics: [],
        example_ids: [],
        task_prompt: "",
    };
}

const defaultProps = {
    config: {
        supervisor_prompt: "Ne sors que du JSON.",
        generation: { max_retries: 2, few_shot_count: 8, max_effects_per_spell: 3 },
        entities: {
            item: { ...emptyEntity(), few_shot_panoplies: [] },
            spell: emptyEntity(),
            monster: emptyEntity(),
            npc: emptyEntity(),
            consumable: emptyEntity(),
        },
    },
    is_stored: false,
    updated_at: null,
    entity_labels: {
        item: "Objets",
        spell: "Sorts",
        monster: "Monstres",
        npc: "PNJ",
        consumable: "Consommables",
    },
    characteristic_options: {},
    items_seeder: {
        relative_root: "database/seeders/data/entities/items",
        file_count: 0,
        auto_count: 0,
        can_export: false,
        can_import: false,
        allowed: false,
    },
    usage: { available: false, message: "Usage indisponible." },
    estimates: [],
    has_api_key: false,
    available_models: [
        { id: "claude-haiku-4-5", label: "Haiku 4.5 (moins cher)", hint: "Défaut." },
        { id: "claude-sonnet-5", label: "Sonnet 5", hint: "Qualité." },
        { id: "claude-opus-5", label: "Opus 5", hint: "Plus cher." },
    ],
};

vi.mock("@inertiajs/vue3", () => ({
    Head: { template: "<div />" },
    useForm: (data) =>
        reactive({
            ...data,
            processing: false,
            errors: {},
            put: vi.fn(),
            delete: vi.fn(),
        }),
    usePage: () => ({ props: { flash: {} } }),
}));

vi.mock("@/Composables/layout/usePageTitle", () => ({
    usePageTitle: () => ({ setPageTitle: vi.fn() }),
}));

vi.mock("@/Composables/store/useNotificationStore", () => ({
    useNotificationStore: () => ({ success: vi.fn() }),
}));

vi.mock("@/Composables/auth/useProtectedAdminAction", () => ({
    useProtectedAdminAction: () => ({
        showPasswordModal: { value: false },
        passwordModalTitle: { value: "" },
        passwordModalMessage: { value: "" },
        passwordModalConfirmLabel: { value: "" },
        requirePassword: vi.fn(),
        onPasswordConfirmed: vi.fn(),
        onPasswordModalCancel: vi.fn(),
    }),
}));

function mountIndex() {
    vi.stubGlobal("route", (name) => `/${name}`);
    return mount(Index, {
        props: defaultProps,
        global: {
            stubs: {
                AdminArea: { template: "<div><slot /></div>" },
                Btn: { template: '<button type="button"><slot /></button>' },
                InputField: {
                    props: ["modelValue", "label"],
                    template: "<label>{{ label }}</label>",
                },
                SelectField: {
                    props: ["modelValue", "label"],
                    template: "<label>{{ label }}</label>",
                },
                TextareaField: {
                    props: ["modelValue", "label"],
                    template: "<label>{{ label }}</label>",
                },
                ConfirmPasswordModal: { template: "<div />" },
                Head: { template: "<div />" },
                SidebarNav: {
                    props: ["items", "getItemClick", "getItemLabel", "isItemActive"],
                    template: `
                        <nav data-testid="ia-entity-tabs-nav">
                            <button
                                v-for="item in items"
                                :key="item.key"
                                type="button"
                                :data-active="isItemActive(item) ? 'true' : 'false'"
                                @click="getItemClick(item)"
                            >
                                {{ getItemLabel(item) }}
                            </button>
                        </nav>
                    `,
                },
                EntityPanel: {
                    props: ["entity", "label"],
                    template:
                        '<section data-testid="ia-entity-panel" :data-entity="entity">{{ label }}</section>',
                },
            },
        },
    });
}

describe("IaGeneration Index", () => {
    it("n’affiche qu’un panneau d’entité et change d’onglet sans empiler", async () => {
        const wrapper = mountIndex();

        expect(wrapper.get("[data-testid='ia-entity-tabs']").exists()).toBe(true);
        expect(wrapper.text()).toContain("Objets");
        expect(wrapper.text()).toContain("Sorts");
        expect(wrapper.text()).toContain("Monstres");
        expect(wrapper.text()).toContain("PNJ");
        expect(wrapper.text()).toContain("Consommables");

        const panels = wrapper.findAll("[data-testid='ia-entity-panel']");
        expect(panels).toHaveLength(1);
        expect(panels[0].attributes("data-entity")).toBe("item");

        const sortTab = wrapper.findAll("button").find((b) => b.text() === "Sorts");
        expect(sortTab).toBeTruthy();
        await sortTab.trigger("click");

        const after = wrapper.findAll("[data-testid='ia-entity-panel']");
        expect(after).toHaveLength(1);
        expect(after[0].attributes("data-entity")).toBe("spell");
        expect(after[0].text()).toBe("Sorts");
    });

    it("conserve solde, prompts globaux et étalons d’équipement", () => {
        const wrapper = mountIndex();

        expect(wrapper.get("[data-testid='ia-usage']").exists()).toBe(true);
        expect(wrapper.text()).toContain("Prompt superviseur");
        expect(wrapper.text()).toContain("Étalons d’équipement");
        expect(wrapper.text()).toContain("Solde et coûts");
        expect(wrapper.get("[data-testid='ia-model-cache']").exists()).toBe(true);
        expect(wrapper.get("[data-testid='ia-prompt-cache']").element.checked).toBe(true);
        expect(wrapper.text()).toContain("Modèle Anthropic");
        expect(wrapper.text()).toContain("Cache prompt Anthropic");
    });
});

import { describe, expect, it, vi, afterEach } from "vitest";
import { flushPromises, mount } from "@vue/test-utils";
import axios from "axios";
import SpellEffectsUnifiedSection from "@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue";

const page = {
    props: {
        auth: { user: { id: 1 } },
        permissions: { entities: {}, access: {} },
    },
};

vi.mock("axios", () => ({
    default: {
        get: vi.fn(() => Promise.resolve({ data: { data: [] } })),
        post: vi.fn(),
        delete: vi.fn(),
    },
}));

vi.mock("@inertiajs/vue3", () => ({
    router: { reload: vi.fn(() => Promise.resolve()) },
    usePage: () => page,
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
});

/**
 * Monte la section effets (le watch immédiat sur les groupes ne doit pas lever de TDZ).
 *
 * @param {Array} spellEffectGroups
 * @param {object} [extraProps]
 * @returns {import('@vue/test-utils').VueWrapper}
 */
function mountSection(spellEffectGroups = [], extraProps = {}) {
    const wrapper = mount(SpellEffectsUnifiedSection, {
        props: {
            entityId: 12,
            spellEffectGroups,
            effectFormOptions: {
                sub_effects: [
                    {
                        id: 1,
                        slug: "frapper",
                        type_slug: "frapper",
                        param_schema: {
                            action: "frapper",
                            params: [
                                { key: "characteristic", type: "characteristic", categories: ["element"] },
                                { key: "value", type: "formula" },
                            ],
                        },
                    },
                    {
                        id: 2,
                        slug: "soigner",
                        type_slug: "soigner",
                        param_schema: {
                            action: "soigner",
                            params: [
                                { key: "characteristic", type: "characteristic", categories: ["element"] },
                                { key: "value", type: "formula" },
                            ],
                        },
                    },
                ],
                characteristics: [
                    { key: "fire", label: "Feu", category: "element" },
                    { key: "earth", label: "Terre", category: "element" },
                ],
                characteristics_object: [],
            },
            ...extraProps,
        },
        global: {
            stubs: {
                EffectGroupEditorForm: true,
                Container: { template: "<div><slot /></div>" },
                InputField: true,
                Icon: true,
                AreaDisplay: true,
            },
        },
    });
    wrappers.push(wrapper);
    return wrapper;
}

describe("SpellEffectsUnifiedSection", () => {
    it("se monte sans effet lié (watch immédiat sur effectsEditorDirty)", async () => {
        const wrapper = mountSection([]);
        await flushPromises();

        expect(wrapper.text()).toContain("Aucun effet lié");
        expect(wrapper.vm.isDirty).toBe(false);
        const previewCall = axios.get.mock.calls.find((call) => call[0] === "/api/effects/for-entity");
        expect(previewCall?.[1]?.params).toEqual({
            entity_type: "spell",
            entity_id: 12,
            level: 1,
        });
    });

    it("se monte avec un groupe d’effets déjà lié", async () => {
        const wrapper = mountSection([
            {
                anchor_effect_id: 4,
                label: "Dommages",
                group_effects: [{ id: 9 }],
            },
        ]);
        await flushPromises();

        expect(wrapper.text()).toContain("Effets du sort");
        expect(wrapper.findComponent({ name: "EffectGroupEditorForm" }).exists()).toBe(true);
    });

    it("crée un effet sur la fiche, sans lien vers l’admin", async () => {
        page.props.permissions = {
            entities: {},
            access: { effectsAdmin: true },
        };
        axios.post.mockResolvedValueOnce({ data: { data: { id: 44, name: "Souffle" } } });

        const wrapper = mountSection([]);
        await flushPromises();

        expect(wrapper.text()).toContain("Créer un effet");
        expect(wrapper.text()).not.toContain("Créer un effet (admin)");
        expect(wrapper.find("[data-cy=create-spell-effect-form]").exists()).toBe(false);

        await wrapper.get("[data-cy=create-spell-effect]").trigger("click");
        expect(wrapper.find("[data-cy=create-spell-effect-form]").exists()).toBe(true);
        expect(wrapper.findAll("[data-cy=create-spell-effect-sub-row]").length).toBe(1);
        expect(wrapper.text()).toContain("Sous-effets");

        await wrapper.get("#spell-effect-create-name").setValue("Souffle");
        const valueInput = wrapper.find("[data-cy=create-spell-effect-sub-row] input[placeholder*='2d6']");
        expect(valueInput.exists()).toBe(true);
        await valueInput.setValue("2d6");
        await wrapper.get("[data-cy=create-spell-effect-form]").trigger("submit");
        await flushPromises();

        expect(axios.post).toHaveBeenCalledWith("/api/effects/spell-effects", {
            spell_id: 12,
            name: "Souffle",
            target_type: "direct",
            initial_area: null,
            initial_sub_effects: [
                expect.objectContaining({
                    sub_effect_id: 1,
                    order: 0,
                    params: expect.objectContaining({ value_formula: "2d6" }),
                }),
            ],
        });
    });

    it("permet d’ajouter plusieurs sous-effets dans le formulaire de création", async () => {
        page.props.permissions = {
            entities: {},
            access: { effectsAdmin: true },
        };

        const wrapper = mountSection([]);
        await flushPromises();

        await wrapper.get("[data-cy=create-spell-effect]").trigger("click");
        expect(wrapper.findAll("[data-cy=create-spell-effect-sub-row]").length).toBe(1);

        await wrapper.get("[data-cy=create-spell-effect-add-sub]").trigger("click");
        expect(wrapper.findAll("[data-cy=create-spell-effect-sub-row]").length).toBe(2);
        expect(wrapper.text()).toContain("Enchaînement");
    });
});

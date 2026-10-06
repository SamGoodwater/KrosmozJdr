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
    Link: { props: ["href"], template: "<a><slot /></a>" },
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
 * @returns {import('@vue/test-utils').VueWrapper}
 */
function mountSection(spellEffectGroups = []) {
    const wrapper = mount(SpellEffectsUnifiedSection, {
        props: {
            entityId: 12,
            spellEffectGroups,
            effectFormOptions: {},
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
});

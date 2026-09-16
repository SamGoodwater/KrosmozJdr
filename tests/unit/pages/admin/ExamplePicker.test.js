import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";
import { flushPromises, mount } from "@vue/test-utils";
import { nextTick } from "vue";
import ExamplePicker from "@/Pages/Admin/Content/IaGeneration/ExamplePicker.vue";

vi.mock("@/Composables/entity/useFavoriteEntityIds", () => ({
    isEntityFavorite: () => false,
    useFavoriteEntityVersion: () => ({ value: 0 }),
}));

const stubs = {
    InputCore: {
        props: ["modelValue", "placeholder"],
        template:
            '<input type="search" :value="modelValue" :placeholder="placeholder" @input="$emit(\'update:modelValue\', $event.target.value)" />',
    },
    Btn: {
        template: '<button type="button" v-bind="$attrs"><slot /></button>',
    },
    Badge: { template: "<span><slot /></span>" },
    EntityThumb: { template: "<span class='thumb-stub' />" },
};

const playableCape = {
    id: 101,
    official_id: "jdr:item:cape-piou",
    name: "Cape du Piou Vert",
    state: "playable",
};

const playableRing = {
    id: 202,
    official_id: "",
    name: "Anneau du Tofu",
    state: "playable",
};

function lastFetchUrl() {
    const calls = global.fetch.mock.calls;
    expect(calls.length).toBeGreaterThan(0);
    return decodeURIComponent(String(calls[calls.length - 1][0]));
}

describe("ExamplePicker", () => {
    beforeEach(() => {
        global.fetch = vi.fn(async () => ({
            ok: true,
            json: async () => ({
                entities: [playableCape, playableRing],
                meta: {},
            }),
        }));
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.restoreAllMocks();
    });

    it("demande les fiches playable du type courant dès le chargement", async () => {
        mount(ExamplePicker, {
            props: { entity: "item", modelValue: [] },
            global: { stubs },
        });

        await flushPromises();

        expect(global.route).toHaveBeenCalled();
        const routeName = global.route.mock.calls[0][0];
        expect(routeName).toBe("api.tables.items");

        const params = global.route.mock.calls[0][1];
        expect(params["filters[state]"]).toBe("playable");
        expect(params.format).toBe("entities");

        const url = lastFetchUrl();
        expect(url).toContain("filters[state]=playable");
    });

    it("conserve le filtre playable quand on recherche par nom", async () => {
        vi.useFakeTimers();
        const wrapper = mount(ExamplePicker, {
            props: { entity: "spell", modelValue: [] },
            global: { stubs },
        });
        await flushPromises();

        await wrapper.get('input[type="search"]').setValue("Pression");
        await vi.advanceTimersByTimeAsync(300);
        await flushPromises();

        const lastCall = global.route.mock.calls.at(-1);
        expect(lastCall[0]).toBe("api.tables.spells");
        expect(lastCall[1]["filters[state]"]).toBe("playable");
        expect(lastCall[1].search).toBe("Pression");
    });

    it("ajoute l’official_id (pas l’id SQL) et permet de retirer un étalon", async () => {
        const wrapper = mount(ExamplePicker, {
            props: { entity: "item", modelValue: [] },
            global: { stubs },
        });
        await flushPromises();
        await nextTick();

        const resultButtons = wrapper.get("[data-testid='ia-example-results']").findAll("button");
        expect(resultButtons.length).toBeGreaterThan(0);
        await resultButtons[0].trigger("click");

        expect(wrapper.emitted("update:modelValue")?.[0]).toEqual([["jdr:item:cape-piou"]]);
        expect(wrapper.emitted("update:modelValue")[0][0]).not.toContain(101);

        await wrapper.setProps({ modelValue: ["jdr:item:cape-piou"] });
        await nextTick();

        const remove = wrapper
            .get("[data-testid='ia-example-selected']")
            .find("button");
        await remove.trigger("click");
        expect(wrapper.emitted("update:modelValue")?.at(-1)).toEqual([[]]);
    });

    it("stocke le nom quand official_id est absent", async () => {
        const wrapper = mount(ExamplePicker, {
            props: { entity: "item", modelValue: [] },
            global: { stubs },
        });
        await flushPromises();
        await nextTick();

        const resultButtons = wrapper.get("[data-testid='ia-example-results']").findAll("button");
        await resultButtons[1].trigger("click");
        expect(wrapper.emitted("update:modelValue")?.[0]).toEqual([["Anneau du Tofu"]]);
    });
});

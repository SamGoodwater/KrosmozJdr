import { describe, it, expect, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import LinkedEntityShow from "@/Pages/Pages/page/LinkedEntityShow.vue";

vi.mock("@inertiajs/vue3", () => ({
    Head: { props: ["title"], template: "<div />" },
    usePage: () => ({ props: { auth: { user: null }, characteristicRuntime: null } }),
}));

const stubs = {
    BreedViewFull: { template: "<div />" },
    SpecializationViewFull: { template: "<div />" },
    EntitySectionsRenderer: { template: "<div />" },
    BreedWriteMetaPanel: { template: "<div />" },
    PageRenderer: { template: "<div />" },
};

/**
 * Payload Inertia d’une JsonResource : champs sous `data`, pas à la racine.
 *
 * @param {string} name
 * @param {"breed"|"specialization"} type
 * @returns {Object}
 */
function wrappedProps(name, type) {
    return {
        page: { data: { id: 1, title: name, sections: [] } },
        pages: [],
        linkedEntityType: type,
        linkedEntity: { data: { id: 4, name, sections: [] } },
    };
}

describe("LinkedEntityShow — titre", () => {
    it("affiche le nom de la classe, pas le repli « Page »", () => {
        const wrapper = mount(LinkedEntityShow, {
            props: wrappedProps("Féca", "breed"),
            global: { stubs },
        });

        expect(usePageTitle().pageTitle.value).toBe("Féca");
        wrapper.unmount();
    });

    it("affiche le nom de la spécialisation", () => {
        const wrapper = mount(LinkedEntityShow, {
            props: wrappedProps("Voie du Feu", "specialization"),
            global: { stubs },
        });

        expect(usePageTitle().pageTitle.value).toBe("Voie du Feu");
        wrapper.unmount();
    });
});

import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import Index from "@/Pages/Admin/Content/Dashboard/Index.vue";

vi.mock("@inertiajs/vue3", () => ({
    Head: { template: "<div />" },
    Link: {
        props: ["href"],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        post: vi.fn(),
        type: "items",
    }),
    usePage: () => ({ props: { flash: {} } }),
}));

vi.mock("@/Composables/layout/usePageTitle", () => ({
    usePageTitle: () => ({ setPageTitle: vi.fn() }),
}));

vi.mock("@/Composables/admin/useProjectConsoleJob", () => ({
    useProjectConsoleJob: () => ({
        liveJob: null,
        pollError: null,
        busy: false,
        cancelJob: vi.fn(),
        cancelling: false,
    }),
}));

describe("Content Dashboard Index", () => {
    it("affiche les cartes atelier DofusDB et génération IA au-dessus des camemberts", () => {
        const wrapper = mount(Index, {
            props: {
                overview: { cms: { pages: 2, sections: 4 }, entities: [] },
                stateLabels: { raw: "Brut", playable: "Jouable" },
                stateColors: { raw: "#888", playable: "#0a0" },
            },
            global: {
                stubs: {
                    AdminArea: { template: "<div><slot /></div>" },
                    AdminDoughnutChart: { template: "<div />" },
                    AdminConsoleJobPanel: { template: "<div />" },
                    AdminCommandMeta: { template: "<div />" },
                    Btn: { template: "<button type='button'><slot /></button>" },
                    Head: { template: "<div />" },
                },
            },
        });

        expect(wrapper.text()).toContain("Atelier DofusDB");
        expect(wrapper.text()).toContain("Récupérer");
        expect(wrapper.text()).toContain("Mettre à jour");
        expect(wrapper.text()).toContain("Compléter");
        expect(wrapper.text()).toContain("Génération IA");
        expect(wrapper.text()).toContain("Pages CMS");

        const hrefs = wrapper.findAll("a").map((a) => a.attributes("href"));
        expect(hrefs).toContain("/admin/content/dofusdb?mode=retrieve");
        expect(hrefs).toContain("/admin/content/dofusdb?mode=update");
        expect(hrefs).toContain("/admin/content/dofusdb?mode=complete");
        expect(hrefs).toContain("/admin/content/ia-generation");
    });
});

import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import Index from "@/Pages/Admin/Content/DofusdbWorkshop/Index.vue";

const pageState = {
    props: { auth: { password_recently_confirmed: true } },
    url: "/admin/content/dofusdb",
};

vi.mock("@inertiajs/vue3", () => ({
    Head: { template: "<div />" },
    Link: { template: "<a><slot /></a>" },
    usePage: () => pageState,
}));

vi.mock("@/Composables/layout/usePageTitle", () => ({
    usePageTitle: () => ({ setPageTitle: vi.fn() }),
}));

describe("DofusdbWorkshop Index", () => {
    it("expose les trois modes et pas Images seules ni le preset auto_update", async () => {
        vi.stubGlobal("route", (name) => `/${name}`);
        const wrapper = mount(Index, {
            global: {
                stubs: {
                    AdminArea: { template: "<div><slot /></div>" },
                    Container: { template: "<div><slot /></div>" },
                    Btn: {
                        template: "<button type='button'><slot /></button>",
                    },
                    ConfirmPasswordModal: { template: "<div />" },
                    ScrappingDashboard: {
                        props: ["workshopMode"],
                        template: '<div class="dash" :data-mode="workshopMode">dashboard</div>',
                    },
                    Head: { template: "<div />" },
                    Link: { template: "<a><slot /></a>" },
                },
            },
        });

        expect(wrapper.text()).toContain("Récupérer");
        expect(wrapper.text()).toContain("Mettre à jour");
        expect(wrapper.text()).toContain("Compléter");
        expect(wrapper.text()).not.toContain("Images seules");
        expect(wrapper.text()).not.toContain("Preset");
        expect(wrapper.find(".dash").attributes("data-mode")).toBe("retrieve");

        const updateBtn = wrapper.findAll("button").find((b) => b.text().includes("Compléter"));
        await updateBtn.trigger("click");
        expect(wrapper.find(".dash").attributes("data-mode")).toBe("complete");
    });

    it("ouvre le mode fourni par ?mode=", () => {
        pageState.url = "/admin/content/dofusdb?mode=update";
        vi.stubGlobal("route", (name) => `/${name}`);
        const wrapper = mount(Index, {
            global: {
                stubs: {
                    AdminArea: { template: "<div><slot /></div>" },
                    Container: { template: "<div><slot /></div>" },
                    Btn: {
                        template: "<button type='button'><slot /></button>",
                    },
                    ConfirmPasswordModal: { template: "<div />" },
                    ScrappingDashboard: {
                        props: ["workshopMode"],
                        template: '<div class="dash" :data-mode="workshopMode">dashboard</div>',
                    },
                    Head: { template: "<div />" },
                    Link: { template: "<a><slot /></a>" },
                },
            },
        });

        expect(wrapper.find(".dash").attributes("data-mode")).toBe("update");
        pageState.url = "/admin/content/dofusdb";
    });
});

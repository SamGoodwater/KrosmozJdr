import { describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import Route from "@/Pages/Atoms/action/Route.vue";

vi.mock("@inertiajs/vue3", () => ({
    Link: { props: ["href"], template: '<a :href="href"><slot /></a>' },
}));

describe("Route", () => {
    it("pose rel sur un lien externe sans l’enregistrer comme événement onRel", () => {
        const warn = vi.spyOn(console, "warn").mockImplementation(() => {});
        const wrapper = mount(Route, {
            props: {
                href: "https://discord.gg/XVu4VWFskj",
                target: "_blank",
            },
            attrs: { rel: "noopener noreferrer" },
            slots: { default: "Discord" },
        });

        const link = wrapper.get("a");
        expect(link.attributes("href")).toBe("https://discord.gg/XVu4VWFskj");
        expect(link.attributes("target")).toBe("_blank");
        expect(link.attributes("rel")).toBe("noopener noreferrer");
        expect(warn.mock.calls.some((args) => String(args[0]).includes("onRel"))).toBe(false);

        warn.mockRestore();
    });

    it("transmet un clic sans le préfixer en onOnClick", async () => {
        const onClick = vi.fn();
        const wrapper = mount(Route, {
            props: { href: "mailto:contact@krosmoz-jdr.fr" },
            attrs: { onClick },
            slots: { default: "Contact" },
        });

        await wrapper.get("a").trigger("click");
        expect(onClick).toHaveBeenCalledOnce();
    });
});

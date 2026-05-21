import "../css/app.css";
import { applyZiggyFromPageProps } from "./ziggy-global.js";
import "./bootstrap";

// PhotoSwipe (ImageViewer)
import "photoswipe/style.css";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { InertiaZiggyVue } from "@/Plugins/inertia-ziggy";
import { createPinia } from "pinia";
import DefaultLayout from "@/Pages/Layouts/Main.vue";

const appName = import.meta.env.VITE_APP_NAME || "KrosmozJDR";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"));
        page.default.layout = page.default.layout || DefaultLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        applyZiggyFromPageProps(
            props.initialPage?.props?.ziggy,
            props.initialPage?.props?.ziggy_location,
        );
        void import("@/Utils/Formatters");
        const pinia = createPinia();
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(InertiaZiggyVue)
            .use(pinia)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
    progress: {
        color: "#155e75",
        showSpinner: true,
    },
});

import "../css/app.css";
import "./bootstrap";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createPinia } from "pinia";
import DefaultLayout from "@/Pages/Layouts/Main.vue";
import mediaManagerPlugin from "@/Plugins/mediaManager";
const appName = import.meta.env.VITE_APP_NAME || "KrosmozJDR";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ).then((module) => {
            const page = module.default;
            page.layout = page.layout || DefaultLayout;
            return page;
        }),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(mediaManagerPlugin)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
    progress: {
        color: "#155e75",
        showSpinner: true,
    },
});

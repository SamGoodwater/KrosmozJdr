import "../css/app.css";
import "./bootstrap";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createPinia } from "pinia"; // Import Pinia
import DefaultLayout from "@/Pages/Layouts/Main.vue";

const appName = import.meta.env.VITE_APP_NAME || "KrosmozJDR";
const appDescription = import.meta.env.VITE_APP_DESCRIPTION;
const appVersion = import.meta.env.VITE_APP_VERSION;
const appStability = import.meta.env.VITE_APP_STABILITY;
const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};
const appStabilitySymbol = convertStability[appStability] || appStability;

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
        const pinia = createPinia(); // Créez une instance de Pinia
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia) // Utilisez Pinia
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
    progress: {
        // The color of the progress bar...
        color: "#155e75", // Cyan 800
        // Whether to include the default NProgress styles...
        showSpinner: true,
    },
});

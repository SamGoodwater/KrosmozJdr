import "../css/app.css";
import "./bootstrap";

// Import Cally web component pour les composants de date
import "cally";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createPinia } from "pinia";
import DefaultLayout from "@/Pages/Layouts/Main.vue";
import { preloadCommonTemplates } from "@/Pages/Organismes/section/composables/useTemplateRegistry";

const appName = import.meta.env.VITE_APP_NAME || "KrosmozJDR";

// Précharger les templates courants au démarrage (performance)
preloadCommonTemplates().catch(err => console.warn('Template preload failed:', err));

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        page.default.layout = page.default.layout || DefaultLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
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

/**
 * Plugin Vue : route() alimenté par les props Inertia (ziggy + ziggy_location).
 *
 * @description
 * Synchronise globalThis.Ziggy à chaque appel ; globalThis.route est défini dans ziggy-global.js.
 *
 * @example
 * // Dans un composant : route('pages.show', 'accueil')
 */
import { router, usePage } from "@inertiajs/vue3";
import { route as ziggyRoute } from "../../../vendor/tightenco/ziggy";
import { applyZiggyFromPageProps } from "@/ziggy-global.js";

/**
 * @returns {import('ziggy-js').Router|string}
 */
function inertiaRoute(name, params, absolute) {
    try {
        const page = usePage();
        applyZiggyFromPageProps(page.props.ziggy, page.props.ziggy_location);
    } catch {
        // Hors composant actif : globalThis.Ziggy (bootstrap ziggy-global.js)
    }

    return ziggyRoute(name, params, absolute, globalThis.Ziggy);
}

export const InertiaZiggyVue = {
    install(app) {
        globalThis.route = inertiaRoute;

        if (parseInt(app.version, 10) > 2) {
            app.config.globalProperties.route = inertiaRoute;
            app.provide("route", inertiaRoute);
        } else {
            app.mixin({
                methods: {
                    route: inertiaRoute,
                },
            });
        }

        router.on("navigate", (event) => {
            const props = event.detail?.page?.props;
            if (props) {
                applyZiggyFromPageProps(props.ziggy, props.ziggy_location);
            }
        });
    },
};

/**
 * Initialise Ziggy pour les appels `route()` hors contexte Vue (module scope, composables).
 *
 * @description
 * Remplace le script injecté par `@routes`. Les navigations Inertia mettent à jour
 * `globalThis.Ziggy` via `applyZiggyFromPageProps` dans inertia-ziggy.js.
 */
import { Ziggy as ziggyRoutes } from "./ziggy.js";
import { route as ziggyRoute } from "../../vendor/tightenco/ziggy";

/**
 * @param {Record<string, unknown>|undefined} ziggy
 * @param {string|undefined} location
 */
export function applyZiggyFromPageProps(ziggy, location) {
    if (!ziggy || typeof ziggy !== "object") {
        return;
    }

    const resolvedLocation =
        location ??
        ziggy.location ??
        (typeof window !== "undefined" ? window.location.href : ziggyRoutes.url);

    globalThis.Ziggy = { ...ziggy, location: resolvedLocation };
}

if (typeof window !== "undefined") {
    applyZiggyFromPageProps(ziggyRoutes, window.location.href);
}

/**
 * @type {typeof ziggyRoute}
 */
function globalRoute(name, params, absolute) {
    return ziggyRoute(name, params, absolute, globalThis.Ziggy);
}

globalThis.route = globalRoute;

export { globalRoute as route };

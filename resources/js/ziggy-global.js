/**
 * Initialise Ziggy pour les appels `route()` hors contexte Vue (module scope, composables).
 *
 * @description
 * Remplace le script injecté par `@routes`. Les navigations Inertia mettent à jour
 * `globalThis.Ziggy` via `applyZiggyFromPageProps` dans inertia-ziggy.js.
 *
 * Important : Ziggy génère des URL absolues depuis `Ziggy.url` (pas `location`).
 * Si l’utilisateur ouvre l’app en `127.0.0.1` alors que le bundle a `localhost`
 * (APP_URL), les soumissions Inertia / axios partent en cross-origin → échec silencieux.
 */
import { Ziggy as ziggyRoutes } from "./ziggy.js";
import { route as ziggyRoute } from "../../vendor/tightenco/ziggy";
import { resolveZiggyOrigin } from "./Utils/ziggy-origin.js";

export { resolveZiggyOrigin } from "./Utils/ziggy-origin.js";

/**
 * @param {Record<string, unknown>|undefined} ziggy
 * @param {string|{href?: string}|undefined} location
 */
export function applyZiggyFromPageProps(ziggy, location) {
    const origin = resolveZiggyOrigin(location);

    if (ziggy && typeof ziggy === "object") {
        globalThis.Ziggy = {
            ...ziggy,
            ...origin,
            location: origin.location ?? location ?? ziggy.location,
        };
        return;
    }

    // Prop Inertia absente : garder le catalogue du bundle, aligner l’origine courante.
    if (globalThis.Ziggy && typeof globalThis.Ziggy === "object") {
        globalThis.Ziggy = {
            ...globalThis.Ziggy,
            ...origin,
            location: origin.location ?? location ?? globalThis.Ziggy.location,
        };
    }
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

/**
 * useGlobalEntitySearch — Recherche globale multi‑entités pour le header
 *
 * @description
 * Lance en parallèle des recherches sur plusieurs endpoints `api.tables.{entityType}`
 * (format=entities) et agrège les résultats dans une liste plate, prête à afficher
 * dans un dropdown sous le champ de recherche du header.
 *
 * Pensé pour un usage léger (User / invité) :
 * - Requête déclenchée après un minimum de caractères
 * - Debounce configurable
 * - Pas de pagination : petit nombre de résultats par entité
 */

import { ref, computed, watch } from "vue";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";

const DEFAULT_GLOBAL_ENTITIES = [
    { entityType: "campaigns", label: "Campagnes", icon: "fa-solid fa-flag", limit: 5 },
    { entityType: "scenarios", label: "Scénarios", icon: "fa-solid fa-scroll", limit: 5 },
    { entityType: "spells", label: "Sorts", icon: "fa-solid fa-wand-magic-sparkles", limit: 5 },
    { entityType: "items", label: "Objets", icon: "fa-solid fa-sack-dollar", limit: 5 },
    { entityType: "resources", label: "Ressources", icon: "fa-solid fa-box", limit: 5 },
    { entityType: "consumables", label: "Consommables", icon: "fa-solid fa-flask", limit: 5 },
    { entityType: "monsters", label: "Monstres", icon: "fa-solid fa-dragon", limit: 5 },
    { entityType: "npcs", label: "PNJ", icon: "fa-solid fa-user", limit: 5 },
];

/**
 * @param {Object} [options]
 * @param {Array<{entityType:string,label:string,icon?:string,limit?:number}>} [options.entities]
 * @param {number} [options.minQueryLength=2]
 * @param {number} [options.debounce=250]
 */
export function useGlobalEntitySearch(options = {}) {
    const {
        entities = DEFAULT_GLOBAL_ENTITIES,
        minQueryLength = 2,
        debounce = 250,
    } = options;

    const query = ref("");
    const loading = ref(false);
    const error = ref(null);
    const isOpen = ref(false);

    /** @type {import('vue').Ref<Array<{ id:number|string, entityType:string, group:string, title:string, subtitle?:string, href:string, icon?:string }>>} */
    const flatResults = ref([]);

    let debounceTimer = null;
    let abortController = null;

    const hasResults = computed(() => flatResults.value.length > 0);

    const clearResults = () => {
        flatResults.value = [];
        error.value = null;
    };

    const setQuery = (value) => {
        query.value = value ?? "";
    };

    const close = () => {
        isOpen.value = false;
    };

    const open = () => {
        if (hasResults.value) {
            isOpen.value = true;
        }
    };

    const buildUrl = (entityType, searchText, limit) => {
        const params = {
            format: "entities",
            search: searchText,
            limit,
        };
        // Ziggy `route` global — la route peut ne pas exister pour certains entityType
        try {
            // eslint-disable-next-line no-undef
            return route(`api.tables.${entityType}`, params);
        } catch (e) {
            // eslint-disable-next-line no-console
            console.warn(
                "[useGlobalEntitySearch] Route Ziggy manquante pour",
                `api.tables.${entityType}`
            );
            return null;
        }
    };

    const normalizeEntity = (entityType, cfg, raw) => {
        const id = raw.id;
        const title = raw.name || raw.creature?.name || raw.title || raw.slug || `#${id}`;
        const subtitle =
            raw.description ||
            raw.keyword ||
            raw.slug ||
            raw.creature?.name ||
            "";

        let href = "";
        try {
            // Certaines entités (campagnes, scénarios) utilisent le slug pour la route show
            const slugOrId = raw.slug || id;
            href = resolveEntityRouteHref(entityType, "show", slugOrId);
        } catch (e) {
            href = "";
        }

        return {
            id,
            entityType,
            group: cfg.label || entityType,
            title: String(title),
            subtitle: subtitle ? String(subtitle) : "",
            href,
            icon: cfg.icon || "",
        };
    };

    const searchNow = async () => {
        const q = (query.value || "").trim();

        if (q.length < minQueryLength) {
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            loading.value = false;
            clearResults();
            isOpen.value = false;
            return;
        }

        if (abortController) {
            abortController.abort();
        }
        abortController = typeof AbortController !== "undefined" ? new AbortController() : null;

        loading.value = true;
        error.value = null;
        isOpen.value = true;

        try {
            const controller = abortController;
            const promises = entities.map(async (cfg) => {
                const { entityType, limit = 5 } = cfg;
                if (!entityType) return [];

                const url = buildUrl(entityType, q, limit);
                if (!url) return [];
                const res = await fetch(url, {
                    headers: { Accept: "application/json" },
                    signal: controller?.signal,
                });
                if (!res.ok) {
                    // 403/404/etc. => on ignore silencieusement pour cette entité
                    return [];
                }
                const data = await res.json();
                const list = Array.isArray(data?.entities) ? data.entities : [];
                return list.map((raw) => normalizeEntity(entityType, cfg, raw));
            });

            const perEntityResults = await Promise.all(promises);
            const merged = perEntityResults.flat();

            flatResults.value = merged;
        } catch (e) {
            if (e?.name === "AbortError") {
                // Nouvelle recherche a été lancée
                return;
            }
            // Pour ne pas spammer l'utilisateur, on loggue en console et on affiche un message générique
            // eslint-disable-next-line no-console
            console.error("[useGlobalEntitySearch] Erreur lors de la recherche globale :", e);
            error.value = e?.message || "Erreur lors de la recherche.";
        } finally {
            loading.value = false;
        }
    };

    const debouncedSearch = () => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
        debounceTimer = setTimeout(() => {
            searchNow();
        }, debounce);
    };

    watch(
        () => query.value,
        () => {
            debouncedSearch();
        }
    );

    return {
        query,
        results: flatResults,
        loading,
        error,
        isOpen,
        hasResults,
        setQuery,
        searchNow,
        open,
        close,
    };
}


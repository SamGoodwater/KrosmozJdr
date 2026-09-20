/**
 * Overlay kref « entité » : fiche `*ViewMinimal` (même contrat que
 * {@link EntityViewTextLink} / recherche), via `api.tables.*`.
 *
 * @example
 * const overlay = await loadKrefEntityMinimalOverlay("capabilities", 12);
 * // { component: CapabilityViewMinimal, props: { capability, showActions, displayMode } }
 */
import { markRaw } from "vue";
import { resolveEntityViewComponent } from "@/Utils/entity/resolveEntityViewComponent";
import {
    entityViewPropName,
    fetchEntityModelById,
    supportsEntityCatalogViews,
} from "@/Composables/entity/useEntityTableFetch";

/**
 * Charge la vue minimale d’une entité référencée par kref.
 *
 * @param {string} entityType — type kref / catalogue (`capabilities`, `spells`, …)
 * @param {string|number} id — identifiant numérique
 * @returns {Promise<{ component: object, props: Record<string, unknown> }|null>}
 */
export async function loadKrefEntityMinimalOverlay(entityType, id) {
    const type = String(entityType || "").trim();
    if (!type || id == null || id === "") {
        return null;
    }
    if (!supportsEntityCatalogViews(type)) {
        return null;
    }

    try {
        const [component, model] = await Promise.all([
            resolveEntityViewComponent(type, "minimal"),
            fetchEntityModelById(type, id),
        ]);
        if (!component || !model) {
            return null;
        }
        return {
            component: markRaw(component),
            props: {
                [entityViewPropName(type)]: model,
                showActions: true,
                displayMode: "extended",
            },
        };
    } catch {
        return null;
    }
}

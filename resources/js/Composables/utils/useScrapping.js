/**
 * Composable pour gérer le scrapping d'entités
 *
 * @description
 * Conservé pour les appels existants. La maj unitaire passe par
 * `useEntityDofusdbRefresh` (id local, preview, policy update).
 *
 * @example
 * const { refreshEntity } = useScrapping();
 * await refreshEntity('resource', 123);
 */
import { useEntityDofusdbRefresh } from "@/Composables/entity/useEntityDofusdbRefresh";
import { isScrappableEntityType } from "@/Entities/entity-actions-config";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

export function useScrapping() {
    const { applyRefresh } = useEntityDofusdbRefresh();
    const { error: showError } = useNotificationStore();

    /**
     * Applique une maj DofusDB unitaire (sans aperçu). Préférer le panneau de confirmation.
     *
     * @param {string} entityType
     * @param {number|string} entityId
     * @param {{ forceUpdate?: boolean, imagesOnly?: boolean }} options
     * @returns {Promise<boolean>}
     */
    const refreshEntity = async (entityType, entityId, options = {}) => {
        if (!isScrappableEntityType(entityType)) {
            showError(`Le scrapping n'est pas supporté pour le type "${entityType}".`);
            return false;
        }
        return applyRefresh(entityType, entityId, {
            mode: options.imagesOnly ? "images_only" : "full",
            force: Boolean(options.forceUpdate),
        });
    };

    return {
        refreshEntity,
    };
}

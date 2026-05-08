/**
 * Dispatcher central des actions d'entités.
 *
 * @description
 * Mutualise les effets standards : navigation, modal, copie de lien, refresh et suppression.
 *
 * @example
 * const { dispatchEntityAction } = useEntityActionDispatcher('items', { openModal });
 */
import { router } from "@inertiajs/vue3";
import { computed } from "vue";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";
import {
    getEntityRouteConfig,
    getEntitySingularRouteKey,
    resolveEntityRouteUrl,
} from "@/Composables/entity/entityRouteRegistry";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useScrapping } from "@/Composables/utils/useScrapping";

function getEntityId(entity) {
    return entity?.id ?? entity?._data?.id ?? null;
}

export function useEntityActionDispatcher(entityType, handlers = {}) {
    const { copyToClipboard } = useCopyToClipboard();
    const { refreshEntity } = useScrapping();

    const normalizedType = computed(() => normalizeActionEntityType(entityType?.value ?? entityType));
    const routeParamKey = computed(() => getEntitySingularRouteKey(normalizedType.value));

    async function dispatchEntityAction(actionKey, entity, meta = {}) {
        const entityId = getEntityId(entity);
        if (!entityId) return false;

        const plural = normalizedType.value;
        const paramKey = routeParamKey.value;
        const cfg = getEntityRouteConfig(paramKey);

        switch (actionKey) {
            case "view":
            case "expand":
            case "open-page":
                router.visit(route(`entities.${plural}.show`, { [paramKey]: entityId }));
                handlers.onOpenPage?.(entity, meta);
                return true;

            case "quick-view":
            case "open-modal":
                handlers.openModal?.(entity, meta);
                return true;

            case "edit":
            case "edit-page":
                router.visit(route(`entities.${plural}.edit`, { [paramKey]: entityId }));
                handlers.onEditPage?.(entity, meta);
                return true;

            case "quick-edit":
            case "edit-modal":
                handlers.openEditModal?.(entity, meta);
                return true;

            case "copy-link": {
                const url = resolveEntityRouteUrl(paramKey, "show", entityId, cfg);
                if (url) await copyToClipboard(url, "Lien de l'entité copié !");
                handlers.onCopyLink?.(entity, meta);
                return true;
            }

            case "refresh": {
                if (handlers.onRefresh) {
                    await handlers.onRefresh(entity, meta);
                } else {
                    await refreshEntity(paramKey, entityId, { forceUpdate: true });
                }
                return true;
            }

            case "delete":
                handlers.onDelete?.(entity, meta);
                return true;

            default:
                handlers.onUnhandled?.(actionKey, entity, meta);
                return false;
        }
    }

    return {
        dispatchEntityAction,
    };
}

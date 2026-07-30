/**
 * Dispatcher central des actions d'entités.
 *
 * @description
 * Mutualise les effets standards : navigation, modal, copie de lien, refresh et suppression.
 * La suppression expose un état de ConfirmModal (récap impact) plutôt qu’un `window.confirm`.
 *
 * @example
 * const { dispatchEntityAction, deleteConfirm, confirmPendingDelete, cancelPendingDelete } =
 *   useEntityActionDispatcher('items', { openModal });
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
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

function getEntityLabel(entity) {
    return entity?.name || entity?.title || entity?._data?.name || entity?._data?.title || "cette entité";
}

export function useEntityActionDispatcher(entityType, handlers = {}) {
    const { copyToClipboard } = useCopyToClipboard();
    const { refreshEntity } = useScrapping();

    const normalizedType = computed(() => normalizeActionEntityType(entityType?.value ?? entityType));
    const routeParamKey = computed(() => getEntitySingularRouteKey(normalizedType.value));

    const deleteConfirm = ref({
        open: false,
        title: "Placer en corbeille",
        message: "",
        details: [],
        entity: null,
        meta: {},
        loading: false,
    });

    function resetDeleteConfirm() {
        deleteConfirm.value = {
            open: false,
            title: "Placer en corbeille",
            message: "",
            details: [],
            entity: null,
            meta: {},
            loading: false,
        };
    }

    async function deleteEntityWithConfirmation(entity, meta = {}) {
        if (handlers.onDelete) {
            handlers.onDelete(entity, meta);
            return true;
        }

        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural) return false;

        let impact = null;
        try {
            const { data } = await axios.get(`/api/entities/${encodeURIComponent(plural)}/${entityId}/delete-impact`, {
                headers: { Accept: "application/json" },
            });
            impact = data;
        } catch {
            impact = null;
        }

        const relations = Array.isArray(impact?.relations) ? impact.relations : [];
        const mediaCount = Number(impact?.media_count ?? 0);
        const details = [
            relations.length > 0
                ? `${relations.length} relation(s) seront détachées en suppression définitive : ${relations.join(", ")}.`
                : null,
            mediaCount > 0
                ? `${mediaCount} média(s) lié(s) seront conservés en corbeille.`
                : null,
            "Un administrateur pourra restaurer l’entité depuis le journal admin.",
        ].filter(Boolean);

        deleteConfirm.value = {
            open: true,
            title: "Placer en corbeille",
            message: `Placer « ${getEntityLabel(entity)} » en corbeille ?`,
            details,
            entity,
            meta,
            loading: false,
        };

        return true;
    }

    async function confirmPendingDelete() {
        const pending = deleteConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural || pending.loading) return false;

        deleteConfirm.value = { ...pending, loading: true };
        try {
            await axios.delete(`/api/entities/${encodeURIComponent(plural)}/${entityId}`, {
                headers: { Accept: "application/json" },
            });
            handlers.onDeleted?.(entity, pending.meta);
            if (!handlers.onDeleted) {
                router.reload();
            }
            resetDeleteConfirm();
            return true;
        } catch (error) {
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible de placer l’entité en corbeille.";
            window.alert(message);
            deleteConfirm.value = { ...deleteConfirm.value, loading: false };
            return false;
        }
    }

    function cancelPendingDelete() {
        resetDeleteConfirm();
    }

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
                return deleteEntityWithConfirmation(entity, meta);

            default:
                handlers.onUnhandled?.(actionKey, entity, meta);
                return false;
        }
    }

    return {
        dispatchEntityAction,
        deleteConfirm,
        confirmPendingDelete,
        cancelPendingDelete,
    };
}

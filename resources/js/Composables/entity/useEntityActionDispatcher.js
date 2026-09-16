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
import {
    isAiConvertibleEntityType,
    isScrappableEntityType,
    normalizeActionEntityType,
} from "@/Entities/entity-actions-config";
import {
    getEntityRouteConfig,
    getEntitySingularRouteKey,
    resolveEntityRouteUrl,
} from "@/Composables/entity/entityRouteRegistry";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useEntityDofusdbRefresh } from "@/Composables/entity/useEntityDofusdbRefresh";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

function getEntityId(entity) {
    return entity?.id ?? entity?._data?.id ?? null;
}

function getEntityLabel(entity) {
    return entity?.name || entity?.title || entity?._data?.name || entity?._data?.title || "cette entité";
}

export function useEntityActionDispatcher(entityType, handlers = {}) {
    const { copyToClipboard } = useCopyToClipboard();
    const { previewRefresh, applyRefresh } = useEntityDofusdbRefresh();
    const { isAdmin, canUpdateAny } = usePermissions();
    const notificationStore = useNotificationStore();

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

    const refreshConfirm = ref({
        open: false,
        loading: false,
        applying: false,
        preview: null,
        error: "",
        playable: false,
        entity: null,
        entityLabel: "cette fiche",
        meta: {},
        showDofusdb: false,
        showAi: false,
        aiBrief: "",
        aiSubmitting: false,
        aiError: "",
        aiSuccess: "",
        aiEstimate: null,
        aiAction: "",
        aiActionLabel: "Conversion IA",
    });

    function resetRefreshConfirm() {
        refreshConfirm.value = {
            open: false,
            loading: false,
            applying: false,
            preview: null,
            error: "",
            playable: false,
            entity: null,
            entityLabel: "cette fiche",
            meta: {},
            showDofusdb: false,
            showAi: false,
            aiBrief: "",
            aiSubmitting: false,
            aiError: "",
            aiSuccess: "",
            aiEstimate: null,
            aiAction: "",
            aiActionLabel: "Conversion IA",
        };
    }

    function actionForType(plural) {
        const map = {
            monsters: "encounter",
            spells: "spell",
            npcs: "npc",
            items: "item",
            consumables: "consumable",
        };
        return map[plural] || "";
    }

    function actionLabel(action) {
        const map = {
            encounter: "Rencontre (monstre + sorts)",
            spell: "Sort (effets)",
            npc: "PNJ (fiche complète)",
            item: "Équipement",
            consumable: "Consommable",
        };
        return map[action] || "Conversion IA";
    }

    function isPlayableEntity(entity) {
        const state = entity?.state || entity?.creature?.state || entity?._data?.state;
        return state === "playable" || state === "archived";
    }

    async function openRefreshPanel(entity, meta = {}) {
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const showDofusdb = Boolean(entityId && isScrappableEntityType(plural) && canUpdateAny(plural));
        const aiAction = actionForType(plural);
        const showAi = Boolean(entityId && isAdmin.value && isAiConvertibleEntityType(plural) && aiAction);
        if (!entityId || (!showDofusdb && !showAi)) return false;

        refreshConfirm.value = {
            open: true,
            loading: showDofusdb,
            applying: false,
            preview: null,
            error: "",
            playable: isPlayableEntity(entity),
            entity,
            entityLabel: getEntityLabel(entity),
            meta,
            showDofusdb,
            showAi,
            aiBrief: "",
            aiSubmitting: false,
            aiError: "",
            aiSuccess: "",
            aiEstimate: null,
            aiAction,
            aiActionLabel: actionLabel(aiAction),
        };

        if (showAi) {
            try {
                const { data } = await axios.get("/api/ia/status", { headers: { Accept: "application/json" } });
                const estimates = Array.isArray(data?.estimates) ? data.estimates : [];
                const estimate = estimates.find((row) => row.action === aiAction) || null;
                if (refreshConfirm.value.open) {
                    refreshConfirm.value = { ...refreshConfirm.value, aiEstimate: estimate };
                }
            } catch {
                // Estimé optionnel.
            }
        }

        if (!showDofusdb) {
            return true;
        }

        const preview = await previewRefresh(plural, entityId);
        if (!refreshConfirm.value.open) return false;
        if (!preview) {
            refreshConfirm.value = {
                ...refreshConfirm.value,
                loading: false,
                error: "Impossible de charger l’aperçu DofusDB.",
            };
            return false;
        }
        if (preview.success === false && preview.message && !preview.data) {
            refreshConfirm.value = {
                ...refreshConfirm.value,
                loading: false,
                error: String(preview.message),
            };
            return false;
        }
        refreshConfirm.value = {
            ...refreshConfirm.value,
            loading: false,
            preview,
            error: preview.success === false ? String(preview.message || "") : "",
        };
        return true;
    }

    async function submitAiConvert() {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const action = pending.aiAction || actionForType(plural);
        if (!entityId || !plural || pending.aiSubmitting || !action) return false;

        refreshConfirm.value = { ...pending, aiSubmitting: true, aiError: "", aiSuccess: "" };
        try {
            const { data } = await axios.post(
                `/api/entities/${encodeURIComponent(plural)}/${entityId}/ia-convert`,
                { action, brief: pending.aiBrief || null, force: Boolean(pending.playable) },
                { headers: { Accept: "application/json" } },
            );
            if (data?.success === false) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiSubmitting: false,
                    aiError: data.message || "La conversion IA a échoué.",
                };
                return false;
            }
            notificationStore.success(data?.message || "Conversion IA enregistrée en auto.");
            handlers.onRefresh?.(entity, pending.meta);
            resetRefreshConfirm();
            return true;
        } catch (error) {
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible de lancer la conversion IA.";
            refreshConfirm.value = { ...refreshConfirm.value, aiSubmitting: false, aiError: String(message) };
            return false;
        }
    }

    async function confirmPendingRefresh(options = {}) {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural || pending.applying) return false;

        refreshConfirm.value = { ...pending, applying: true };
        const ok = await applyRefresh(plural, entityId, {
            mode: options.mode === "images_only" ? "images_only" : "full",
            force: Boolean(options.force),
        });
        if (!ok) {
            refreshConfirm.value = { ...refreshConfirm.value, applying: false };
            return false;
        }
        handlers.onRefresh?.(entity, pending.meta);
        resetRefreshConfirm();
        return true;
    }

    function cancelPendingRefresh() {
        resetRefreshConfirm();
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
            case "edit-modal":
                router.visit(route(`entities.${plural}.edit`, { [paramKey]: entityId }));
                handlers.onEditPage?.(entity, meta);
                return true;

            case "copy-link": {
                const url = resolveEntityRouteUrl(paramKey, "show", entityId, cfg);
                if (url) await copyToClipboard(url, "Lien de l'entité copié !");
                handlers.onCopyLink?.(entity, meta);
                return true;
            }

            case "refresh": {
                if (handlers.onRefreshRequest) {
                    await handlers.onRefreshRequest(entity, meta);
                } else {
                    await openRefreshPanel(entity, meta);
                }
                return true;
            }

            case "view-dofusdb": {
                // Ouvert via EntityActionButton / EntityActions → store Pinia.
                handlers.onViewDofusdb?.(entity, meta);
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
        refreshConfirm,
        confirmPendingRefresh,
        cancelPendingRefresh,
        submitAiConvert,
    };
}

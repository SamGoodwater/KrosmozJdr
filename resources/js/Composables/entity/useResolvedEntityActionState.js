/**
 * État local des actions d'entités (`pin`, `favorite`).
 *
 * @description
 * Centralise les libellés, icônes et bascules locales utilisés par les menus et raccourcis.
 *
 * @example
 * const { resolvedActions, runLocalAction } = useResolvedEntityActionState('items', entity, actions);
 */
import { computed } from "vue";
import {
    isEntityPinned,
    toggleEntityPin,
    usePinnedEntityVersion,
} from "@/Composables/entity/usePinnedEntityIds";
import {
    isEntityFavorite,
    toggleEntityFavorite,
    useFavoriteEntityVersion,
} from "@/Composables/entity/useFavoriteEntityIds";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

export function useResolvedEntityActionState(entityType, entity, actions) {
    const pinVersion = usePinnedEntityVersion();
    const favoriteVersion = useFavoriteEntityVersion();
    const { notifySuccess } = useUxFeedback();

    const entityIdStr = computed(() => {
        const e = entity?.value ?? entity;
        const id = e?.id ?? e?._data?.id;
        if (id == null || id === "") return "";
        return String(id);
    });

    const normalizedEntityType = computed(() => String(entityType?.value ?? entityType ?? "").trim());

    const pinned = computed(() => {
        pinVersion.value;
        if (!normalizedEntityType.value || !entityIdStr.value) return false;
        return isEntityPinned(normalizedEntityType.value, entityIdStr.value);
    });

    const favorite = computed(() => {
        favoriteVersion.value;
        if (!normalizedEntityType.value || !entityIdStr.value) return false;
        return isEntityFavorite(normalizedEntityType.value, entityIdStr.value);
    });

    const resolvedActions = computed(() => {
        const list = Array.isArray(actions?.value) ? actions.value : Array.isArray(actions) ? actions : [];
        return list.map((a) => {
            if (a?.key === "pin") {
                return {
                    ...a,
                    label: pinned.value ? "Désépingler" : "Épingler",
                    tooltip: pinned.value ? "Retirer des fiches épinglées (local)" : a.tooltip || "Épingler cette fiche",
                    icon: "fa-solid fa-thumbtack",
                    active: pinned.value,
                };
            }
            if (a?.key === "favorite") {
                return {
                    ...a,
                    label: favorite.value ? "Enlever des favoris" : "Ajouter aux favoris",
                    tooltip: favorite.value ? "Retirer cette fiche des favoris (local)" : a.tooltip || "Ajouter aux favoris",
                    icon: favorite.value ? "fa-solid fa-star" : "fa-regular fa-star",
                    active: favorite.value,
                };
            }
            return a;
        });
    });

    function runLocalAction(actionKey) {
        if (actionKey === "pin") {
            if (!normalizedEntityType.value || !entityIdStr.value) return true;
            const now = toggleEntityPin(normalizedEntityType.value, entityIdStr.value);
            notifySuccess(now ? "Fiche épinglée" : "Épinglage retiré");
            return true;
        }
        if (actionKey === "favorite") {
            if (!normalizedEntityType.value || !entityIdStr.value) return true;
            const now = toggleEntityFavorite(normalizedEntityType.value, entityIdStr.value);
            notifySuccess(now ? "Favori ajouté" : "Favori retiré");
            return true;
        }
        return false;
    }

    return {
        pinned,
        favorite,
        resolvedActions,
        runLocalAction,
    };
}

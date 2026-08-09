/**
 * État des actions d'entités (`pin`, `favorite`).
 *
 * @description
 * Centralise libellés, icônes et bascules. Les favoris exigent une session :
 * sinon un message invite à se connecter (sans le mot « entité »).
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
    FAVORITES_AUTH_REQUIRED_MESSAGE,
    isEntityFavorite,
    toggleEntityFavorite,
    useFavoriteEntityVersion,
} from "@/Composables/entity/useFavoriteEntityIds";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

export function useResolvedEntityActionState(entityType, entity, actions) {
    const pinVersion = usePinnedEntityVersion();
    const favoriteVersion = useFavoriteEntityVersion();
    const { isAuthenticated } = usePermissions();
    const { notifySuccess, notifyInfo, notifyError } = useUxFeedback();

    const entityIdStr = computed(() => {
        const e = entity?.value ?? entity;
        const id = e?.id ?? e?._data?.id;
        if (id == null || id === "") return "";
        return String(id);
    });

    const normalizedEntityType = computed(() => String(entityType?.value ?? entityType ?? "").trim());

    const entityValue = computed(() => entity?.value ?? entity ?? null);

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
                    tooltip: pinned.value
                        ? "Fermer la fenêtre flottante"
                        : "Ouvrir en fenêtre flottante (local)",
                    icon: "fa-solid fa-thumbtack",
                    active: pinned.value,
                };
            }
            if (a?.key === "favorite") {
                const isFav = favorite.value;
                return {
                    ...a,
                    label: isFav ? "Retirer des favoris" : "Ajouter aux favoris",
                    tooltip: isAuthenticated.value
                        ? isFav
                            ? "Retirer cette fiche de vos favoris"
                            : "Ajouter cette fiche à vos favoris"
                        : FAVORITES_AUTH_REQUIRED_MESSAGE,
                    icon: isFav ? "fa-solid fa-heart" : "fa-regular fa-heart",
                    active: isFav,
                };
            }
            return a;
        });
    });

    async function runLocalAction(actionKey) {
        if (actionKey === "pin") {
            if (!normalizedEntityType.value || !entityIdStr.value) return true;
            const now = toggleEntityPin(normalizedEntityType.value, entityIdStr.value, {
                entity: entityValue.value,
            });
            notifySuccess(now ? "Fenêtre épinglée" : "Épinglage retiré");
            return true;
        }
        if (actionKey === "favorite") {
            if (!normalizedEntityType.value || !entityIdStr.value) return true;
            if (!isAuthenticated.value) {
                notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
                return true;
            }
            const result = await toggleEntityFavorite(
                normalizedEntityType.value,
                entityIdStr.value,
                { authenticated: true },
            );
            if (!result.ok) {
                if (result.reason === "auth") {
                    notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
                } else {
                    notifyError("Impossible de mettre à jour vos favoris.");
                }
                return true;
            }
            notifySuccess(result.favorited ? "Ajouté aux favoris" : "Retiré des favoris");
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

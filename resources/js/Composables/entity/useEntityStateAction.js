/**
 * État dynamique d'une entité dans la barre d'actions.
 *
 * @description
 * Centralise les libellés, couleurs et la requête de changement d'état.
 *
 * @example
 * const { currentState, updateState } = useEntityStateAction('items', entityRef, actionRef);
 */
import { computed, ref } from "vue";
import {
    getEntityStateActionLabel,
    getEntityStateDotClass,
    getEntityStateOptions,
} from "@/Utils/Entity/SharedConstants";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

function unwrapEntity(entity) {
    return entity?.value ?? entity ?? null;
}

function readRawEntity(entity) {
    const e = unwrapEntity(entity);
    return e?._data ?? e ?? null;
}

function writeEntityState(entity, state) {
    const e = unwrapEntity(entity);
    if (!e) return;
    if (e._data && typeof e._data === "object") {
        e._data.state = state;
        return;
    }
    e.state = state;
}

export function useEntityStateAction(entityType, entity, action = null) {
    const { getCsrfToken } = useBulkRequest();
    const { notifySuccess, notifyError } = useUxFeedback();
    const pending = ref(false);

    const normalizedEntityType = computed(() => normalizeActionEntityType(entityType?.value ?? entityType ?? ""));
    const entityId = computed(() => {
        const raw = readRawEntity(entity);
        const id = raw?.id ?? unwrapEntity(entity)?.id;
        return id == null || id === "" ? "" : String(id);
    });
    const currentState = computed(() => {
        const raw = readRawEntity(entity);
        return raw?.state ?? action?.value?.stateValue ?? action?.stateValue ?? null;
    });
    const label = computed(() => getEntityStateActionLabel(currentState.value));
    const dotClass = computed(() => getEntityStateDotClass(currentState.value));
    const canUpdate = computed(() => Boolean(action?.value?.canUpdateState ?? action?.canUpdateState));
    const tooltip = computed(() => {
        const suffix = canUpdate.value ? "Cliquer pour modifier." : "Lecture seule.";
        return `État : ${label.value}. ${suffix}`;
    });
    const options = computed(() =>
        getEntityStateOptions().map((option) => ({
            ...option,
            label: option.value === "playable" ? "Actif" : option.label,
            dotClass: getEntityStateDotClass(option.value),
            active: option.value === currentState.value,
        })),
    );

    async function updateState(nextState) {
        const state = String(nextState || "").trim();
        if (!state || state === currentState.value || pending.value) return false;
        if (!canUpdate.value) {
            notifyError("Vous n'avez pas les droits pour modifier cet état.");
            return false;
        }
        if (!normalizedEntityType.value || !entityId.value) return false;

        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            notifyError("Token CSRF introuvable. Recharge la page.");
            return false;
        }

        pending.value = true;
        try {
            const response = await fetch(`/api/entities/${normalizedEntityType.value}/${entityId.value}/state`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ state }),
            });
            const data = await response.json().catch(() => null);
            if (!response.ok || !data?.success) {
                notifyError(data?.message || "Erreur lors du changement d'état.");
                return false;
            }

            const updatedState = data?.entity?.state ?? state;
            writeEntityState(entity, updatedState);
            notifySuccess(`État modifié : ${getEntityStateActionLabel(updatedState)}`);
            return true;
        } catch (error) {
            notifyError("Erreur lors du changement d'état: " + (error?.message || "unknown"));
            return false;
        } finally {
            pending.value = false;
        }
    }

    return {
        canUpdate,
        currentState,
        dotClass,
        entityId,
        label,
        options,
        pending,
        tooltip,
        updateState,
    };
}

export default useEntityStateAction;

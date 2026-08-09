/**
 * Ouverture d’une fiche en modal full (hors pages Index).
 *
 * @example
 * const { modalOpen, modalEntity, modalEntityType, openHit, closeModal } = useOpenEntityModal();
 */
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import {
    canOpenEntityModal,
    fetchEntityModel,
} from "@/Utils/entity/fetchEntityModel";
import { normalizeEntityType } from "@/Entities/entity-registry";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

export function useOpenEntityModal() {
    const modalOpen = ref(false);
    const modalEntity = ref(null);
    const modalEntityType = ref("");
    const modalLoading = ref(false);

    const { notifyError } = useUxFeedback();

    function closeModal() {
        modalOpen.value = false;
        modalEntity.value = null;
        modalEntityType.value = "";
    }

    /**
     * @param {{ entityType?: string, id?: number|string, href?: string }} hit
     * @param {{ onBeforeOpen?: () => void }} [options]
     */
    async function openHit(hit, options = {}) {
        const type = normalizeEntityType(hit?.entityType || "");
        const id = hit?.id;
        if (!type || id == null || id === "") return;

        if (!canOpenEntityModal(type)) {
            if (hit?.href) {
                options.onBeforeOpen?.();
                router.visit(hit.href);
            }
            return;
        }

        modalLoading.value = true;
        try {
            const model = await fetchEntityModel(type, id);
            if (!model) {
                notifyError("Impossible d’ouvrir cette fiche.");
                return;
            }
            options.onBeforeOpen?.();
            modalEntityType.value = type;
            modalEntity.value = model;
            modalOpen.value = true;
        } catch {
            notifyError("Impossible d’ouvrir cette fiche.");
        } finally {
            modalLoading.value = false;
        }
    }

    /**
     * @param {string} entityType
     * @param {object} entity
     * @param {{ onBeforeOpen?: () => void }} [options]
     */
    function openEntity(entityType, entity, options = {}) {
        if (!entity?.id) return;
        options.onBeforeOpen?.();
        modalEntityType.value = normalizeEntityType(entityType);
        modalEntity.value = entity;
        modalOpen.value = true;
    }

    return {
        modalOpen,
        modalEntity,
        modalEntityType,
        modalLoading,
        openHit,
        openEntity,
        closeModal,
    };
}

<script setup>
/**
 * Bandeau métadonnées « Rédaction » (niveaux, audit) pour une spécialisation.
 *
 * @description
 * Affichage compact réservé aux profils autorisés à modifier l’entité ou l’ensemble
 * des spécialisations. À placer en fin de page (après sections CMS, etc.).
 *
 * @props {Object} specialization - Instance ou payload `Specialization` (méthode `toCell`).
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";

const props = defineProps({
    specialization: {
        type: Object,
        required: true,
    },
});

const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("specialization", "viewAny"),
        createAny: permissions.can("specialization", "createAny"),
        updateAny: permissions.can("specialization", "updateAny"),
        deleteAny: permissions.can("specialization", "deleteAny"),
        manageAny: permissions.can("specialization", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpecializationFieldDescriptors(ctx.value));

const canViewWriteMeta = computed(() =>
    Boolean(
        props.specialization?.can?.update
        || permissions.can("specialization", "updateAny")
        || permissions.can("specialization", "manageAny"),
    ),
);

const writeMetaFieldKeys = computed(() =>
    ["read_level", "write_level", "created_by", "created_at", "updated_at"].filter((key) =>
        Boolean(descriptors.value?.[key]),
    ),
);

const getFieldLabel = (fieldKey) => descriptors.value?.[fieldKey]?.label || fieldKey;

const getFieldIcon = (fieldKey) => descriptors.value?.[fieldKey]?.icon || "fa-solid fa-info-circle";

const getCellCompact = (fieldKey) =>
    props.specialization.toCell(fieldKey, {
        size: "xs",
        context: "extended",
    });
</script>

<template>
    <div
        v-if="canViewWriteMeta && writeMetaFieldKeys.length"
        class="rounded-box border border-base-300/50 bg-base-200/30 px-3 py-3 md:px-4"
        role="region"
        aria-label="Informations réservées à la rédaction"
    >
        <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-base-content/45">
            Rédaction
        </p>
        <div class="flex flex-wrap gap-x-3 gap-y-2">
            <div
                v-for="fieldKey in writeMetaFieldKeys"
                :key="fieldKey"
                class="inline-flex max-w-full items-center gap-1.5 rounded-lg bg-base-100/50 px-2 py-1 text-xs text-base-content/90"
            >
                <Icon
                    :source="getFieldIcon(fieldKey)"
                    :alt="getFieldLabel(fieldKey)"
                    size="xs"
                    class="shrink-0 text-base-content/50"
                />
                <span class="shrink-0 text-[10px] font-medium uppercase tracking-wide text-base-content/50">
                    {{ getFieldLabel(fieldKey) }}
                </span>
                <span class="min-w-0 text-xs wrap-break-word">
                    <CellRenderer :cell="getCellCompact(fieldKey)" ui-color="neutral" />
                </span>
            </div>
        </div>
    </div>
</template>

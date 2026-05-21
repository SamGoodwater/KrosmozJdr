<script setup>
/**
 * Bandeau métadonnées « Rédaction » pour une classe (Breed).
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
});

const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("breeds", "viewAny"),
        createAny: permissions.can("breeds", "createAny"),
        updateAny: permissions.can("breeds", "updateAny"),
        deleteAny: permissions.can("breeds", "deleteAny"),
        manageAny: permissions.can("breeds", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getBreedFieldDescriptors(ctx.value));

const canViewWriteMeta = computed(() =>
    Boolean(
        props.breed?.can?.update
        || permissions.can("breeds", "updateAny")
        || permissions.can("breeds", "manageAny"),
    ),
);

const writeMetaFieldKeys = computed(() =>
    [
        "read_level",
        "write_level",
        "auto_update",
        "dofus_version",
        "dofusdb_id",
        "official_id",
        "created_by",
        "created_at",
        "updated_at",
    ].filter((key) => Boolean(descriptors.value?.[key])),
);

const autoUpdateValue = computed(() => {
    const v = props.breed?.auto_update ?? props.breed?._data?.auto_update;
    return typeof v === "boolean" ? v : null;
});

const getFieldLabel = (fieldKey) => descriptors.value?.[fieldKey]?.label || fieldKey;

const getFieldIcon = (fieldKey) => descriptors.value?.[fieldKey]?.icon || "fa-solid fa-info-circle";

const getCellCompact = (fieldKey) =>
    props.breed.toCell(fieldKey, {
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
                <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 shrink-0" />
                <span class="uppercase tracking-wide text-base-content/55">{{ getFieldLabel(fieldKey) }}</span>
                <template v-if="fieldKey === 'auto_update'">
                    <Icon
                        v-if="autoUpdateValue !== null"
                        :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                        size="sm"
                        :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                    />
                    <span v-else>—</span>
                </template>
                <template v-else>
                    <span class="min-w-0 text-xs wrap-break-word">
                        <CellRenderer :cell="getCellCompact(fieldKey)" ui-color="neutral" />
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>

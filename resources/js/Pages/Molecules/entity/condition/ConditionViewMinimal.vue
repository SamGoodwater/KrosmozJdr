<script setup>
/**
 * ConditionViewMinimal — Vue minimal (Condition)
 *
 * @description
 * EntityMinimalCard : image, nom, dissipable ; description en déployé.
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import ConditionDissipableHighlight from "@/Pages/Molecules/entity/condition/ConditionDissipableHighlight.vue";
import ConditionMechanicalFlags from "@/Pages/Molecules/entity/condition/ConditionMechanicalFlags.vue";

const props = defineProps({
    condition: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "quick-edit",
    "delete",
    "action",
]);

const entity = computed(() => props.condition);

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "conditions",
    showRoute: "entities.conditions.show",
    editRoute: "entities.conditions.edit",
    routeParam: "condition",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const dissipableValue = computed(
    () => props.condition?.dissipable ?? props.condition?._data?.dissipable,
);

const imageUrl = computed(() => {
    const u = props.condition?.image ?? props.condition?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const displayName = computed(() => props.condition?.name || "État");

const getCell = (fieldKey) =>
    props.condition.toCell(fieldKey, { size: "sm", context: "minimal" });

const descriptionCell = computed(() => getCell("description"));
const hasDescription = computed(() => {
    const v = descriptionCell.value?.value;
    if (v == null) return false;
    return String(v).replace(/<[^>]*>/g, "").trim().length > 0;
});
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="conditions"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="flex flex-col gap-1.5 p-2">
                <div class="flex items-start gap-2">
                    <EntityThumb size="xs" :src="imageUrl || ''" :label="displayName" />
                    <div class="min-w-0 flex-1">
                        <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                        <ConditionDissipableHighlight
                            class="mt-1"
                            :dissipable="dissipableValue"
                            variant="block"
                        />
                    </div>
                </div>
            </div>
        </template>
        <template #expanded>
            <div data-cy="entity-minimal-card-expanded" class="flex flex-col gap-1.5 p-2">
                <div class="flex items-start gap-2">
                    <EntityThumb size="xs" :src="imageUrl || ''" :label="displayName" />
                    <div class="min-w-0 flex-1">
                        <div class="flex w-full min-w-0 items-start gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="conditions"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <ConditionDissipableHighlight
                            class="mt-1"
                            :dissipable="dissipableValue"
                            variant="block"
                        />
                        <ConditionMechanicalFlags
                            class="mt-1"
                            :condition="entity"
                            size="xs"
                        />
                    </div>
                </div>
                <div
                    v-if="hasDescription"
                    class="prose prose-sm prose-invert max-w-none border-t border-base-300/80 pt-1.5 text-xs leading-snug text-primary-300/90"
                >
                    <CellRenderer :cell="descriptionCell" ui-color="primary" />
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>

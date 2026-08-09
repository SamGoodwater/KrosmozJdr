<script setup>
/**
 * CreatureTraitViewMinimal — Vue minimal (CreatureTrait)
 *
 * @description
 * EntityMinimalCard : image + nom ; description en déployé.
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";

const props = defineProps({
    creatureTrait: { type: Object, required: true },
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

const entity = computed(() => props.creatureTrait);

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "creature-traits",
    showRoute: "entities.creature-traits.show",
    editRoute: "entities.creature-traits.edit",
    routeParam: "creatureTrait",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const imageUrl = computed(() => {
    const u = props.creatureTrait?.image ?? props.creatureTrait?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const displayName = computed(() => props.creatureTrait?.name || "Trait");

const getCell = (fieldKey) =>
    props.creatureTrait.toCell(fieldKey, { size: "sm", context: "minimal" });

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
        pinned-entity-type="creature-traits"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="flex gap-2 p-2">
                <EntityThumb size="xs" :src="imageUrl || ''" :label="displayName" />
                <div class="min-w-0 flex-1">
                    <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                </div>
            </div>
        </template>
        <template #expanded>
            <div data-cy="entity-minimal-card-expanded" class="flex flex-col gap-1.5 p-2">
                <div class="flex items-start gap-2">
                    <EntityThumb size="xs" :src="imageUrl || ''" :label="displayName" />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start gap-1.5">
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="creature-traits"
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

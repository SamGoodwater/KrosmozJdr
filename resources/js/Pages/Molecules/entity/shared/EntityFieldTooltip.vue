<script setup>
/**
 * Infobulle d’un champ d’entité (helper descriptor ou caractéristique BDD).
 *
 * @example
 * <EntityFieldTooltip field-key="rarity" entity-type="resource" :descriptors="descriptors" :table-meta="tableMeta">
 *   <Badge>Commun</Badge>
 * </EntityFieldTooltip>
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    fieldKey: { type: String, required: true },
    entityType: { type: String, required: true },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
});

const content = computed(() => {
    const ui = resolveEntityFieldUi({
        fieldKey: props.fieldKey,
        descriptors: props.descriptors,
        tableMeta: props.tableMeta,
        entityType: props.entityType,
    });
    return String(ui?.tooltip || "").trim();
});
</script>

<template>
    <Tooltip
        v-if="content"
        :content="content"
        placement="top"
        class="inline-flex max-w-full min-w-0"
    >
        <slot />
    </Tooltip>
    <slot v-else />
</template>

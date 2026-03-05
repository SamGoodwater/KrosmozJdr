<script setup>
/**
 * CharacteristicChip — atome d'affichage d'une caractéristique (icône + valeur).
 *
 * @description
 * Utilisé pour les rendus "chips" en tableau et cartes.
 */
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Tooltip
        :content="item.tooltip || item.label || String(item.value)"
        placement="top"
        class="inline-flex items-center gap-1"
    >
        <Icon
            v-if="item.icon"
            :source="item.icon"
            :alt="item.tooltip || item.label || ''"
            size="xs"
            class="shrink-0 opacity-80"
            :style="item.color ? { color: item.color } : undefined"
        />
        <span class="text-xs" :style="item.color ? { color: item.color } : undefined">{{ item.value }}</span>
    </Tooltip>
</template>

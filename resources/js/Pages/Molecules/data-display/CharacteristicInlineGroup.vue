<script setup>
/**
 * CharacteristicInlineGroup — molécule d'affichage inline de caractéristiques.
 *
 * @description
 * Gère le layout "chips" avec option `maxRows` (flow en colonnes).
 */
import { computed } from "vue";
import CharacteristicChip from "@/Pages/Atoms/data-display/CharacteristicChip.vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    maxRows: {
        type: Number,
        default: null,
    },
});

const chipItems = computed(() =>
    (props.items || []).filter((item) => item && item.value != null && item.value !== "")
);

const safeMaxRows = computed(() => {
    const n = Number(props.maxRows);
    return Number.isFinite(n) && n > 0 ? Math.floor(n) : null;
});

const useColumnFlow = computed(() => Boolean(safeMaxRows.value));

const containerClass = computed(() => {
    if (useColumnFlow.value) {
        return "inline-grid grid-flow-col auto-cols-max items-center content-start gap-x-2 gap-y-0.5 max-w-full overflow-x-auto overflow-y-hidden align-middle";
    }
    return "inline-flex flex-wrap items-center gap-x-2 gap-y-0.5";
});

const containerStyle = computed(() => {
    if (!useColumnFlow.value) return undefined;
    return {
        gridTemplateRows: `repeat(${safeMaxRows.value}, minmax(0, max-content))`,
    };
});
</script>

<template>
    <span :class="containerClass" :style="containerStyle">
        <CharacteristicChip
            v-for="(item, idx) in chipItems"
            :key="idx"
            :item="item"
        />
        <span v-if="!chipItems.length" class="text-base-content/40">—</span>
    </span>
</template>

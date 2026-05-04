<script setup>
/**
 * Pastille de langue (couleur + tooltip sur la description).
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    language: {
        type: Object,
        required: true,
    },
});

const hex = computed(() => {
    const c = props.language?.color;
    if (!c || typeof c !== "string") return "#64748b";
    const s = c.trim();
    return s.startsWith("#") ? s : `#${s}`;
});

const tip = computed(() => {
    const d = props.language?.description;
    return d && String(d).trim() ? String(d).trim() : "";
});
</script>

<template>
    <Tooltip
        v-if="tip"
        :content="tip"
        placement="top"
        :accent-style="{ '--color': hex }"
    >
        <span
            class="inline-flex max-w-full cursor-default items-center truncate rounded-full border px-2 py-0.5 text-xs font-medium"
            :style="{
                borderColor: hex,
                color: hex,
                backgroundColor: `${hex}22`,
            }"
        >
            {{ language.name }}
        </span>
    </Tooltip>
    <span
        v-else
        class="inline-flex max-w-full items-center truncate rounded-full border px-2 py-0.5 text-xs font-medium"
        :style="{
            borderColor: hex,
            color: hex,
            backgroundColor: `${hex}22`,
        }"
    >
        {{ language.name }}
    </span>
</template>

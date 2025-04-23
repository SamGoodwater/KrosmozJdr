<script setup>
import { computed } from "vue";
import Tooltip from "./Tooltip.vue";

const props = defineProps({
    tooltip: {
        type: [String, Object],
        default: null
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) => ["top", "right", "bottom", "left"].includes(value)
    }
});

// Détermine si le tooltip doit être affiché
const hasTooltip = computed(() => {
    return props.tooltip !== null && props.tooltip !== "";
});

// Détermine si le tooltip utilise un slot ou une chaîne de caractères
const isTooltipSlot = computed(() => {
    return typeof props.tooltip === "object";
});

// Position du tooltip avec le suffixe -center par défaut
const tooltipPlacement = computed(() => {
    return `${props.tooltipPosition}-center`;
});
</script>

<template>
    <Tooltip
        v-if="hasTooltip"
        :placement="tooltipPlacement"
    >
        <slot />
        <template #content>
            <slot v-if="isTooltipSlot" name="tooltip" />
            <span v-else>{{ tooltip }}</span>
        </template>
    </Tooltip>
    <slot v-else />
</template>

<script setup>
import { computed } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
    },
});

const buildErrorClasses = (themeProps, props) => {
    const classes = [];

    // Color
    const color = themeProps.color || 'error-600';
    classes.push(`text-${color}`);

    // Size
    const size = themeProps.size || 'sm';
    classes.push(`text-${size}`);

    // Tooltip
    if (props.tooltip) {
        classes.push('tooltip');
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildErrorClasses(themeProps.value, props));
</script>

<template>
    <div v-if="props.message" :class="getClasses">
        {{ props.message }}
    </div>
</template>

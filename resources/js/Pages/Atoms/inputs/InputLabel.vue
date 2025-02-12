<script setup>
import { computed } from 'vue';
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: '',
    },
    for: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'top',
    },
});

const buildLabelClasses = (themeProps, props) => {
    const classes = [];

    // Color
    let color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`text-${size}`);

    // Tooltip
    if (props.tooltip) {
        classes.push('tooltip');
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildLabelClasses(themeProps.value, props));
</script>

<template>
    <label :for="props.for" :class="getClasses">
        {{ props.value }}
    </label>
</template>

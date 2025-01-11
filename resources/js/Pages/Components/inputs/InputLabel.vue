<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    for: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    theme: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: '',
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

const classes = computed(() => {
    let classes = [];
    let match;

    if (props.theme) {
        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
        } else {
            classes.push('text-red-600');
        }

        // SIZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
        } else {
            classes.push('text-md');
        }
    }

    if (!['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`text-${props.size}`);
        }
    }

    if (props.tooltip) {
        classes.push('tooltip');
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
});
</script>

<template>
    <label :for="props.for" :class="classes">
        {{ props.value }}
    </label>
</template>

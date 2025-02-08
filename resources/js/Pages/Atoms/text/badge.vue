<script setup>
import { computed, defineProps } from 'vue';

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'body-900',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
    },
    outline: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'bottom',
        validator: (value) => ['', 'top', 'right', 'bottom', 'left'].includes(value),
    },
});

const getClasses = computed(() => {
    let classes = ['badge'];
    let match;
    let is_outline = false;
    let color = props.color;

    if (props.theme) {
        // Outline
        const regexOutline = /(?:^|\s)(?<capture>outline)(?:\s|$)/;
        match = regexOutline.exec(props.theme);
        if (match && match?.groups?.capture) {
            is_outline = true;
        }

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            color = match.groups.capture;
        }

        // SIZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`badge-${match.groups.capture}`);
        } else {
            classes.push(`badge-${props.size}`);
        }
    } else {
        classes.push(`badge-${props.size}`);
    }

    if (is_outline) {
        classes.push(`badge-outline`);
        classes.push(`text-${color}`);
        classes.push(`border-${color}`);
    } else {
        classes.push(`badge-${color}`);
    }

    if (props.tooltip) {
        classes.push('tooltip');
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
});
</script>

<template>
    <span :class="getClasses">
        <slot></slot>
    </span>
</template>

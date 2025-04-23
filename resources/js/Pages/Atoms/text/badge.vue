/** * Badge Component * * Props: * - theme (String): Custom theme classes for
the badge. Default is ''. If color-auto is present, the color will be
automatically set based on the color prop. * - color (String): Color of the
badge. Default is 'body-900'. * - size (String): Size of the badge. Can be '',
'xs', 'sm', 'md', 'lg'. Default is 'md'. * - outline (Boolean): If true, the
badge will have an outline style. Default is false. * - tooltip (String):
Tooltip text to display on hover. Default is ''. * - tooltipPosition (String):
Position of the tooltip. Can be '', 'top', 'right', 'bottom', 'left'. Default is
'bottom'. * * Computed: * - getClasses: Computes the classes to be applied to
the badge based on the props. */

<script setup>
import { computed } from "vue";
import { getColorFromString, adjustColorForContrast } from "@/Utils/Color.js";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    outline: {
        type: Boolean,
        default: false
    }
});

const buildBadgeClasses = (props) => {
    const classes = ["badge"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Style (outline or filled)
    if (props.outline) {
        classes.push('border-1');
        classes.push('border-solid');
        classes.push(`text-${adjustColorForContrast(props.color)}`);
        classes.push(`border-${props.color}`);
    } else {
        classes.push(`bg-${props.color}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const badgeClasses = computed(() => buildBadgeClasses(combinedProps.value));
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <span :class="badgeClasses">
            <slot />
        </span>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

<style scoped>
.badge {
    @apply inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full;
}

.badge-xs {
    @apply px-1.5 py-0.5 text-xs;
}

.badge-sm {
    @apply px-2 py-0.5 text-sm;
}

.badge-md {
    @apply px-2.5 py-1 text-base;
}

.badge-lg {
    @apply px-3 py-1.5 text-lg;
}

.badge-xl {
    @apply px-4 py-2 text-xl;
}

.badge-2xl {
    @apply px-5 py-2.5 text-2xl;
}

.badge-3xl {
    @apply px-6 py-3 text-3xl;
}
</style>

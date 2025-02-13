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
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "primary-700",
    },
    tooltip: {
        type: String,
        default: "",
    },
    size: {
        type: String,
        default: "",
        validator: (value) => {
            return ["", "xs", "sm", "md", "lg", "xl", "2xl", "3xl"].includes(value);
        },
    },
});

const buildBadgeClasses = (themeProps, props) => {
    const classes = ["badge"];

    // Size
    if(props.size) {
        classes.push(`badge-${props.size}`);
    } else if(themeProps.size) {
        classes.push(`badge-${themeProps.size}`);
    } else {
        classes.push(`badge-md`);
    }

    // Color handling
    let color = props.color;
    if (themeProps.colorAuto) {
        color = getColorFromString(props.color, 700);
    } else if (themeProps.color) {
        color = themeProps.color;
    }

    // Style (outline or filled)
    if (themeProps.styled === 'outline') {
        classes.push('border-1');
        classes.push('border-solid');
        classes.push(`text-${adjustColorForContrast(color)}`);
        classes.push(`border-${color}`);
    } else {
        classes.push(`bg-${color}`);
    }

    // Tooltip
    if (props.tooltip) {
        classes.push('tooltip');
        if (themeProps.tooltipPosition) {
            classes.push(`tooltip-${themeProps.tooltipPosition}`);
        }
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildBadgeClasses(themeProps.value, props));
</script>

<template>
    <span :class="getClasses" :data-tip="tooltip">
        <slot />
    </span>
</template>

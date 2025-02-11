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
import { computed, defineProps } from "vue";
import { getColorFromString, adjustColorForContrast } from "@/Utils/Color.js";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "primary-700",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["", "xs", "sm", "md", "lg"].includes(value),
    },
    outline: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: "",
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) =>
            ["", "top", "right", "bottom", "left"].includes(value),
    },
});

const getColor = computed(() => {

});

const getClasses = computed(() => {
    let classes = ["badge"];
    let color = "";
    let match;
    let is_outline = props.outline;

    if (props.theme) {
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

    if (props.theme) {
        const regexColorAuto = /(?:^|\s)(?<capture>color-auto)(?:\s|$)/;
        match = regexColorAuto.exec(props.theme);
        if (match && match?.groups?.capture) {
            color = getColorFromString(props.color, 700);
        }

        const regexColor =
            /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            color = match.groups.capture;
        }
    } else if (props.color) {
        color = props.color;
    } else {
        color = "primary-700";
    }

    if (props.theme) {
        // OUTLINE
        const regexOutline = /(?:^|\s)(?<capture>outline)(?:\s|$)/;
        match = regexOutline.exec(props.theme);
        if (match && match?.groups?.capture) {
            is_outline = true;
        }
    }
    if (props.outline) {
        is_outline = props.outline;
    }

    if (is_outline) {
        classes.push(`border-1`);
        classes.push(`border-solid`);
        classes.push(`text-${adjustColorForContrast(color)}`);
        classes.push(`border-${color}`);
    } else {
        classes.push(`bg-${color}`);
    }

    if (props.tooltip) {
        classes.push("tooltip");
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(" ");
});
</script>

<template>
    <span :class="getClasses" v-tooltip="props.tooltip">
        <slot />
    </span>
</template>

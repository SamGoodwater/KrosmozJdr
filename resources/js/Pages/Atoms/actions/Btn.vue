<script setup>
import { computed } from "vue";
import { isDark } from "@/Utils/Color";
import { extractTheme } from "@/Utils/extractTheme";
import Tooltip from "../feedback/Tooltip.vue";

const props = defineProps({
    theme: {
        type: String,
        default: "button",
    },
    label: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "button",
        validator: (value) =>
            ["", "button", "submit", "reset", "radio", "checkbox"].includes(value),
    },
    face: {
        type: String,
        default: "",
        validator: (value) =>
            ["", "block", "wide", "square", "circle"].includes(value),
    },
    styled: {
        type: String,
        default: "",
        validator: (value) =>
            ["", "glass", "outline", "link", "ghost"].includes(value),
    },
    color: {
        type: String,
        default: "primary",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["", "xs", "sm", "md", "lg"].includes(value),
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

const buildButtonClasses = (themeProps, props) => {
    const classes = ["btn"];

    // Style (glass, outline, link, ghost)
    if (props.styled) {
        if (props.styled === "glass") {
            classes.push("glass");
        } else {
            classes.push("btn-" + props.styled);
        }
    } else if (themeProps.styled) {
        if (themeProps.styled === "glass") {
            classes.push("glass");
        } else {
            classes.push("btn-" + themeProps.styled);
        }
    }

    // Face (block, wide, square, circle)
    if (props.face) {
        classes.push("btn-" + props.face);
    } else if (themeProps.face) {
        classes.push("btn-" + themeProps.face);
    }

    // Size
    if (props.size) {
        classes.push(`btn-${props.size}`);
    } else if (themeProps.size) {
        classes.push(`btn-${themeProps.size}`);
    } else {
        classes.push(`btn-md`);
    }

    // Color
    let color = props.color;
    if (themeProps.colorAuto) {
        color = getColorFromString(props.color);
    } else if (themeProps.color) {
        color = themeProps.color;
    }
    if (props.styled === "outline" || themeProps.styled === "outline") {
        classes.push(`border-${color}`);
        classes.push(`text-${color}`);
    } else if (props.styled === "link" || themeProps.styled === "link") {
        classes.push(`text-${color}`);
    } else {
        classes.push(`bg-${color}`);
        if (isDark(color)) {
            classes.push(`text-content-dark`);
        } else {
            classes.push(`text-content-light`);
        }
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildButtonClasses(themeProps.value, props));
</script>

<template>
    <Tooltip v-if="tooltip" :placement="tooltipPosition">

            <button
                :type="type"
                :class="[
                    getClasses,
                    {
                        tooltip: tooltip,
                        [`tooltip-${tooltipPosition}`]: tooltip && tooltipPosition,
                    }
                ]"
                :data-tip="tooltip"
            >
                <span v-if="label">{{ label }}</span>
                <slot v-else />
            </button>

        <template #content>
            <span>{{ tooltip }}</span>
        </template>
    </Tooltip>
    <button
        v-else
        :type="type"
        :class="[
            getClasses,
            {
                tooltip: tooltip,
                [`tooltip-${tooltipPosition}`]: tooltip && tooltipPosition,
            }
        ]"
        :data-tip="tooltip"
    >
        <span v-if="label">{{ label }}</span>
        <slot v-else />
    </button>
</template>

<style scoped lang="scss">
.btn-link {
    background-color: transparent;
    text-decoration: none;
    margin: 0;
    padding: 0;
    height: auto;
    min-height: auto;
    width: auto;
    min-width: auto;
    transition: filter 0.2s ease-in-out, backdrop-filter 0.2s ease-in-out,
        text-shadow 0.3s ease-in-out;

    &.btn-xs {
        font-size: 0.75rem;
    }
    &.btn-sm {
        font-size: 0.875rem;
    }
    &.btn-md {
        font-size: 1rem;
    }
    &.btn-lg {
        font-size: 1.25rem;
    }

    &:hover {
        filter: brightness(1.1);
        backdrop-filter: blur(4px);
        text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.6);
    }
}
.btn:not(.btn-link) {
    transition: filter 0.2s ease-in-out, backdrop-filter 0.3s ease-in-out,
        box-shadow 0.4s ease-in-out, text-shadow 0.3s ease-in-out;

           position: relative;
    overflow: hidden;

    &:hover {
        filter: brightness(1.1);
        backdrop-filter: blur(4px);
        text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.6);
        box-shadow:
        0 0 1px 1px rgba(255, 255, 255, 0.50),
        0 0 3px 4px rgba(255, 255, 255, 0.10),
        0 0 5px 6px rgba(255, 255, 255, 0.05),
        inset 0 0 3px 4px rgba(255, 255, 255, 0.10),
        inset 0 0 5px 6px rgba(255, 255, 255, 0.05);
    }

    &:not(.btn-outline) {
        &::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.2) 48%,
                rgba(255, 255, 255, 0.35) 50%,
                rgba(255, 255, 255, 0.2) 52%,
            );
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.5s ease;
        }
    }
    &.btn-outline {
        &::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05) 48%,
                rgba(255, 255, 255, 0.15) 50%,
                rgba(255, 255, 255, 0.05) 52%,
            );
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.5s ease;
        }
    }

    &:hover::after {
        transform: translateX(100%) rotate(45deg);
    }
}
</style>

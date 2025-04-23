<script setup>
import { computed } from "vue";
import { isDark } from "@/Utils/Color";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from "../feedback/BaseTooltip.vue";

const props = defineProps({
    ...commonProps,
    label: {
        type: String,
        default: "",
    },
    variant: {
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
    }
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const buildButtonClasses = (props) => {
    const classes = ["btn"];

    // Style (glass, outline, link, ghost)
    if (props.styled) {
        if (props.styled === "glass") {
            classes.push("glass");
        } else {
            classes.push("btn-" + props.styled);
        }
    } else if (themeProps.value.styled) {
        if (themeProps.value.styled === "glass") {
            classes.push("glass");
        } else {
            classes.push("btn-" + themeProps.value.styled);
        }
    }

    // Face (block, wide, square, circle)
    if (props.face) {
        classes.push("btn-" + props.face);
    } else if (themeProps.value.face) {
        classes.push("btn-" + themeProps.value.face);
    }

    // Size
    if (props.size) {
        classes.push(`btn-${props.size}`);
    } else if (themeProps.value.size) {
        classes.push(`btn-${themeProps.value.size}`);
    } else {
        classes.push(`btn-md`);
    }

    // Couleurs
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Gestion spéciale des couleurs pour les styles outline et link
    if (props.styled === "outline" || themeProps.value.styled === "outline") {
        classes.push(`border-${props.color || themeProps.value.color || 'primary'}`);
        classes.push(`text-${props.color || themeProps.value.color || 'primary'}`);
    } else if (props.styled === "link" || themeProps.value.styled === "link") {
        classes.push(`text-${props.color || themeProps.value.color || 'primary'}`);
    } else {
        const color = props.color || themeProps.value.color || 'primary';
        classes.push(`bg-${color}`);
        if (isDark(color)) {
            classes.push(`text-content-dark`);
        } else {
            classes.push(`text-content-light`);
        }
    }

    return classes.join(" ");
};

const getClasses = computed(() => buildButtonClasses(combinedProps.value));
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <button
            :type="variant"
            :class="getClasses"
        >difi
            <span v-if="label">{{ label }}</span>
            <slot v-else />
        </button>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
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

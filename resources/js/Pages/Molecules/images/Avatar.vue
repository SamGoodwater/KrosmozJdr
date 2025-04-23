/**
 * Avatar component that displays an image or a placeholder with initials.
 * Utilizes Atoms components for consistent styling and behavior.
 *
 * Props:
 * - theme (String): The theme of the avatar. Default is an empty string.
 * - source (String): The source URL of the avatar image. Default is an empty string.
 * - rounded (String): The border radius of the avatar. Default is "full".
 *   Valid values are "none", "sm", "md", "lg", "xl", "2xl", "3xl", "full".
 * - size (String): The size of the avatar. Default is "md".
 *   Valid values are "xs", "sm", "md", "lg", "xl", "2xl", "3xl", "full".
 * - altText (String): The alternative text to display if the image is not available. Default is an empty string.
 * - color (String): The background color of the placeholder. Default is an empty string.
 * - showTooltip (Boolean): Whether to show a tooltip when hovering over the avatar. Default is true.
 *
 * Computed:
 * - getClasses: Computes the CSS classes for the avatar based on the size, color, and rounded props.
 * - getClassParent: Computes the CSS classes for the parent container based on the rounded prop.
 * - getAltText: Computes the initials to display in the placeholder based on the altText prop.
 *
 * Data:
 * - sourceRef (ref): A reference to the source URL of the avatar image.
 * - textSize (ref): A reference to the text size class for the placeholder initials.
 *
 * Methods:
 * - imageExists: Checks if the image exists at the given URL.
 * - getColorFromString: Generates a color based on the given string.
 *
 * Template:
 * - Renders a div with the computed parent classes.
 * - If the sourceRef is available, renders an img element with the avatar image.
 * - If the sourceRef is not available, renders a span element with the computed initials.
 */
<script setup>
import { computed, ref, watch } from "vue";
import { imageExists } from "@/Utils/files";
import { getColorFromString } from "@/Utils/Color.js";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";

// Composants Atoms
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import Icon from "@/Pages/Atoms/images/Icon.vue";

const props = defineProps({
    ...commonProps,
    source: {
        type: String,
        default: "",
    },
    rounded: {
        type: String,
        default: "full",
        validator: (value) => ["none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(value),
    },
    altText: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "",
    },
    showTooltip: {
        type: Boolean,
        default: true,
    },
});

const sourceRef = ref(props.source);
const textSize = ref("text-md");

const buildAvatarClasses = (props) => {
    const classes = [
        'light:text-primary-950',
        'dark:text-primary-50',
        '!flex',
        'items-center',
        'justify-center',
        'overflow-hidden',
        'transition-all',
        'duration-200',
    ];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Size
    const size = props.size || "md";
    switch (size) {
        case "xs":
            classes.push("w-6 h-6");
            textSize.value = "text-xs";
            break;
        case "sm":
            classes.push("w-10 h-10");
            textSize.value = "text-sm";
            break;
        case "md":
            classes.push("w-16 h-16");
            textSize.value = "text-base";
            break;
        case "lg":
            classes.push("w-20 h-20");
            textSize.value = "text-lg";
            break;
        case "xl":
            classes.push("w-32 h-32");
            textSize.value = "text-2xl";
            break;
        case "2xl":
            classes.push("w-40 h-40");
            textSize.value = "text-3xl";
            break;
        case "3xl":
            classes.push("w-48 h-48");
            textSize.value = "text-4xl";
            break;
        case "full":
            classes.push("w-full h-full");
            textSize.value = "text-5xl";
            break;
    }

    // Color
    let bgColor = props.color;
    if (props.theme?.colorAuto) {
        bgColor = getColorFromString(props.altText);
    } else if (props.theme?.color) {
        bgColor = props.theme.color;
    }

    if (bgColor) {
        classes.push(`bg-${bgColor}`);
    } else {
        classes.push("bg-primary-300/20");
    }

    // Rounded
    const rounded = props.rounded || "full";
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    return classes.join(" ");
};

const buildParentClasses = (props) => {
    const classes = ['avatar', 'placeholder', 'relative'];

    const rounded = props.rounded || "full";
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildAvatarClasses(combinedProps.value));
const getClassParent = computed(() => buildParentClasses(combinedProps.value));

const getAltText = computed(() => {
    const trimmedText = props.altText.trim();
    if (trimmedText.length <= 2) {
        return trimmedText.toUpperCase();
    }

    const words = trimmedText.split(/\s+/);
    if (words.length > 1) {
        return (words[0][0] + words[words.length - 1][0]).toUpperCase();
    } else {
        return trimmedText.slice(0, 2).toUpperCase();
    }
});

watch(() => props.source, (newSource) => {
    if (newSource && imageExists(newSource)) {
        sourceRef.value = newSource;
    } else {
        sourceRef.value = "";
    }
}, { immediate: true });
</script>

<template>
    <BaseTooltip
        v-if="showTooltip && altText"
        :tooltip="altText"
        tooltip-position="bottom"
    >
        <div :class="getClassParent">
            <div v-if="sourceRef" :class="getClasses">
                <img
                    :src="sourceRef"
                    :alt="altText"
                    class="w-full h-full object-cover"
                />
            </div>
            <div v-else :class="getClasses">
                <span :class="[textSize, 'font-medium']">{{ getAltText }}</span>
            </div>
        </div>
    </BaseTooltip>
    <div v-else :class="getClassParent">
        <div v-if="sourceRef" :class="getClasses">
            <img
                :src="sourceRef"
                :alt="altText"
                class="w-full h-full object-cover"
            />
        </div>
        <div v-else :class="getClasses">
            <span :class="[textSize, 'font-medium']">{{ getAltText }}</span>
        </div>
    </div>
</template>

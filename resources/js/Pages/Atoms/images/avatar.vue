
/**
 * Avatar component that displays an image or a placeholder with initials.
 *
 * Props:
 * - source (String): The source URL of the avatar image. Default is an empty string.
 * - rounded (String): The border radius of the avatar. Default is "full".
 *   Valid values are "none", "sm", "md", "lg", "xl", "2xl", "3xl", "full".
 * - size (String): The size of the avatar. Default is "md".
 *   Valid values are "xs", "sm", "md", "lg", "xl".
 * - altText (String): The alternative text to display if the image is not available. Default is an empty string.
 * - color (String): The background color of the placeholder. Default is an empty string.
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
import { defineProps, computed, ref } from "vue";
import { imageExists } from "@/Utils/Images";
import {getColorFromString} from "@/Utils/Color.js";

const props = defineProps({
    source: {
        type: String,
        default: "",
    },
    rounded: {
        type: String,
        default: "full",
        validator: (value) =>
            ["none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(
                value,
            ),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl"].includes(value),
    },
    altText: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "",
    },
});

const sourceRef = ref("");

const textSize = ref("text-md");

const getClasses = computed(() => {
    let classes = ['light:text-primary-950','dark:text-primary-50','!flex','items-center','justify-center'];

    switch (props.size) {
        case "xs":
            classes.push("w-6");
            textSize.value = "text-sm";
            break;
        case "sm":
            classes.push("w-10");
            textSize.value = "text-md";
            break;
        case "md":
            classes.push("w-16");
            textSize.value = "text-lg";
            break;
        case "lg":
            classes.push("w-20");
            textSize.value = "text-xl";
            break;
        case "xl":
            classes.push("w-32");
            textSize.value = "text-3xl";
            break;
    }

    if(props.color) {
        classes.push("bg-"+props.color);
    } else if(props.altText) {
        classes.push("bg-"+getColorFromString(props.altText));
    } else {
        classes.push("bg-primary-500");
    }

    switch (props.rounded) {
        case "none":
            classes.push("rounded-none");
        case "sm":
            classes.push("rounded-sm");
        case "md":
            classes.push("rounded-md");
        case "lg":
            classes.push("rounded-lg");
        case "xl":
            classes.push("rounded-xl");
        case "2xl":
            classes.push("rounded-2xl");
        case "3xl":
            classes.push("rounded-3xl");
        case "full":
            classes.push("rounded-full");
    }

    return classes.join(" ");
});

const getClassParent = computed(() => {
    let classes = ['avatar','placeholder'];
    switch (props.rounded) {
        case "none":
            classes.push("rounded-none");
        case "sm":
            classes.push("rounded-sm");
        case "md":
            classes.push("rounded-md");
        case "lg":
            classes.push("rounded-lg");
        case "xl":
            classes.push("rounded-xl");
        case "2xl":
            classes.push("rounded-2xl");
        case "3xl":
            classes.push("rounded-3xl");
        case "full":
            classes.push("rounded-full");
    }
    return classes.join(" ");
});

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

if (props.source && imageExists(props.source)) {
    sourceRef.value = props.source;
} else {
    sourceRef.value = "";
}
</script>

<template>
    <div :class="getClassParent">
        <div v-if="sourceRef" :class="getClasses">
            <img :src="sourceRef" alt="avatar" />
        </div>
        <div v-else :class="getClasses">
            <span :class="[textSize]">{{ getAltText }}</span>
        </div>
    </div>
</template>

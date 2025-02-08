<script setup>
import { defineProps, computed, ref } from "vue";
import { imageExists } from "@/Utils/Images";

const props = defineProps({
    source: {
        type: String,
        default: "",
    },
    rounded: {
        type: String,
        default: "rounded-none",
        validator: (value) =>
            [
                "rounded-xs",
                "rounded-md",
                "rounded-lg",
                "rounded-xl",
                "rounded-full",
            ].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "ms", "md", "lg", "xl"].includes(value),
    },
    altText: {
        type: String,
        default: "",
    },
});

const sourceRef = ref("");

const textSize = ref("text-md");

const getClasses = computed(() => {
    let classes = ["avatar"];

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

    if (props.rounded) {
        classes.push(props.rounded);
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
    <div :class="getClasses">
        <div v-if="sourceRef" :class="getClasses">
            <img :src="sourceRef" alt="avatar" />
        </div>
        <div v-else :class="['placeholder', getClasses]">
            <div
                class="bg-primary-600 light:text-primary-950 dark:text-primary-50 !flex items-center justify-center"
            >
                <span :class="[textSize]">{{ getAltText }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useFloating, autoUpdate, flip, shift, offset } from "@floating-ui/vue";
import { ref } from "vue";

const props = defineProps({
    placement: {
        type: String,
        default: "bottom-center",
    },
    bgColor: {
        type: String,
        default: "bg-secondary-500/15",
    },
    blur: {
        type: String,
        default: "lg",
        validator: (value) =>
            ["xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    rounded: {
        type: String,
        default: "lg",
        validator: (value) =>
            ["none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(
                value,
            ),
    },
});
</script>

<template>
    <div
        :class="[
            'card shadow-sm',
            `backdrop-blur-${props.blur}`,
            `rounded-${props.rounded}`,
            props.bgColor,
        ]"
    >
        <slot />
    </div>
</template>

<style scoped></style>

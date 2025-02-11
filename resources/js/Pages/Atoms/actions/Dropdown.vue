<script setup>
import { computed, defineProps, ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    placement: {
        type: String,
        default: "",
        validator: (value) =>
            ["left", "end", "right", "", "top", "bottom", "bottom-end", "top-end", "left-end", "right-end"].includes(value),
    },
    color: {
        type: String,
        default: "base-900/80",
    },
    label: {
        type: String,
        default: "",
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === "Escape") {
        open.value = false;
    }
};

const getPlacement = computed(() => {
    let placement = [];
    if (props.placement.includes("left")) {
        placement.push("dropdown-left");
    } else if (props.placement.includes("right")) {
        placement.push("dropdown-right");
    } else if (props.placement.includes("top")) {
        placement.push("dropdown-top");
    } else if(props.placement.includes("bottom")) {
        placement.push("dropdown-bottom");
    }

    if (props.placement.includes("end")) {
        placement.push("dropdown-end");
    }

    return placement.join(" ");
});

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
</script>

<template>
    <div :class="[getPlacement, 'dropdown']">
        <div v-if="label" tabindex="0" role="button" class="btn m-1">
            {{ label }}
        </div>
        <div v-else tabindex="0" role="button">
            <slot name="label" />
        </div>
        <ul
            tabindex="0"
            :class="` backdrop-blur-2xl dropdown-content menu bg-${color}  rounded-box z-[1] w-52 p-2 shadow`"
        >
            <slot name="list" />
        </ul>
    </div>
</template>

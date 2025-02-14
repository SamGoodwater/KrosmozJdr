<script setup>
import { computed, defineProps, ref, onMounted, onUnmounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
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

const buildDropdownClasses = (themeProps, props) => {
    const classes = ["dropdown"];

    // Placement
    if (props.placement.includes("left")) {
        classes.push("dropdown-left");
    } else if (props.placement.includes("right")) {
        classes.push("dropdown-right");
    } else if (props.placement.includes("top")) {
        classes.push("dropdown-top");
    } else if(props.placement.includes("bottom")) {
        classes.push("dropdown-bottom");
    }

    if (props.placement.includes("end")) {
        classes.push("dropdown-end");
    }

    return classes.join(" ");
};

const buildDropdownContentClasses = (themeProps, props) => {
    let color = props.color;
    if (themeProps.colorAuto) {
        color = getColorFromString(props.color);
    } else if (themeProps.color) {
        color = themeProps.color;
    }

    return `backdrop-blur-2xl dropdown-content menu bg-${color} rounded-box z-[1] w-52 p-2 shadow`;
};

const closeOnEscape = (e) => {
    if (open.value && e.key === "Escape") {
        open.value = false;
    }
};

const themeProps = computed(() => extractTheme(props.theme));
const dropdownClasses = computed(() => buildDropdownClasses(themeProps.value, props));
const contentClasses = computed(() => buildDropdownContentClasses(themeProps.value, props));

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
</script>

<template>
    <div :class="dropdownClasses">
        <div v-if="label" tabindex="0" role="button" class="btn m-1">
            {{ label }}
        </div>
        <div v-else tabindex="0" role="button">
            <slot />
        </div>
        <ul tabindex="0" :class="contentClasses">
            <slot name="list" />
        </ul>
    </div>
</template>

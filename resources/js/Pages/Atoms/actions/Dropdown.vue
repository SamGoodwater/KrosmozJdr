<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from "../feedback/BaseTooltip.vue";

const props = defineProps({
    ...commonProps,
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
    }
});

const open = ref(false);

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
    const classes = ['backdrop-blur-2xl', 'dropdown-content', 'menu', 'rounded-box', 'z-[1]', 'w-52', 'p-2', 'shadow'];

    // Couleur de fond
    const color = props.color || themeProps.value.color || 'base-900/80';
    classes.push(`bg-${color}`);

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(' ');
};

const closeOnEscape = (e) => {
    if (open.value && e.key === "Escape") {
        open.value = false;
    }
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const dropdownClasses = computed(() => buildDropdownClasses(themeProps.value, props));
const contentClasses = computed(() => buildDropdownContentClasses(themeProps.value, props));

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
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
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

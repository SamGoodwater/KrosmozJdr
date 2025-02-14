<script setup>
import { useFloating, autoUpdate, flip, shift, offset } from "@floating-ui/vue";
import { ref, computed } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    placement: {
        type: String,
        default: "bottom-center",
    },
});

const referenceRef = ref(null);
const floatingRef = ref(null);
const isHidden = ref(true);

const buildTooltipClasses = (themeProps, props) => {
    const classes = ['w-max', 'absolute', 'p-2', 'px-4', 'z-50', 'left-0', 'top-0', 'rounded-lg', 'text-secondary-300'];

    // Background color and opacity
    const bgColor = themeProps.bgColor || 'secondary-900';
    const opacity = themeProps.opacity || '75';
    classes.push(`bg-${bgColor}/${opacity}`);

    // Blur
    const blur = themeProps.blur || '3';
    classes.push(`bg-blur-${blur}`);

    if (isHidden.value) {
        classes.push('hidden');
    }

    return classes.join(' ');
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildTooltipClasses(themeProps.value, props));

const placementType = [
    "top",
    "top-start",
    "top-end",
    "right",
    "right-start",
    "right-end",
    "bottom",
    "bottom-start",
    "bottom-end",
    "left",
    "left-start",
    "left-end",
];

const { floatingStyles } = useFloating(referenceRef, floatingRef, {
    whileElementsMounted: autoUpdate,
    placement: placementType.includes(props.placement)
        ? props.placement
        : "bottom-center",
    middleware: [offset(5), flip(), shift({ padding: 5 })],
});

function hideTooltips() {
    isHidden.value = true;
}
function showTooltips() {
    isHidden.value = false;
}
</script>

<template>
    <div>
        <div
            ref="referenceRef"
            @mouseenter="showTooltips"
            @mouseleave="hideTooltips"
            @focus="showTooltips"
            @blur="hideTooltips"
        >
            <slot />
        </div>
        <div
            ref="floatingRef"
            :style="floatingStyles"
            :class="getClasses"
        >
            <div>
                <slot name="content" />
            </div>
        </div>
    </div>
</template>

<style scoped></style>

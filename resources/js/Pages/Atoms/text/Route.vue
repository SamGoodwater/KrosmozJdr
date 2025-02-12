<script setup>
import { computed, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    href: {
        type: String,
        default: "#",
    },
    route: {
        type: String,
        default: "",
    },
    target: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
});

const hrefRef = ref(props.href);

const buildRouteClasses = (themeProps, props) => {
    const classes = [];

    // Tooltip
    if (props.tooltip) {
        classes.push('tooltip');
        if (themeProps.tooltipPosition) {
            classes.push(`tooltip-${themeProps.tooltipPosition}`);
        }
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildRouteClasses(themeProps.value, props));

// Update href if route is provided
if (props.route) {
    hrefRef.value = route(props.route);
}
</script>

<template>
    <Link
        :href="hrefRef"
        :target="target"
        :data-tip="tooltip"
        :class="getClasses"
    >
        <slot />
    </Link>
</template>

<style scoped></style>

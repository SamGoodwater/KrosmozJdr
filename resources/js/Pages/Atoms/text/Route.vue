<script setup>
import { computed, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
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
    }
});

const hrefRef = ref(props.href);

const buildRouteClasses = (props) => {
    const classes = [];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const routeClasses = computed(() => buildRouteClasses(combinedProps.value));

// Update href if route is provided
if (props.route) {
    hrefRef.value = route(props.route);
}
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <Link
            :href="hrefRef"
            :target="target"
            :class="routeClasses"
        >
            <slot />
        </Link>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

<style scoped>
/* Styles spécifiques au composant Route si nécessaire */
</style>

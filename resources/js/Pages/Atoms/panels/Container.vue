<script setup>
import { computed } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    fluid: {
        type: Boolean,
        default: false,
    },
    centered: {
        type: Boolean,
        default: false,
    },
    padded: {
        type: Boolean,
        default: true,
    },
    bordered: {
        type: Boolean,
        default: false,
    },
});

const buildContainerClasses = (props) => {
    const classes = ["container"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Container fluide
    if (props.fluid) {
        classes.push("container-fluid");
    }

    // Centrage
    if (props.centered) {
        classes.push("mx-auto");
    }

    // Padding
    if (props.padded) {
        classes.push("p-4");
    }

    // Bordure
    if (props.bordered) {
        classes.push("border");
    }

    // Ombre
    if (props.shadow) {
        classes.push("shadow-md");
    }

    // Coins arrondis
    if (props.rounded) {
        classes.push("rounded-lg");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildContainerClasses(combinedProps.value));
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <div :class="getClasses">
            <slot />
        </div>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

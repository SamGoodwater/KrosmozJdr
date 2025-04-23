<script setup>
import { computed } from 'vue';
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    for: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const buildLabelClasses = (props) => {
    const classes = [];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Couleur par défaut pour les labels
    if (!props.color) {
        classes.push('text-base-800');
    }

    // Taille par défaut pour les labels
    if (!props.size) {
        classes.push('text-sm');
    }

    return classes.join(' ');
};

const getClasses = computed(() => buildLabelClasses(combinedProps.value));
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <label :for="props.for" :class="getClasses">
            {{ props.value }}
        </label>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

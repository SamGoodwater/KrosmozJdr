<script setup>
import { computed } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    message: {
        type: String,
        default: "",
    },
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const buildErrorClasses = (props) => {
    const classes = [];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Couleur par défaut pour les erreurs
    if (!props.color) {
        classes.push('text-error-600');
    }

    // Taille par défaut pour les erreurs
    if (!props.size) {
        classes.push('text-sm');
    }

    return classes.join(' ');
};

const getClasses = computed(() => buildErrorClasses(combinedProps.value));
</script>

<template>
    <BaseTooltip
        v-if="props.message"
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <div :class="getClasses">
            {{ props.message }}
        </div>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

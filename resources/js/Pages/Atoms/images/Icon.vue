<script setup>
import { MediaManager } from '@/Utils/MediaManager';
import { imageExists } from '@/Utils/files';
import { computed, ref, onMounted } from 'vue';
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    source: {
        type: String,
        default: '',
    },
    alt: {
        type: String,
        default: '',
    },
    theme: {
        type: String,
        default: 'button'
    }
});

const sourceRef = ref('');
const altRef = ref('');
const isLoading = ref(true);

const buildIconClasses = (props) => {
    const classes = ['icon'];

    if (isLoading.value) {
        classes.push('animate-pulse');
    }

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(' ');
};

const initializeSource = async () => {
    isLoading.value = true;
    try {
        if (props.source) {
            sourceRef.value = await MediaManager.get(props.source, 'image');
        } else {
            sourceRef.value = await MediaManager.get('no_found', 'image');
        }

        if (!props.alt && sourceRef.value) {
            const fileName = sourceRef.value.split('/').pop().split('.').shift();
            altRef.value = fileName;
        }
    } catch (error) {
        console.error('Erreur lors du chargement de l\'icône:', error);
        sourceRef.value = await MediaManager.get('no_found', 'image');
    } finally {
        isLoading.value = false;
    }
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const iconClasses = computed(() => buildIconClasses(combinedProps.value));

onMounted(() => {
    initializeSource();
});
</script>

<template>
    <BaseTooltip
        :tooltip="tooltip"
        :tooltip-position="tooltipPosition"
    >
        <img
            :class="iconClasses"
            :src="sourceRef"
            :alt="altRef"
        />
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
</template>

<style scoped lang="scss">
.icon {
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: inline-block;

    &-xs { height: 0.75rem; }
    &-sm { height: 1rem; }
    &-md { height: 1.5rem; }
    &-lg { height: 2rem; }
    &-xl { height: 3rem; }
    &-2xl { height: 4rem; }
    &-3xl { height: 5rem; }
    &-4xl { height: 6rem; }
    &-5xl { height: 7rem; }
    &-6xl { height: 8rem; }

    &-xs, &-sm, &-md, &-lg, &-xl, &-2xl, &-3xl, &-4xl, &-5xl, &-6xl {
        width: auto;
    }
}
</style>

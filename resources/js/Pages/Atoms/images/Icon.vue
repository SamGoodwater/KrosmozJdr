<script setup>
import { MediaManager } from '@/Utils/MediaManager';
import { imageExists } from '@/Utils/files';
import { computed, ref, onMounted } from 'vue';
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
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
    },
    tooltip: {
        type: String,
        default: '',
    },
});

const sourceRef = ref('');
const altRef = ref('');
const isLoading = ref(true);

const buildIconClasses = (themeProps, props) => {
    const classes = ['icon'];

    if (isLoading.value) {
        classes.push('animate-pulse');
    }

    // Size
    const size = themeProps.size || 'md';
    classes.push(`icon-${size}`);

    // Rounded
    const rounded = themeProps.rounded || 'none';
    if (rounded !== 'none') {
        classes.push(`rounded-${rounded}`);
    }

    // Tooltip
    if (props.tooltip) {
        classes.push('tooltip');
        if (themeProps.tooltipPosition) {
            classes.push(`tooltip-${themeProps.tooltipPosition}`);
        }
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
const getClasses = computed(() => buildIconClasses(themeProps.value, props));

onMounted(() => {
    initializeSource();
});
</script>

<template>
    <img
        :class="getClasses"
        :src="sourceRef"
        :alt="altRef"
        :data-tip="tooltip"
    >
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

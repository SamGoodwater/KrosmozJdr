<script setup>
import { IconsGetter } from '@/Utils/IconsGetter';
import { imageExists } from '@/Utils/Images';
import { computed, ref } from 'vue';
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    source: {
        type: String || Array,
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

const buildIconClasses = (themeProps, props) => {
    const classes = ['icon'];

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

const initializeSource = () => {
    let source = '';
    if (props.source) {
        if (Array.isArray(props.source)) {
            source = IconsGetter.get(props.source);
        } else {
            source = props.source;
        }

        if (imageExists(source)) {
            sourceRef.value = source;
        } else {
            sourceRef.value = IconsGetter.get('icons', 'no_icon_found');
        }
    } else {
        sourceRef.value = IconsGetter.get('icons', 'no_icon_found');
    }

    if (!props.alt) {
        const fileName = source.split('/').pop().split('.').shift();
        altRef.value = fileName;
    }
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildIconClasses(themeProps.value, props));

initializeSource();
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

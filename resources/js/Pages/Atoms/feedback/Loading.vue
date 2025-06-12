<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Loading Atom (DaisyUI)
 *
 * @description
 * Composant atomique Loading conforme DaisyUI (v5.x) et Atomic Design.
 * - Affiche une animation de chargement (spinner, dots, ring, ball, bars, infinity)
 * - Props DaisyUI : type, size, color
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Slot par défaut pour accessibilité (ex: "Chargement…")
 *
 * @see https://daisyui.com/components/loading/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Loading />
 * <Loading type="dots" size="lg" color="primary" />
 * <Loading type="ring" size="sm" color="error">Chargement de l'image…</Loading>
 *
 * @props {String} type - Type d'animation ('spinner', 'dots', 'ring', 'ball', 'bars', 'infinity'), défaut 'spinner'
 * @props {String} size - Taille DaisyUI ('xs', 'sm', 'md', 'lg', 'xl'), défaut 'md'
 * @props {String} color - Couleur DaisyUI ('primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'), défaut ''
 * @slot default - Texte d'accessibilité (optionnel)
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    type: {
        type: String,
        default: 'spinner',
        validator: v => ['spinner', 'dots', 'ring', 'ball', 'bars', 'infinity'].includes(v),
    },
    size: {
        type: String,
        default: 'md',
        validator: v => sizeXlList.includes(v),
    },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'].includes(v),
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'loading',
            props.type === 'spinner' && 'loading-spinner',
            props.type === 'dots' && 'loading-dots',
            props.type === 'ring' && 'loading-ring',
            props.type === 'ball' && 'loading-ball',
            props.type === 'bars' && 'loading-bars',
            props.type === 'infinity' && 'loading-infinity',
            props.size === 'xs' && 'loading-xs',
            props.size === 'sm' && 'loading-sm',
            props.size === 'md' && 'loading-md',
            props.size === 'lg' && 'loading-lg',
            props.size === 'xl' && 'loading-xl',
            props.color === 'primary' && 'text-primary',
            props.color === 'secondary' && 'text-secondary',
            props.color === 'accent' && 'text-accent',
            props.color === 'neutral' && 'text-neutral',
            props.color === 'info' && 'text-info',
            props.color === 'success' && 'text-success',
            props.color === 'warning' && 'text-warning',
            props.color === 'error' && 'text-error',
        ].filter(Boolean),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <span :class="atomClasses" role="status" aria-live="polite" v-bind="attrs" v-on="$attrs">
            <slot>Chargement…</slot>
        </span>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped>
/* Pas de styles custom, tout est DaisyUI */
</style>

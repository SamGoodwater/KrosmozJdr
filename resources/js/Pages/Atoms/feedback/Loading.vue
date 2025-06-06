<script setup>
/**
 * Loading Atom (DaisyUI)
 *
 * @description
 * Composant atomique Loading conforme DaisyUI et Atomic Design.
 * - Affiche une animation de chargement (spinner, dots, ring, ball, bars, infinity)
 * - Props DaisyUI : type, size, color
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Slot par défaut pour accessibilité (ex: "Chargement…")
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

const props = defineProps({
    type: {
        type: String,
        default: 'spinner',
        validator: v => ['spinner', 'dots', 'ring', 'ball', 'bars', 'infinity'].includes(v),
    },
    size: {
        type: String,
        default: 'md',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'].includes(v),
    },
});

function getAtomClasses(props) {
    const classes = ['loading'];
    if (props.type === 'spinner') classes.push('loading-spinner');
    if (props.type === 'dots') classes.push('loading-dots');
    if (props.type === 'ring') classes.push('loading-ring');
    if (props.type === 'ball') classes.push('loading-ball');
    if (props.type === 'bars') classes.push('loading-bars');
    if (props.type === 'infinity') classes.push('loading-infinity');
    if (props.size === 'xs') classes.push('loading-xs');
    if (props.size === 'sm') classes.push('loading-sm');
    if (props.size === 'md') classes.push('loading-md');
    if (props.size === 'lg') classes.push('loading-lg');
    if (props.size === 'xl') classes.push('loading-xl');
    if (props.color === 'primary') classes.push('text-primary');
    if (props.color === 'secondary') classes.push('text-secondary');
    if (props.color === 'accent') classes.push('text-accent');
    if (props.color === 'neutral') classes.push('text-neutral');
    if (props.color === 'info') classes.push('text-info');
    if (props.color === 'success') classes.push('text-success');
    if (props.color === 'warning') classes.push('text-warning');
    if (props.color === 'error') classes.push('text-error');
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
</script>

<template>
    <span :class="atomClasses" role="status" aria-live="polite">
        <slot>Chargement…</slot>
    </span>
</template>

<style scoped>
/* Pas de styles custom, tout est DaisyUI */
</style>

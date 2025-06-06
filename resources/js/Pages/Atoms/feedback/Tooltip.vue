<script setup>
/**
 * Tooltip Atom (DaisyUI)
 *
 * @description
 * Composant atomique Tooltip conforme DaisyUI et Atomic Design.
 * - Slot par défaut : élément déclencheur (trigger)
 * - Slot #content : contenu complexe du tooltip (optionnel)
 * - Prop content : string simple pour le tooltip (fallback)
 * - Props : placement, color, open, responsive, + commonProps (sauf tooltip/tooltip_placement)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @example
 * <Tooltip content="Info-bulle simple">
 *   <button>Survois-moi</button>
 * </Tooltip>
 *
 * <Tooltip placement="right">
 *   <template #default>
 *     <i class="fa fa-info-circle"></i>
 *   </template>
 *   <template #content>
 *     <div>
 *       <strong>Info-bulle riche</strong>
 *       <p>Texte détaillé, <a href="#">lien</a>, etc.</p>
 *     </div>
 *   </template>
 * </Tooltip>
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps({ exclude: ['tooltip', 'tooltip_placement'] }),
    // Contenu du tooltip (string simple)
    content: {
        type: String,
        default: '',
    },
    // Placement DaisyUI : top, right, bottom, left
    placement: {
        type: String,
        default: 'top',
        validator: v => ['top', 'right', 'bottom', 'left'].includes(v),
    },
    // Couleur DaisyUI : neutral, primary, secondary, accent, info, success, warning, error
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    // Forcer l'ouverture
    open: {
        type: Boolean,
        default: false,
    },
    // Responsive (ex: lg)
    responsive: {
        type: String,
        default: '',
    },
});

// Fonction qui retourne toutes les classes DaisyUI possibles en toutes lettres
function getAtomClasses(props) {
    const classes = [];
    // Responsive
    if (props.responsive) {
        // On liste explicitement toutes les variantes possibles
        if (props.responsive === 'sm') classes.push('sm:tooltip');
        if (props.responsive === 'md') classes.push('md:tooltip');
        if (props.responsive === 'lg') classes.push('lg:tooltip');
        if (props.responsive === 'xl') classes.push('xl:tooltip');
        if (props.responsive === '2xl') classes.push('2xl:tooltip');
    } else {
        classes.push('tooltip');
    }
    // Placement
    if (props.placement === 'top') classes.push('tooltip-top');
    if (props.placement === 'right') classes.push('tooltip-right');
    if (props.placement === 'bottom') classes.push('tooltip-bottom');
    if (props.placement === 'left') classes.push('tooltip-left');
    // Couleur
    if (props.color === 'neutral') classes.push('tooltip-neutral');
    if (props.color === 'primary') classes.push('tooltip-primary');
    if (props.color === 'secondary') classes.push('tooltip-secondary');
    if (props.color === 'accent') classes.push('tooltip-accent');
    if (props.color === 'info') classes.push('tooltip-info');
    if (props.color === 'success') classes.push('tooltip-success');
    if (props.color === 'warning') classes.push('tooltip-warning');
    if (props.color === 'error') classes.push('tooltip-error');
    // Ouvert
    if (props.open) classes.push('tooltip-open');
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" :data-tip="!$slots.content ? content : undefined">
        <slot />
        <template v-if="$slots.content">
            <div class="tooltip-content" role="tooltip">
                <slot name="content" />
            </div>
        </template>
    </div>
</template>

<style scoped>
.tooltip-content {
    position: relative;
    z-index: 1;
}
</style>

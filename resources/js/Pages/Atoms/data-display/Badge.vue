<script setup>
/**
 * Badge Atom (DaisyUI)
 *
 * @description
 * Composant atomique Badge conforme DaisyUI et Atomic Design.
 * - Slot par défaut : contenu du badge (texte, nombre, icône, etc.)
 * - Prop content : texte simple à afficher (prioritaire sur slot par défaut)
 * - Slot #content : contenu HTML complexe (prioritaire sur prop content)
 * - Props DaisyUI : color, size, variant (outline, dash, soft, ghost)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Badge color="primary" content="Nouveau" />
 * <Badge color="error" size="lg" variant="outline">Erreur</Badge>
 * <Badge color="info" size="xs" variant="soft"><template #content><b>Info</b> <i>badge</i></template></Badge>
 *
 * @props {String} content - Texte simple à afficher dans le badge (optionnel, prioritaire sur slot)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl'), défaut ''
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft', 'ghost'), défaut ''
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - Contenu du badge (fallback)
 * @slot content - Contenu HTML complexe prioritaire
 */
import { computed } from "vue"
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    content: { type: String, default: '' },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'outline', 'dash', 'soft', 'ghost'].includes(v),
    },
    outline: {
        type: Boolean,
        default: false
    }
});

function getAtomClasses(props) {
    const classes = ['badge'];
    // Couleur DaisyUI
    if (props.color === 'neutral') classes.push('badge-neutral');
    if (props.color === 'primary') classes.push('badge-primary');
    if (props.color === 'secondary') classes.push('badge-secondary');
    if (props.color === 'accent') classes.push('badge-accent');
    if (props.color === 'info') classes.push('badge-info');
    if (props.color === 'success') classes.push('badge-success');
    if (props.color === 'warning') classes.push('badge-warning');
    if (props.color === 'error') classes.push('badge-error');
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('badge-xs');
    if (props.size === 'sm') classes.push('badge-sm');
    if (props.size === 'md') classes.push('badge-md');
    if (props.size === 'lg') classes.push('badge-lg');
    if (props.size === 'xl') classes.push('badge-xl');
    // Style DaisyUI
    if (props.variant === 'outline') classes.push('badge-outline');
    if (props.variant === 'dash') classes.push('badge-dash');
    if (props.variant === 'soft') classes.push('badge-soft');
    if (props.variant === 'ghost') classes.push('badge-ghost');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <span :class="atomClasses" v-bind="attrs">
            <span v-if="content && !$slots.default">{{ content }}</span>
            <slot name="content" v-else />
        </span>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

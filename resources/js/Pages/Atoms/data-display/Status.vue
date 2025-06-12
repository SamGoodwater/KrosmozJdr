<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Status Atom (DaisyUI)
 *
 * @description
 * Composant atomique Status conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <span class="status"> stylé DaisyUI
 * - Props DaisyUI : color (neutral, primary, secondary, accent, info, success, warning, error), size (xs, sm, md, lg, xl)
 * - Props d'accessibilité et HTML natif héritées de commonProps
 * - Toutes les classes DaisyUI sont écrites en toutes lettres (aucune concaténation dynamique)
 * - Tooltip intégré (hors Tooltip lui-même)
 * - Slot par défaut pour accessibilité ou animation custom (rare)
 *
 * @see https://daisyui.com/components/status/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Status color="success" size="lg" aria-label="En ligne" />
 * <Status color="error" size="md" class="animate-ping" aria-label="Erreur" />
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String|Object} tooltip, tooltip_placement, class, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - Contenu custom/accessibilité (rare)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'status',
            props.color === 'neutral' && 'status-neutral',
            props.color === 'primary' && 'status-primary',
            props.color === 'secondary' && 'status-secondary',
            props.color === 'accent' && 'status-accent',
            props.color === 'info' && 'status-info',
            props.color === 'success' && 'status-success',
            props.color === 'warning' && 'status-warning',
            props.color === 'error' && 'status-error',
            props.size === 'xs' && 'status-xs',
            props.size === 'sm' && 'status-sm',
            props.size === 'md' && 'status-md',
            props.size === 'lg' && 'status-lg',
            props.size === 'xl' && 'status-xl',
        ].filter(Boolean),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <span :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <slot />
        </span>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

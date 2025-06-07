<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Progress Atom (DaisyUI)
 *
 * @description
 * Composant atomique Progress fusionnant DaisyUI Progress et RadialProgress.
 * - Prop radial : affiche un progress radial si true, sinon progress linéaire
 * - Props ergonomiques : value, max, color, size, width, thickness, label, utilitaires custom
 * - Toutes les classes DaisyUI sont explicites (pas de concaténation dynamique)
 * - Tooltip intégré (hors Tooltip lui-même)
 * - Accessibilité : role, aria-valuenow, aria-valuemax, aria-label
 * - Slot par défaut : label (affiché au centre du radial ou à côté du progress)
 *
 * @see https://daisyui.com/components/progress/
 * @see https://daisyui.com/components/radial-progress/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Progress :value="40" color="primary" width="w-56" />
 * <Progress radial :value="75" color="primary" size="lg" label="75%" />
 * <Progress radial :value="60" color="accent" size="xl" thickness="md">60%</Progress>
 *
 * @props {Boolean} radial - Affiche un progress radial si true (défaut false)
 * @props {Number} value - Valeur du progress (0-100)
 * @props {Number} max - Valeur max (défaut 100, ignoré pour radial)
 * @props {String} color - Couleur DaisyUI (progress: 'neutral', 'primary', ... ; radial: 'primary', 'accent', ...)
 * @props {String} size - Taille du radial (xs à 4xl, mapping explicite)
 * @props {String} width - Largeur du progress linéaire (ex: 'w-56')
 * @props {String} thickness - Épaisseur du radial (xs à xl, mapping explicite)
 * @props {String} label - Label à afficher (optionnel, sinon slot)
 * @props {String|Object} tooltip, tooltip_placement, class, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @slot default - Label custom (affiché au centre du radial ou à côté du progress)
 * @slot tooltip - Tooltip custom
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const colorMap = {
    neutral: 'progress-neutral',
    primary: 'progress-primary',
    secondary: 'progress-secondary',
    accent: 'progress-accent',
    info: 'progress-info',
    success: 'progress-success',
    warning: 'progress-warning',
    error: 'progress-error',
};
const radialColorMap = {
    neutral: 'text-neutral',
    primary: 'text-primary',
    secondary: 'text-secondary',
    accent: 'text-accent',
    info: 'text-info',
    success: 'text-success',
    warning: 'text-warning',
    error: 'text-error',
};
const sizeMap = {
    xs: 'w-8 h-8',
    sm: 'w-12 h-12',
    md: 'w-16 h-16',
    lg: 'w-20 h-20',
    xl: 'w-24 h-24',
    '2xl': 'w-32 h-32',
    '3xl': 'w-40 h-40',
    '4xl': 'w-56 h-56',
};
const thicknessMap = {
    xs: 'border',
    sm: 'border-2',
    md: 'border-4',
    lg: 'border-8',
    xl: 'border-[10px]',
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    radial: { type: Boolean, default: false },
    value: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl'].includes(v),
    },
    width: { type: String, default: '' }, // progress: w-56
    thickness: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    label: { type: String, default: '' },
    class: { type: String, default: '' },
});

const progressClasses = computed(() =>
    mergeClasses(
        [
            'progress',
            props.color && colorMap[props.color],
            props.width,
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const radialClasses = computed(() =>
    mergeClasses(
        [
            'radial-progress',
            props.color && radialColorMap[props.color],
            props.size && sizeMap[props.size],
            props.thickness && thicknessMap[props.thickness],
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    role: 'progressbar',
    'aria-valuenow': props.value,
    'aria-valuemax': 100,
    'aria-label': props.ariaLabel || 'Progression',
}));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <template v-if="!props.radial">
            <div class="flex items-center gap-2">
                <progress :class="progressClasses" :value="props.value" :max="props.max" v-bind="attrs" v-on="$attrs" />
                <span v-if="props.label || $slots.default">
                    <slot>{{ props.label }}</slot>
                </span>
            </div>
        </template>
        <template v-else>
            <div :class="radialClasses" :style="`--value:${props.value};`" v-bind="attrs" v-on="$attrs">
                <span v-if="props.label || $slots.default"
                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-base font-semibold">
                    <slot>{{ props.label }}</slot>
                </span>
            </div>
        </template>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

<script setup>
/**
 * Label Atom (DaisyUI)
 *
 * @description
 * Composant atomique Label conforme DaisyUI et Atomic Design.
 * - Props DaisyUI : floating (active le floating label), size, color
 * - Props : for (id du champ), value (texte du label), position (start/end), tooltip, tooltip_placement, id, ariaLabel, role, tabindex, disabled
 * - Slot par défaut : contenu HTML du label (prioritaire sur value)
 * - Toutes les classes DaisyUI sont explicites
 * - Support label avant/après input, floating label, label pour select, etc.
 *
 * @example
 * <Label for="email" value="Email" />
 * <Label for="password" floating size="lg">Mot de passe</Label>
 * <Label for="url" position="start">https://</Label>
 * <Label for="domain" position="end">.com</Label>
 *
 * @props {String} for - id du champ associé (optionnel)
 * @props {String} value - texte du label (optionnel, prioritaire sur slot)
 * @props {Boolean} floating - active le floating label (DaisyUI)
 * @props {String} size - xs, sm, md, lg, xl
 * @props {String} color - primary, secondary, accent, info, success, warning, error, neutral, base-100, base-200, base-300
 * @props {String} position - start (avant input), end (après input), défaut 'start'
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex, disabled - hérités de commonProps
 * @slot default - contenu HTML du label (prioritaire sur value)
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atom/atomManager';

const sizeMap = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl',
};
const colorMap = {
    primary: 'text-primary',
    secondary: 'text-secondary',
    accent: 'text-accent',
    info: 'text-info',
    success: 'text-success',
    warning: 'text-warning',
    error: 'text-error',
    neutral: 'text-neutral',
    'base-100': 'text-base-100',
    'base-200': 'text-base-200',
    'base-300': 'text-base-300',
};

const props = defineProps({
    ...getCommonProps(),
    for: { type: String, default: '' },
    value: { type: String, default: '' },
    floating: { type: Boolean, default: false },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error', 'neutral', 'base-100', 'base-200', 'base-300'].includes(v),
    },
    position: {
        type: String,
        default: 'start',
        validator: v => ['start', 'end'].includes(v),
    },
});

function getLabelClasses(props) {
    const classes = ['label'];
    if (props.floating) classes.push('floating-label');
    if (props.size && sizeMap[props.size]) classes.push(sizeMap[props.size]);
    if (props.color && colorMap[props.color]) classes.push(colorMap[props.color]);
    return classes.join(' ');
}

const labelClasses = computed(() => getLabelClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <label :for="props.for || undefined" :class="labelClasses" v-bind="attrs">
            <span class="label-text">
                <slot>
                    {{ value }}
                </slot>
            </span>
        </label>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

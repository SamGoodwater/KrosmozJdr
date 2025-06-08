<script setup>
defineOptions({ inheritAttrs: false });

/**
 * FileInputAtom (DaisyUI)
 *
 * @description
 * Atomique input file stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <input type="file"> stylé DaisyUI
 * - Props DaisyUI : color, size, variant (ghost)
 * - Props utilitaires custom : shadow, backdrop, opacity, rounded
 * - Props communes : accessibilité, tooltip, etc.
 * - mergeClasses pour la composition des classes
 * - getCommonAttrs pour les attributs HTML/accessibilité
 * - Tooltip intégré (hors Tooltip lui-même)
 * - AUCUNE logique de drag&drop, preview, progress, label, etc. (géré par la molécule)
 *
 * @see https://daisyui.com/components/file-input/
 * @version DaisyUI v5.x
 *
 * @example
 * <FileInputAtom color="primary" size="md" />
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, tooltip, tooltip_placement, class, style, disabled - hérités de commonProps
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps({ exclude: ['modelValue', 'label', 'errorMessage', 'validator', 'help', 'theme'] }),
    ...getCustomUtilityProps(),
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
        validator: v => ['', 'ghost'].includes(v),
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'file-input',
            props.color === 'neutral' && 'file-input-neutral',
            props.color === 'primary' && 'file-input-primary',
            props.color === 'secondary' && 'file-input-secondary',
            props.color === 'accent' && 'file-input-accent',
            props.color === 'info' && 'file-input-info',
            props.color === 'success' && 'file-input-success',
            props.color === 'warning' && 'file-input-warning',
            props.color === 'error' && 'file-input-error',
            props.size === 'xs' && 'file-input-xs',
            props.size === 'sm' && 'file-input-sm',
            props.size === 'md' && 'file-input-md',
            props.size === 'lg' && 'file-input-lg',
            props.size === 'xl' && 'file-input-xl',
            props.variant === 'ghost' && 'file-input-ghost',
            'w-full',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
}));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <input type="file" :class="atomClasses" v-bind="attrs" v-on="$attrs" />
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

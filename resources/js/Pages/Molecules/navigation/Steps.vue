<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Steps Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Steps stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <ul class="steps"> stylé DaisyUI
 * - Props : direction (vertical/horizontal), responsive (ex: 'vertical lg:horizontal'), customUtility, accessibilité, etc.
 * - Slot par défaut pour les StepItem (ou <li> custom)
 * - mergeClasses pour les classes DaisyUI explicites (steps, steps-vertical, steps-horizontal, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Supporte les utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/steps/
 *
 * @example
 * <Steps direction="horizontal">
 *   <StepItem color="primary" active>Register</StepItem>
 *   <StepItem color="primary">Choose plan</StepItem>
 *   <StepItem>Purchase</StepItem>
 *   <StepItem>Receive Product</StepItem>
 * </Steps>
 *
 * @props {String} direction - Direction du steps ('vertical', 'horizontal'), défaut 'vertical'
 * @props {String} responsive - Responsive DaisyUI (ex: 'vertical lg:horizontal')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Items du steps (StepItem, <li>, etc.)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    direction: {
        type: String,
        default: 'vertical',
        validator: v => ['vertical', 'horizontal'].includes(v),
    },
    responsive: { type: String, default: '' },
});

const moleculeClasses = computed(() => {
    const base = ['steps'];
    if (props.responsive) {
        // ex: 'vertical lg:horizontal' => ['steps-vertical', 'lg:steps-horizontal']
        base.push(...props.responsive.split(' ').map(dir => dir === 'vertical' ? 'steps-vertical' : dir === 'horizontal' ? 'steps-horizontal' : dir.replace(':', ':steps-')));
    } else {
        base.push(props.direction === 'vertical' ? 'steps-vertical' : 'steps-horizontal');
    }
    base.push('bg-base-100', 'shadow-sm', 'backdrop-blur-sm', props.class);
    return mergeClasses(base, getCustomUtilityClasses(props));
});
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <ul :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </ul>
</template>

<style scoped></style>

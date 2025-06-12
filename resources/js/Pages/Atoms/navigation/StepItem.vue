<script setup>
defineOptions({ inheritAttrs: false });

/**
 * StepItem Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Atomique item de step stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <li class="step"> stylé DaisyUI
 * - Props : active, color (neutral, primary, secondary, accent, info, success, warning, error), icon (string), state (état custom), customUtility, accessibilité, etc.
 * - Slot icon prioritaire sur prop icon, slot default pour le label/contenu
 * - mergeClasses pour les classes DaisyUI explicites (step, step-primary, etc.)
 * - Utilise l'atom Icon pour l'icône si besoin
 * - Si slot icon ou prop icon, rend <span class="step-icon">...</span>
 * - getCommonAttrs pour l'accessibilité
 *
 * @see https://daisyui.com/components/steps/
 *
 * @example
 * <StepItem color="primary" active>Register</StepItem>
 * <StepItem color="success"><template #icon>✔️</template>Valider</StepItem>
 *
 * @props {Boolean} active - Met l'item en état actif (applique la couleur)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} icon - Nom logique ou chemin de l'icône (optionnel, sinon slot #icon)
 * @props {String} state - État custom (optionnel, pour variantes avancées)
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot icon - Slot pour l'icône (prioritaire sur prop icon)
 * @slot default - Label ou contenu du step
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    active: { type: Boolean, default: false },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    icon: { type: String, default: '' },
    state: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'step',
            props.active && props.color && `step-${props.color}`,
            !props.active && props.state && `step-${props.state}`,
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <li :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <span v-if="$slots.icon || icon" class="step-icon">
            <slot name="icon">
                <Icon v-if="icon" :source="icon" :alt="'icon'" size="md" />
            </slot>
        </span>
        <slot />
    </li>
</template>

<style scoped></style>

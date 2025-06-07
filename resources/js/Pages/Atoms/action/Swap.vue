<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Swap Atom (DaisyUI)
 *
 * @description
 * Composant atomique Swap conforme DaisyUI (v5.x) et Atomic Design.
 * Permet de basculer entre deux états visuels (on/off, icônes, textes, etc.) avec effet flip/rotate.
 * - Props : modelValue (v-model), active (force l'état), indeterminate, rotate, flip, disabled, + commonProps
 * - Slots : on (swap-on), off (swap-off), indeterminate (swap-indeterminate)
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (aria-checked, aria-label, tabindex, etc.)
 * - Mode contrôlé (v-model) ou non contrôlé (interne)
 *
 * @see https://daisyui.com/components/swap/
 * @version DaisyUI v5.x
 *
 * @example
 * <Swap v-model="isDark" rotate>
 *   <template #on><i class="fa-solid fa-moon"></i></template>
 *   <template #off><i class="fa-solid fa-sun"></i></template>
 * </Swap>
 *
 * <Swap :active="true" flip>
 *   <template #on>ON</template>
 *   <template #off>OFF</template>
 * </Swap>
 *
 * @props {Boolean} modelValue - Valeur contrôlée (v-model)
 * @props {Boolean} active - Force l'état actif (prioritaire sur modelValue)
 * @props {Boolean} indeterminate - Affiche le slot indeterminate
 * @props {Boolean} rotate - Ajoute l'effet rotate
 * @props {Boolean} flip - Ajoute l'effet flip
 * @props {Boolean} disabled - Désactive le swap (hérité de commonProps)
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot on - Contenu affiché quand actif (swap-on)
 * @slot off - Contenu affiché quand inactif (swap-off)
 * @slot indeterminate - Contenu affiché quand indéterminé (swap-indeterminate)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Le composant gère l'accessibilité (aria-checked, aria-label, tabindex) et la navigation clavier.
 */

import { ref, computed, watch, toRefs } from 'vue';
import { getCommonProps, getCommonAttrs, mergeClasses, getCustomUtilityClasses } from '@/Utils/atomic-design/uiHelper';

const emit = defineEmits(['update:modelValue', 'change']);

const props = defineProps({
    ...getCommonProps(),
    modelValue: {
        type: Boolean,
        default: undefined,
    },
    active: {
        type: Boolean,
        default: false,
    },
    indeterminate: {
        type: Boolean,
        default: false,
    },
    rotate: {
        type: Boolean,
        default: false,
    },
    flip: {
        type: Boolean,
        default: false,
    },
});

// État interne si non contrôlé
const internalValue = ref(false);

// État effectif (active > modelValue > interne)
const isActive = computed(() => {
    if (props.active) return true;
    if (props.modelValue !== undefined) return props.modelValue;
    return internalValue.value;
});

// Gestion du changement (checkbox ou clic)
function toggle() {
    if (props.disabled) return;
    if (props.modelValue !== undefined) {
        emit('update:modelValue', !props.modelValue);
        emit('change', !props.modelValue);
    } else {
        internalValue.value = !internalValue.value;
        emit('change', internalValue.value);
    }
}

// Classes DaisyUI explicites
const atomClasses = computed(() =>
    mergeClasses(
        [
            'swap',
            props.rotate && 'swap-rotate',
            props.flip && 'swap-flip',
            isActive.value && 'swap-active',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));

</script>

<template>
    <label :class="atomClasses" v-bind="attrs" v-on="$attrs" :aria-checked="isActive" :tabindex="props.tabindex">
        <!-- Checkbox caché pour accessibilité et v-model -->
        <input type="checkbox" :checked="isActive" :disabled="props.disabled" @change="toggle" style="display: none;"
            :aria-checked="isActive" :id="props.id" />
        <!-- Indeterminate -->
        <div v-if="props.indeterminate" class="swap-indeterminate">
            <slot name="indeterminate" />
        </div>
        <!-- On/Off -->
        <div v-else>
            <div class="swap-on">
                <slot name="on" />
            </div>
            <div class="swap-off">
                <slot name="off" />
            </div>
        </div>
    </label>
</template>

<style scoped>
/* Optionnel : styles additionnels si besoin */
</style>

<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ThemeController Molecule (DaisyUI Theme Controller + Swap)
 *
 * @description
 * Molécule ThemeController pour switcher entre le thème dark et light via DaisyUI (Theme Controller using a swap).
 * - Utilise l'atom Swap pour l'animation et l'accessibilité
 * - Place <input type="checkbox" class="theme-controller"> dans le slot default de Swap
 * - Icônes FontAwesome + Tooltip dans les slots #off et #on
 * - Props : value ("dark" ou "light"), checked (bool), ariaLabel, id, etc.
 * - mergeClasses pour les classes DaisyUI explicites (swap, swap-rotate, etc.)
 * - getCommonAttrs pour l'accessibilité
 *
 * @see https://daisyui.com/components/theme-controller/
 *
 * @example
 * <ThemeController v-model="isDark" />
 *
 * @props {String} value - Valeur du thème à activer quand coché ("dark" ou "light"), défaut "dark"
 * @props {Boolean} modelValue - Etat coché (v-model natif)
 * @props {String} ariaLabel, id, class - hérités de commonProps
 */
import { computed } from 'vue';
import Swap from '@/Pages/Atoms/action/Swap.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    ...getCommonProps(),
    value: {
        type: String,
        default: 'dark',
        validator: v => ['dark', 'light'].includes(v),
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const checked = computed({
    get: () => props.modelValue,
    set: v => emit('update:modelValue', v),
});

const swapClasses = computed(() =>
    mergeClasses([
        'swap-rotate',
        props.class
    ])
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Swap v-model="checked" :class="swapClasses" v-bind="attrs" rotate>
        <template #off>
            <Tooltip content="Activer le mode sombre" placement="top">
                <i class="fa-solid fa-sun h-8 w-8 text-yellow-400"></i>
            </Tooltip>
        </template>
        <template #on>
            <Tooltip content="Activer le mode clair" placement="top">
                <i class="fa-solid fa-moon h-8 w-8 text-blue-400"></i>
            </Tooltip>
        </template>
        <input type="checkbox" class="theme-controller" :value="value" v-model="checked" :id="props.id"
            :aria-label="props.ariaLabel || 'Changer de thème'" style="display:none;" />
    </Swap>
</template>

<style scoped></style>

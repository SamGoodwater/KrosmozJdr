<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Navbar Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Atomique barre de navigation stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <div class="navbar"> stylé DaisyUI
 * - Props : customUtility, accessibilité, etc.
 * - mergeClasses pour les classes DaisyUI explicites (navbar, bg-base-100, shadow-sm, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Slots nommés : start, center, end (pour placer du contenu au début, au centre, à la fin)
 * - Pas de Tooltip
 *
 * @see https://daisyui.com/components/navbar/
 *
 * @example
 * <Navbar>
 *   <template #start><Logo /></template>
 *   <template #center><Menu /></template>
 *   <template #end><UserMenu /></template>
 * </Navbar>
 *
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot start - Contenu à gauche (début)
 * @slot center - Contenu centré
 * @slot end - Contenu à droite (fin)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'navbar',
            'shadow-sm',
            'bd-blur-sm',
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <div class="navbar-start">
            <slot name="start" />
        </div>
        <div class="navbar-center">
            <slot name="center" />
        </div>
        <div class="navbar-end">
            <slot name="end" />
        </div>
    </div>
</template>

<style scoped></style>

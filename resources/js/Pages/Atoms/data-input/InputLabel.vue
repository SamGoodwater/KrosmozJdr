<script setup>
/**
 * InputLabel Atom (DaisyUI universel)
 *
 * @description
 * Composant atomique InputLabel conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <label> stylé DaisyUI, positionnable partout (top, bottom, left, right, floating)
 * - Props DaisyUI : position, floating-label, label, color, size, placement explicites
 * - Props custom : for, value, floating, class
 * - Accessibilité : ariaLabel, role, tabindex, id, for
 * - Slot par défaut : contenu du label (texte, HTML, etc.)
 *
 * @see https://daisyui.com/components/label/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputLabel for="email" value="Email" position="top" />
 * <InputLabel floating color="primary">Nom</InputLabel>
 *
 * @props {String} for - Attribut for du label (id du champ associé)
 * @props {String} value - Texte du label (optionnel, sinon slot)
 * @props {String} position - Position du label (top, bottom, left, right, floating)
 * @props {Boolean} floating - Active le mode floating-label DaisyUI
 * @props {String} color - Couleur DaisyUI ('', 'primary', 'secondary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du label (texte, HTML, etc.)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
 import { computed, ref, watch, onMounted, useSlots } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

// Maps pour les classes DaisyUI
const positionMap = {
    top: 'label',
    bottom: 'label',
    left: 'label',
    right: 'label',
    floating: 'floating-label',
};
const colorMap = {
    neutral: 'label-neutral',
    primary: 'label-primary',
    secondary: 'label-secondary',
    accent: 'label-accent',
    info: 'label-info',
    success: 'label-success',
    warning: 'label-warning',
    error: 'label-error',
};
const sizeMap = {
    xs: 'label-xs',
    sm: 'label-sm',
    md: 'label-md',
    lg: 'label-lg',
    xl: 'label-xl',
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    value: { type: String, default: '' },
    for: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'label',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <label :for="props.for" :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot>{{ props.value }}</slot>
    </label>
</template>

<style scoped></style>

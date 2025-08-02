<script setup>
/**
 * InputLabel Atom (DaisyUI universel)
 *
 * @description
 * Composant atomique InputLabel conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <label> stylé DaisyUI, positionnable partout (top, bottom, left, right, floating)
 * - Props DaisyUI : color, size avec validation explicite
 * - Props custom : for, value, class
 * - Accessibilité : ariaLabel, role, tabindex, id, for
 * - Slot par défaut : contenu du label (texte, HTML, etc.)
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/label/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputLabel for="email" value="Email" />
 * <InputLabel for="name" color="primary" size="lg">Nom complet</InputLabel>
 * <InputLabel for="password" class="font-bold">
 *   <i class="fa-solid fa-lock"></i> Mot de passe
 * </InputLabel>
 *
 * @props {String} for - Attribut for du label (id du champ associé)
 * @props {String} value - Texte du label (optionnel, sinon slot)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - Contenu du label (texte, HTML, etc.)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
 import { computed, ref, watch, onMounted, useSlots } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

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
    color: { type: String, default: '', validator: (v) => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v) },
    size: { type: String, default: '', validator: (v) => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v) },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'label',
            props.color && colorMap[props.color],
            props.size && sizeMap[props.size],
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <label :for="props.for" :class="atomClasses" v-bind="attrs" tabindex="-1" v-on="$attrs">
        <slot>{{ props.value }}</slot>
    </label>
</template>

<style scoped></style>

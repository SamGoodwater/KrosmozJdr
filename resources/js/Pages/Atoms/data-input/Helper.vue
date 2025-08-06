<script setup>
/**
 * Helper Atom (DaisyUI universel)
 *
 * @description
 * Composant atomique Helper conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un message d'aide stylé DaisyUI, positionnable partout
 * - Props DaisyUI : color, size avec validation explicite
 * - Props custom : value, helper, class
 * - Accessibilité : ariaLabel, role, tabindex, id
 * - Slot par défaut : contenu du helper (texte, HTML, etc.)
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Compatible avec le nouveau système de style unifié
 *
 * @see https://daisyui.com/components/
 * @version DaisyUI v5.x
 *
 * @example
 * <Helper value="Votre mot de passe doit contenir au moins 8 caractères" />
 * <Helper helper="Message d'aide" color="primary" size="sm">
 *   <i class="fa-solid fa-info-circle"></i>
 *   Cliquez pour plus d'informations
 * </Helper>
 * <Helper helper="Texte d'aide" color="info" size="lg">
 *   Texte d'aide avec couleur info et taille large
 * </Helper>
 * 
 * // Avec objet helper
 * <Helper :helper="{ message: 'Message', icon: 'fa-solid fa-info' }" color="primary" size="sm">
 *   Message d'aide avec icône
 * </Helper>
 *
 * @props {String} value - Texte du helper (optionnel, sinon slot)
 * @props {String|Object} helper - Message d'aide (string ou objet avec message, icon)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} icon - Icône FontAwesome (optionnel)
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - Contenu du helper (texte, HTML, etc.)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
 import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const colorMap = {
    neutral: 'color-neutral',
    primary: 'color-primary',
    secondary: 'color-secondary',
    accent: 'color-accent',
    info: 'color-info',
    success: 'color-success',
    warning: 'color-warning',
    error: 'color-error',
};

const sizeMap = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl',
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    value: { type: String, default: '' },
    helper: { type: [String, Object], default: '' },
    color: { type: String, default: 'neutral', validator: (v) => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v) },
    size: { type: String, default: 'sm', validator: (v) => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v) },
    icon: { type: String, default: '' },
});

// Gestion de l'objet helper
const helperConfig = computed(() => {
    if (typeof props.helper === 'object' && props.helper !== null) {
        return {
            message: props.helper.message || props.value,
            icon: props.helper.icon || props.icon,
        };
    }
    return {
        message: props.helper || props.value,
        icon: props.icon,
    };
});

// Couleur effective (priorité : props.color > helper.color > défaut)
const effectiveColor = computed(() => {
    return props.color || 'neutral';
});

// Taille effective (priorité : props.size > helper.size > défaut)
const effectiveSize = computed(() => {
    return props.size || 'sm';
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            props.value || props.helper ? 'helper' : 'helper-hint',
            effectiveColor.value && colorMap[effectiveColor.value],
            effectiveSize.value && sizeMap[effectiveSize.value],
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <i v-if="helperConfig.icon" :class="helperConfig.icon" class="mr-1"></i>
        <slot>{{ helperConfig.message }}</slot>
    </div>
</template>

<style scoped lang="scss">
.helper-hint {
    display: none;
}
.helper {
    color: color-mix(in oklab, var(--color) 40%, rgba(255, 255, 255, 0.8));
}
</style> 
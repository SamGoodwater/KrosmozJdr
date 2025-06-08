<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Menu Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Menu stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <ul class="menu"> stylé DaisyUI
 * - Props : direction (vertical/horizontal), size, customUtility, accessibilité, etc.
 * - Slot par défaut pour les items (utilisation de MenuItem.vue possible)
 * - mergeClasses pour les classes DaisyUI explicites (menu, menu-vertical, menu-horizontal, menu-xs, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Supporte les utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/menu/
 *
 * @example
 * <Menu direction="vertical" size="md">
 *   <MenuItem icon="fa-home" active>Accueil</MenuItem>
 *   <MenuItem icon="fa-user">Profil</MenuItem>
 *   <li><details open><summary>Parent</summary><ul><MenuItem>Enfant</MenuItem></ul></details></li>
 * </Menu>
 *
 * @props {String} direction - Direction du menu ('vertical', 'horizontal'), défaut 'vertical'
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Items du menu (MenuItem, <li>, etc.)
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
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'menu',
            props.direction === 'vertical' && 'menu-vertical',
            props.direction === 'horizontal' && 'menu-horizontal',
            props.size === 'xs' && 'menu-xs',
            props.size === 'sm' && 'menu-sm',
            props.size === 'md' && 'menu-md',
            props.size === 'lg' && 'menu-lg',
            props.size === 'xl' && 'menu-xl',
            'bg-base-200',
            'rounded-box',
            'w-56',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <ul :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </ul>
</template>

<style scoped></style>

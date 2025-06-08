<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Tab Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Tabs stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <div class="tabs"> stylé DaisyUI
 * - Props : variant (box, border, lift, ''), placement (top, bottom), size (xs, sm, md, lg, xl), customUtility, accessibilité, etc.
 * - Slot par défaut pour les TabItem (ou <a>/<button> custom)
 * - mergeClasses pour les classes DaisyUI explicites (tabs, tabs-box, tabs-border, tabs-lift, tabs-top, tabs-bottom, tabs-xs, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Supporte les utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/tab/
 *
 * @example
 * <Tab variant="lift" size="md">
 *   <TabItem active icon="fa-user" label="Profil">Contenu du tab</TabItem>
 *   <TabItem icon="fa-cog" label="Paramètres">Contenu</TabItem>
 * </Tab>
 *
 * @props {String} variant - Style DaisyUI ('', 'box', 'border', 'lift')
 * @props {String} placement - Position des tabs ('top', 'bottom'), défaut 'top'
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Items du tabs (TabItem, <a>, <button>, etc.)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'box', 'border', 'lift'].includes(v),
    },
    placement: {
        type: String,
        default: 'top',
        validator: v => ['top', 'bottom'].includes(v),
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
            'tabs',
            props.variant === 'box' && 'tabs-box',
            props.variant === 'border' && 'tabs-border',
            props.variant === 'lift' && 'tabs-lift',
            props.placement === 'top' && 'tabs-top',
            props.placement === 'bottom' && 'tabs-bottom',
            props.size === 'xs' && 'tabs-xs',
            props.size === 'sm' && 'tabs-sm',
            props.size === 'md' && 'tabs-md',
            props.size === 'lg' && 'tabs-lg',
            props.size === 'xl' && 'tabs-xl',
            'bg-base-100',
            'shadow-sm',
            'backdrop-blur-sm',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div role="tablist" :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>

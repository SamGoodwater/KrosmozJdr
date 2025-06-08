<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Dock Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Dock stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <div class="dock"> stylé DaisyUI
 * - Props : size (xs, sm, md, lg, xl), customUtility, accessibilité, etc.
 * - Slot par défaut pour les DockItem (ou <li>/<button> custom)
 * - mergeClasses pour les classes DaisyUI explicites (dock, dock-xs, etc.)
 * - getCommonAttrs pour l'accessibilité
 * - Supporte les utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/dock/
 *
 * @example
 * <Dock size="md" shadow="md">
 *   <DockItem icon="fa-home" label="Accueil" active route="home" />
 *   <DockItem icon="fa-inbox" label="Messages" route="inbox" />
 *   <DockItem icon="fa-cog" label="Paramètres" route="settings" />
 * </Dock>
 *
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Items du dock (DockItem, <li>, etc.)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'dock',
            props.size === 'xs' && 'dock-xs',
            props.size === 'sm' && 'dock-sm',
            props.size === 'md' && 'dock-md',
            props.size === 'lg' && 'dock-lg',
            props.size === 'xl' && 'dock-xl',
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
    <div :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>

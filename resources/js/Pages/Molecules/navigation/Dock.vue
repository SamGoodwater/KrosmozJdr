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
            'dock-custom',
            props.size === 'xs' && 'dock-xs',
            props.size === 'sm' && 'dock-sm',
            props.size === 'md' && 'dock-md',
            props.size === 'lg' && 'dock-lg',
            props.size === 'xl' && 'dock-xl',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <ul :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </ul>
</template>

<style scoped lang="scss">
.dock-custom {
    right: calc(0.25rem * 0);
    bottom: calc(0.25rem * 0);
    left: calc(0.25rem * 0);
    z-index: 1;
    display: flex;
    width: 100%;
    flex-direction: row;
    align-items: center;
    justify-content: space-around;
    padding: calc(0.25rem * 2);
    list-style: none;

    &>* {
        text-decoration: none;
        position: relative;
        margin-bottom: calc(0.25rem * 2);
        display: flex;
        height: 100%;
        max-width: calc(0.25rem * 32);
        flex-shrink: 1;
        flex-basis: 100%;
        cursor: pointer;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1px;
        border-radius: var(--radius-box);
        background-color: transparent;
        transition: opacity 0.2s ease-out;
    }
}
</style>

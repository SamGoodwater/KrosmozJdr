<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Kbd Atom (DaisyUI)
 *
 * @description
 * Composant atomique Kbd conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <kbd> stylé DaisyUI
 * - Slot par défaut : contenu du raccourci clavier (texte, icône, etc.)
 * - Props DaisyUI : size (xs, sm, md, lg, xl)
 * - Props custom : class, shadow, backdrop, opacity
 * - Accessibilité : ariaLabel, role, tabindex, id, tooltip, etc.
 * - Tooltip intégré (hors Tooltip lui-même)
 *
 * @see https://daisyui.com/components/kbd/
 * @version DaisyUI v5.x
 *
 * @example
 * <Kbd size="sm" tooltip="Copier">Ctrl + C</Kbd>
 * <Kbd size="lg">F5</Kbd>
 *
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - accessibilité
 * @slot default - Contenu du raccourci clavier
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'kbd',
            props.size === 'xs' && 'kbd-xs',
            props.size === 'sm' && 'kbd-sm',
            props.size === 'md' && 'kbd-md',
            props.size === 'lg' && 'kbd-lg',
            props.size === 'xl' && 'kbd-xl',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <kbd :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <slot />
        </kbd>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

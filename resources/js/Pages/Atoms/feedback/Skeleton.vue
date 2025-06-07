<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Skeleton Atom (DaisyUI)
 *
 * @description
 * Composant atomique Skeleton conforme DaisyUI (v5.x) et Atomic Design.
 * - Props ergonomiques : element (image, text, avatar, smalltext, longtext), size (xs à 4xl), width, height (classes tailwind)
 * - Mapping explicite des classes DaisyUI/Tailwind selon element/size
 * - Utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Accessibilité : aria-busy, aria-label, role, tabindex, id
 * - Slot par défaut : contenu imbriqué (rare)
 *
 * @see https://daisyui.com/components/skeleton/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Skeleton element="avatar" size="lg" />
 * <Skeleton element="text" size="md" width="w-40" />
 * <Skeleton width="w-full" height="h-8" class="mb-2" />
 *
 * @props {String} element - Type de skeleton ('', 'image', 'text', 'avatar', 'smalltext', 'longtext')
 * @props {String} size - Taille ('', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl')
 * @props {String} width - Classe Tailwind w-* (optionnel, prioritaire)
 * @props {String} height - Classe Tailwind h-* (optionnel, prioritaire)
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu imbriqué (rare)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const sizeMap = {
    xs: { h: 'h-2', w: 'w-8', rounded: 'rounded' },
    sm: { h: 'h-3', w: 'w-16', rounded: 'rounded' },
    md: { h: 'h-4', w: 'w-32', rounded: 'rounded' },
    lg: { h: 'h-6', w: 'w-48', rounded: 'rounded' },
    xl: { h: 'h-8', w: 'w-64', rounded: 'rounded' },
    '2xl': { h: 'h-12', w: 'w-80', rounded: 'rounded-lg' },
    '3xl': { h: 'h-16', w: 'w-96', rounded: 'rounded-lg' },
    '4xl': { h: 'h-24', w: 'w-128', rounded: 'rounded-xl' },
};

const elementMap = {
    avatar: { h: 'h-12', w: 'w-12', rounded: 'rounded-full' },
    image: { h: 'h-32', w: 'w-32', rounded: 'rounded-lg' },
    text: { h: 'h-4', w: 'w-20', rounded: 'rounded' },
    smalltext: { h: 'h-3', w: 'w-16', rounded: 'rounded' },
    longtext: { h: 'h-4', w: 'w-full', rounded: 'rounded' },
};

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    element: {
        type: String,
        default: '',
        validator: v => ['', 'image', 'text', 'avatar', 'smalltext', 'longtext'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl'].includes(v),
    },
    width: { type: String, default: '' },
    height: { type: String, default: '' },
    class: { type: String, default: '' },
});

const base = computed(() => {
    // Si element ET size => fusionne les deux (size prioritaire sur h/w, sauf rounded avatar/image)
    let h = '', w = '', rounded = '';
    if (props.element && elementMap[props.element]) {
        h = elementMap[props.element].h;
        w = elementMap[props.element].w;
        rounded = elementMap[props.element].rounded;
    }
    if (props.size && sizeMap[props.size]) {
        h = sizeMap[props.size].h;
        w = sizeMap[props.size].w;
        // Pour avatar/image, on garde le rounded spécifique
        if (!['avatar', 'image'].includes(props.element)) {
            rounded = sizeMap[props.size].rounded;
        }
    }
    // width/height props écrasent tout
    if (props.width) w = props.width;
    if (props.height) h = props.height;
    return [h, w, rounded].filter(Boolean);
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'skeleton',
            ...base.value,
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    'aria-busy': 'true',
    'aria-label': props.ariaLabel || 'Chargement...'
}));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>

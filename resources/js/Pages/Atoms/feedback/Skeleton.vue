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
import { elementSkeletonMap, sizeSkeletonMap } from './feedbackMap.js';
import { size4XlList } from '@/Pages/Atoms/atomMap';


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
        validator: v => size4XlList.includes(v),
    },
    width: { type: String, default: '' },
    height: { type: String, default: '' },
    class: { type: String, default: '' },
});

const base = computed(() => {
    // Si element ET size => fusionne les deux (size prioritaire sur h/w, sauf rounded avatar/image)
    let h = '', w = '', rounded = '';
    if (props.element && elementSkeletonMap[props.element]) {
        h = elementSkeletonMap[props.element].h;
        w = elementSkeletonMap[props.element].w;
        rounded = elementSkeletonMap[props.element].rounded;
    }
    if (props.size && sizeSkeletonMap[props.size]) {
        h = sizeSkeletonMap[props.size].h;
        w = sizeSkeletonMap[props.size].w;
        // Pour avatar/image, on garde le rounded spécifique
        if (!['avatar', 'image'].includes(props.element)) {
            rounded = sizeSkeletonMap[props.size].rounded;
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

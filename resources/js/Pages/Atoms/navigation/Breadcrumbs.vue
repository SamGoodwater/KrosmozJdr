<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Breadcrumbs Atom (DaisyUI)
 *
 * @description
 * Composant atomique Breadcrumbs conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <div class="breadcrumbs"><ul>...</ul></div> stylé DaisyUI
 * - Slot par défaut pour les <li> (l'utilisateur gère les <a> ou <span> DaisyUI)
 * - Props ergonomiques : maxWidth (max-w-*), small (text-sm), utilitaires custom, accessibilité
 * - Props d'accessibilité et HTML natif héritées de commonProps
 * - Toutes les classes DaisyUI sont écrites en toutes lettres (aucune concaténation dynamique)
 *
 * @see https://daisyui.com/components/breadcrumbs/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Breadcrumbs maxWidth="max-w-xs" small>
 *   <li><a>Home</a></li>
 *   <li><a>Documents</a></li>
 *   <li>Add Document</li>
 * </Breadcrumbs>
 *
 * @props {String} maxWidth - Classe Tailwind max-w-* (optionnel)
 * @props {Boolean} small - Applique text-sm (défaut false)
 * @props {String} class, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - <li> (l'utilisateur gère les <a> ou <span> DaisyUI)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    maxWidth: { type: String, default: '' }, // ex: 'max-w-xs'
    small: { type: Boolean, default: false },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'breadcrumbs',
            props.small && 'text-sm',
            props.maxWidth,
        ].filter(Boolean),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>

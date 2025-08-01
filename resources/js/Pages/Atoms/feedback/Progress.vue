<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Progress Atom (DaisyUI)
 *
 * @description
 * Composant atomique Progress fusionnant DaisyUI Progress et RadialProgress.
 * - Prop radial : affiche un progress radial si true, sinon progress linéaire
 * - Props ergonomiques : value, max, color, size, width, thickness, label, utilitaires custom
 * - Toutes les classes DaisyUI sont explicites (pas de concaténation dynamique)
 * - Accessibilité : role, aria-valuenow, aria-valuemax, aria-label
 * - Slot par défaut : label (affiché au centre du radial ou à côté du progress)
 *
 * @see https://daisyui.com/components/progress/
 * @see https://daisyui.com/components/radial-progress/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Progress :value="40" color="primary" width="w-56" />
 * <Progress radial :value="75" color="primary" size="lg" label="75%" />
 * <Progress radial :value="60" color="accent" size="xl" thickness="md">60%</Progress>
 *
 * @props {Boolean} radial - Affiche un progress radial si true (défaut false)
 * @props {Number} value - Valeur du progress (0-100)
 * @props {Number} max - Valeur max (défaut 100, ignoré pour radial)
 * @props {String} color - Couleur DaisyUI (progress: 'neutral', 'primary', ... ; radial: 'primary', 'accent', ...)
 * @props {String} size - Taille du radial (xs à 4xl, mapping explicite)
 * @props {String} width - Largeur du progress linéaire (ex: 'w-56')
 * @props {String} thickness - Épaisseur du radial (xs à xl, mapping explicite)
 * @props {String} label - Label à afficher (optionnel, sinon slot)
 * @props {String|Object} class, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @slot default - Label custom (affiché au centre du radial ou à côté du progress)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorProgressMap, colorRadialMap, sizeRadialMap, thicknessRadialMap } from './feedbackMap.js';
import { colorList, sizeXlList, size4XlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    radial: { type: Boolean, default: false },
    value: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => size4XlList.includes(v),
    },
    width: { type: String, default: '' }, // progress: w-56
    thickness: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    label: { type: String, default: '' },
    class: { type: String, default: '' },
});

const progressClasses = computed(() =>
    mergeClasses(
        [
            'progress',
            props.color && colorProgressMap[props.color],
            props.width,
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const radialClasses = computed(() =>
    mergeClasses(
        [
            'radial-progress',
            props.color && colorRadialMap[props.color],
            props.size && sizeRadialMap[props.size],
            props.thickness && thicknessRadialMap[props.thickness],
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    role: 'progressbar',
    'aria-valuenow': props.value,
    'aria-valuemax': 100,
    'aria-label': props.ariaLabel || 'Progression',
}));
</script>

<template>
    <progress v-if="!radial" :class="progressClasses" :value="props.value" :max="props.max" v-bind="attrs" v-on="$attrs" />
    <div v-else :class="radialClasses" :style="`--value:${props.value};`" v-bind="attrs" v-on="$attrs">
        <slot />
    </div>
</template>

<style scoped></style>

<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Filter Atom (DaisyUI)
 *
 * @description
 * Composant atomique Filter conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <form class="filter"> ou <div class="filter"> stylé DaisyUI selon la prop 'form'
 * - Slot par défaut : radios (groupe de boutons radio DaisyUI)
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props d'accessibilité et HTML natif héritées de commonProps
 * - Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind
 * - Tooltip intégré (hors Tooltip lui-même)
 *
 * @see https://daisyui.com/components/filter/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Filter :form="true">
 *   <input class="btn btn-square" type="reset" value="×" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="Svelte" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="Vue" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="React" />
 * </Filter>
 *
 * <Filter>
 *   <input class="btn filter-reset" type="radio" name="metaframeworks" aria-label="All" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Sveltekit" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Nuxt" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Next.js" />
 * </Filter>
 *
 * @props {Boolean} form - Utilise <form> si true, sinon <div> (défaut false)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Radios DaisyUI (input type="radio" ou reset)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 */
import { computed, h, resolveDynamicComponent } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    form: { type: Boolean, default: false },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'filter',
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
const tag = computed(() => props.form ? 'form' : 'div');
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <component :is="tag" :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <slot />
        </component>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

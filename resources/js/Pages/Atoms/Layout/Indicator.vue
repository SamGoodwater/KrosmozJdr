<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Indicator Atom (DaisyUI)
 *
 * @description
 * Composant atomique Indicator conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <div class="indicator"> stylé DaisyUI
 * - Slot #indicator : contenu de l'indicateur (badge, status, etc.), positionné selon les props
 * - Slot par défaut : contenu principal (bouton, avatar, etc.)
 * - Props DaisyUI : horizontal (start, center, end), vertical (top, middle, bottom) pour le placement de l'indicateur
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props d'accessibilité et HTML natif héritées de commonProps
 * - Toutes les classes DaisyUI sont écrites en toutes lettres (aucune concaténation dynamique)
 * - Tooltip intégré (hors Tooltip lui-même)
 *
 * @see https://daisyui.com/components/indicator/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Indicator horizontal="end" vertical="top" shadow="md">
 *   <template #indicator>
 *     <span class="indicator-item badge badge-primary">New</span>
 *   </template>
 *   <button class="btn">Inbox</button>
 * </Indicator>
 *
 * @props {String} horizontal - Placement horizontal DaisyUI ('', 'start', 'center', 'end')
 * @props {String} vertical - Placement vertical DaisyUI ('', 'top', 'middle', 'bottom')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom ('' | 'xs' | ...)
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - hérités de commonProps
 * @slot indicator - Contenu de l'indicateur (badge, status, etc.), positionné selon les props
 * @slot default - Contenu principal (bouton, avatar, etc.)
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    horizontal: {
        type: String,
        default: '',
        validator: v => ['', 'start', 'center', 'end'].includes(v),
    },
    vertical: {
        type: String,
        default: '',
        validator: v => ['', 'top', 'middle', 'bottom'].includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'indicator',
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);

const indicatorItemClasses = computed(() => {
    const classes = ['indicator-item'];
    if (props.horizontal === 'start') classes.push('indicator-start');
    if (props.horizontal === 'center') classes.push('indicator-center');
    if (props.horizontal === 'end') classes.push('indicator-end');
    if (props.vertical === 'top') classes.push('indicator-top');
    if (props.vertical === 'middle') classes.push('indicator-middle');
    if (props.vertical === 'bottom') classes.push('indicator-bottom');
    return classes.join(' ');
});

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <template v-if="$slots.indicator">
                <span :class="indicatorItemClasses">
                    <slot name="indicator" />
                </span>
            </template>
            <slot />
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

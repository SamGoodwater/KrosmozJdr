<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Toast Atom (DaisyUI)
 *
 * @description
 * Composant atomique Toast conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <div class="toast"> stylé DaisyUI
 * - Props DaisyUI : horizontal (start/center/end), vertical (top/middle/bottom)
 * - Utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Accessibilité : ariaLabel, role, tabindex, id
 * - Slot par défaut : contenu du toast (souvent des <Alert>)
 *
 * @see https://daisyui.com/components/toast/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Toast horizontal="end" vertical="top" shadow="md">
 *   <Alert color="info">Nouveau message !</Alert>
 * </Toast>
 *
 * @props {String} horizontal - Placement horizontal ('', 'start', 'center', 'end')
 * @props {String} vertical - Placement vertical ('', 'top', 'middle', 'bottom')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du toast (souvent des <Alert>)
 */
import { computed } from 'vue';
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
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'toast',
            props.horizontal === 'start' && 'toast-start',
            props.horizontal === 'center' && 'toast-center',
            props.horizontal === 'end' && 'toast-end',
            props.vertical === 'top' && 'toast-top',
            props.vertical === 'middle' && 'toast-middle',
            props.vertical === 'bottom' && 'toast-bottom',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
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

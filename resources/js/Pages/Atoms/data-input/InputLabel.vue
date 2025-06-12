<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * InputLabel Atom (DaisyUI)
 *
 * @description
 * Composant atomique InputLabel conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <label> ou <span class="label"> stylé DaisyUI
 * - Props DaisyUI : floating-label, label, color, size, placement explicites
 * - Props custom : for, value, floating, class
 * - Accessibilité : ariaLabel, role, tabindex, id, for
 * - Slot par défaut : contenu du label (texte, HTML, etc.)
 *
 * @see https://daisyui.com/components/label/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputLabel for="email" value="Email" />
 * <InputLabel floating color="primary">Nom</InputLabel>
 *
 * @props {String} for - Attribut for du label (id du champ associé)
 * @props {String} value - Texte du label (optionnel, sinon slot)
 * @props {Boolean} floating - Active le mode floating-label DaisyUI
 * @props {String} color - Couleur DaisyUI ('', 'primary', 'secondary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Contenu du label (texte, HTML, etc.)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atomic-design/uiHelper';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';
import { colorMap, sizeMap } from './data-inputMap';

const props = defineProps({
    ...getCommonProps(),
    for: { type: String, default: '' },
    value: { type: String, default: '' },
    floating: { type: Boolean, default: false },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    position: {
        type: String,
        default: 'start',
        validator: v => ['start', 'end'].includes(v),
    },
});

function getAtomClasses(props) {
    return mergeClasses(
        ['label',
            props.floating && 'floating-label',
            props.size && sizeMap[props.size],
            props.color && colorMap[props.color],
        ].filter(Boolean),
    );
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <label :for="props.for || undefined" :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <span class="label-text">
                <slot>
                    {{ value }}
                </slot>
            </span>
        </label>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>

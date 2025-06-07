<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Row Atom (DaisyUI ListRow)
 *
 * @description
 * Composant atomique Row (ListRow) conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <li class="list-row"> ou <div class="list-row"> stylé DaisyUI
 * - Slot par défaut : colonnes de la ligne (divers contenus)
 * - Props DaisyUI : list-row, list-col-grow, list-col-wrap (appliqués explicitement)
 * - Utilitaires : shadow, rounded, backdrop, opacity (via customUtility)
 * - Props custom : as, grow, wrap, class
 * - Accessibilité : ariaLabel, role, tabindex, id, tooltip, etc.
 *
 * @see https://daisyui.com/components/list/
 * @version DaisyUI v5.x
 *
 * @example
 * <Row as="li" :grow="2">
 *   <div>Col 1</div>
 *   <div>Col 2 (grandit)</div>
 *   <div>Col 3</div>
 * </Row>
 *
 * @props {String} as - Balise racine ('li' ou 'div', défaut 'li')
 * @props {Number} grow - Index (1-based) de la colonne à faire grandir (ajoute list-col-grow)
 * @props {Number} wrap - Index (1-based) de la colonne à wrap (ajoute list-col-wrap)
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, rounded, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot default - Colonnes de la ligne
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed, h, resolveDynamicComponent } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    as: {
        type: String,
        default: 'li',
        validator: v => ['li', 'div'].includes(v),
    },
    grow: {
        type: Number,
        default: 2, // Par défaut, la 2e colonne grandit (cf DaisyUI)
    },
    wrap: {
        type: Number,
        default: 0, // 0 = aucun wrap
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() => {
    return mergeClasses(
        ['list-row'],
        getCustomUtilityClasses(props),
        props.class
    );
});

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <component :is="props.as" :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <template v-for="(col, idx) in $slots.default ? $slots.default() : []" :key="idx">
            <div :class="{
                'list-col-grow': props.grow === idx + 1,
                'list-col-wrap': props.wrap === idx + 1
            }">
                <slot :name="col.props?.name" v-bind="col.props || {}">{{ col }}</slot>
            </div>
        </template>
    </component>
</template>

<style scoped></style>

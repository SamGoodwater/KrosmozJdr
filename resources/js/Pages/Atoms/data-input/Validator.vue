<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Validator Atom (DaisyUI)
 *
 * @description
 * Composant atomique Validator-hint conforme DaisyUI (v5.x) et Atomic Design.
 * - Affiche un message d'aide/erreur sous un champ input avec la classe DaisyUI validator-hint
 * - Props : state (error, success, warning, info, ''), message (texte du hint, prioritaire sur slot), visible (booléen), class (custom), id, ariaLabel, role, tabindex
 * - Slot par défaut : contenu HTML du hint (prioritaire sur message)
 * - Toutes les classes DaisyUI sont explicites (validator-hint + text-error, text-success, etc.)
 *
 * @see https://daisyui.com/components/validator/
 * @version DaisyUI v5.x
 *
 * @example
 * <Validator state="error" message="Ce champ est requis" />
 * <Validator state="success">Champ valide !</Validator>
 * <Validator :visible="false" message="Masqué" />
 *
 * @props {String} state - error, success, warning, info, '' (défaut '')
 * @props {String} message - texte du hint (optionnel, prioritaire sur slot)
 * @props {Boolean} visible - contrôle l'affichage (défaut true)
 * @props {String} class - classes custom (optionnel)
 * @props {String} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - contenu HTML du hint (prioritaire sur message)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atomic-design/atomManager';
import { stateList, stateMap } from './data-inputMap';
import { mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    state: {
        type: String,
        default: '',
        validator: v => stateList.includes(v),
    },
    message: { type: String, default: '' },
    visible: { type: Boolean, default: true },
    class: { type: String, default: '' },
});

function getAtomClasses(props) {
    const classes = ['validator-hint'];
    if (props.state && stateMap[props.state]) classes.push(stateMap[props.state]);
    if (!props.visible) classes.push('hidden');
    return mergeClasses(classes, props.class);
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot>
            {{ message }}
        </slot>
    </div>
</template>

<style scoped></style>

<script setup>
/**
 * Validator Atom (DaisyUI)
 *
 * @description
 * Composant atomique Validator-hint conforme DaisyUI et Atomic Design.
 * - Affiche un message d'aide/erreur sous un champ input avec la classe DaisyUI validator-hint
 * - Props : state (error, success, warning, info, ''), message (texte du hint, prioritaire sur slot), visible (booléen), class (custom), id, ariaLabel, role, tabindex
 * - Slot par défaut : contenu HTML du hint (prioritaire sur message)
 * - Toutes les classes DaisyUI sont explicites (validator-hint + text-error, text-success, etc.)
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
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs } from '@/Utils/atom/atomManager';

const stateMap = {
    error: 'text-error',
    success: 'text-success',
    warning: 'text-warning',
    info: 'text-info',
    '': '',
};

const props = defineProps({
    ...getCommonProps(),
    state: {
        type: String,
        default: '',
        validator: v => ['', 'error', 'success', 'warning', 'info'].includes(v),
    },
    message: { type: String, default: '' },
    visible: { type: Boolean, default: true },
    class: { type: String, default: '' },
});

function getHintClasses(props) {
    const classes = ['validator-hint'];
    if (props.state && stateMap[props.state]) classes.push(stateMap[props.state]);
    if (!props.visible) classes.push('hidden');
    if (props.class) classes.push(props.class);
    return classes.join(' ');
}

const hintClasses = computed(() => getHintClasses(props));
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="hintClasses" v-bind="attrs">
        <slot>
            {{ message }}
        </slot>
    </div>
</template>

<style scoped></style>

<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Validator Atom (DaisyUI)
 *
 * @description
 * Composant atomique Validator-hint conforme DaisyUI (v5.x) et Atomic Design.
 * - Affiche un message de validation sous un champ input avec la classe DaisyUI validator-hint
 * - Props : state (error, success, warning, info, ''), message (texte du hint, prioritaire sur slot), visible (booléen), class (custom), id, ariaLabel, role, tabindex
 * - Slot par défaut : contenu HTML du hint (prioritaire sur message)
 * - Toutes les classes DaisyUI sont explicites (validator-hint + text-error, text-success, etc.)
 * - Compatible avec le nouveau système de validation unifié
 *
 * @see https://daisyui.com/components/validator/
 * @version DaisyUI v5.x
 *
 * @example
 * <Validator state="error" message="Ce champ est requis" />
 * <Validator state="success">Champ valide !</Validator>
 * <Validator :visible="false" message="Masqué" />
 * 
 * // Avec objet validation
 * <Validator :validation="{ state: 'error', message: 'Email invalide' }" />
 * <Validator :validation="{ state: 'success', message: 'Email valide !' }" />
 *
 * @props {String} state - error, success, warning, info, '' (défaut '')
 * @props {String} message - texte du hint (optionnel, prioritaire sur slot)
 * @props {Object} validation - objet de validation { state, message } (prioritaire sur state/message)
 * @props {Boolean} visible - contrôle l'affichage (défaut true)
 * @props {String} class - classes custom (optionnel)
 * @props {String} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - contenu HTML du hint (prioritaire sur message)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from "vue";
import { getCommonProps, getCommonAttrs } from "@/Utils/atomic-design/uiHelper";
import { stateList, stateMap } from "./data-inputMap";
import { mergeClasses } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    state: {
        type: String,
        default: "",
        validator: (v) => stateList.includes(v),
    },
    message: { type: String, default: "" },
    validation: { type: Object, default: null },
    visible: { type: Boolean, default: true },
    class: { type: String, default: "" },
});

// Configuration de validation (priorité : validation > state/message)
const validationConfig = computed(() => {
    if (props.validation && typeof props.validation === 'object') {
        return {
            state: props.validation.state || '',
            message: props.validation.message || '',
        };
    }
    return {
        state: props.state,
        message: props.message,
    };
});

function getAtomClasses() {
    const classes = ["validator-hint"];
    const state = validationConfig.value.state;
    
    if (state && stateMap[state]) {
        classes.push(stateMap[state]);
    }
    
    if (!props.visible) {
        classes.push("hidden");
    }
    
    return mergeClasses(classes, props.class);
}

const atomClasses = computed(() => getAtomClasses());
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <slot>
            {{ validationConfig.message }}
        </slot>
    </div>
</template>

<style scoped></style>

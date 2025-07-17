/**
 * atomManager.js — Gestionnaire des props et attributs pour les atoms
 *
 * @description
 * Module centralisé pour la gestion des props et attributs communs aux atoms.
 * Fournit des helpers pour générer les props et attributs HTML des composants atomiques.
 * 
 * Les helpers de validation sont dans validationManager.js
 * Les helpers de labels sont dans labelManager.js
 *
 * @example
 * import { getInputProps, getInputAttrs, hasValidation } from '@/Utils/atomic-design/atomManager';
 * 
 * // Props pour un input
 * const props = defineProps({
 *   ...getInputProps(),
 *   // props spécifiques
 * });
 * 
 * // Attributs HTML
 * const attrs = getInputAttrs(props);
 */

/**
 * Props communes à tous les boutons (Btn, Link, etc.)
 * @param {Object} options - Options de configuration
 * @param {Array} options.exclude - Props à exclure
 * @returns {Object} - Props communes pour les boutons
 */
export function getButtonProps({ exclude = [] } = {}) {
    const allButtonProps = {
        type: { type: String, default: 'button', validator: (v) => ['button', 'submit', 'reset'].includes(v) },
        disabled: { type: Boolean, default: false },
        loading: { type: Boolean, default: false },
        href: { type: String, default: '' },
        target: { type: String, default: '' },
        rel: { type: String, default: '' },
    };
    
    return Object.fromEntries(
        Object.entries(allButtonProps).filter(([key]) => !exclude.includes(key)),
    );
}

/**
 * Attributs HTML pour les boutons
 * @param {Object} props - Props du composant
 * @returns {Object} - Attributs HTML pour les boutons
 */
export function getButtonAttrs(props) {
    return {
        type: props.type || undefined,
        disabled: props.disabled || undefined,
        href: props.href || undefined,
        target: props.target || undefined,
        rel: props.rel || undefined,
    };
}

/**
 * Props communes à tous les éléments de formulaire (checkbox, radio, select, etc.)
 * @param {Object} options - Options de configuration
 * @param {Array} options.exclude - Props à exclure
 * @returns {Object} - Props communes pour les éléments de formulaire
 */
export function getFormElementProps({ exclude = [] } = {}) {
    const allFormElementProps = {
        modelValue: {
            type: [String, Number, Boolean, Array, Object],
            default: "",
        },
        name: { type: String, default: "" },
        required: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        autocomplete: { type: String, default: "" },
        autofocus: { type: Boolean, default: false },
    };
    
    return Object.fromEntries(
        Object.entries(allFormElementProps).filter(([key]) => !exclude.includes(key)),
    );
}

/**
 * Attributs HTML pour les éléments de formulaire
 * @param {Object} props - Props du composant
 * @returns {Object} - Attributs HTML pour les éléments de formulaire
 */
export function getFormElementAttrs(props) {
    return {
        name: props.name || undefined,
        required: props.required || undefined,
        disabled: props.disabled || undefined,
        readonly: props.readonly || undefined,
        autocomplete: props.autocomplete || undefined,
        autofocus: props.autofocus || undefined,
    };
}

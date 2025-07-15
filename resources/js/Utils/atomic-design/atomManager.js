/**
 * Props communes à tous les inputs (input, select, textarea, checkbox, radio, range, etc.)
 * Utiliser dans chaque atom input :
 *   ...getInputProps()
 * Pour exclure certaines props :
 *   ...getInputProps({ exclude: ['modelValue', ...] })
 */
export function getInputProps({ exclude = [] } = {}) {
    const allInputProps = {
        modelValue: {
            type: [String, Number, Boolean, Array, Object],
            default: "",
        },
        name: { type: String, default: "" },
        placeholder: { type: String, default: "" },
        required: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
        autocomplete: { type: String, default: "" },
        autofocus: { type: Boolean, default: false },
        min: { type: [String, Number], default: "" },
        max: { type: [String, Number], default: "" },
        step: { type: [String, Number], default: "" },
        inputmode: { type: String, default: "" },
        pattern: { type: String, default: "" },
        maxlength: { type: [String, Number], default: "" },
        minlength: { type: [String, Number], default: "" },
        label: { type: String, default: "" },
        errorMessage: { type: String, default: "" },
        validator: { type: [Boolean, String, Object], default: true },
        helper: { type: String, default: "" },
    };
    return Object.fromEntries(
        Object.entries(allInputProps).filter(([key]) => !exclude.includes(key)),
    );
}

/**
 * Détermine si un composant doit afficher un état de validation
 * @param {Object} props - Props du composant
 * @param {Object} slots - Slots du composant (optionnel)
 * @returns {Boolean} - True si validation à afficher
 */
export function hasValidation(props, slots = {}) {
    // Si validator est une string non vide (ex: "error", "success")
    if (typeof props.validator === 'string' && props.validator.trim() !== '') {
        return true;
    }
    
    // Si errorMessage est une string non vide
    if (typeof props.errorMessage === 'string' && props.errorMessage.trim() !== '') {
        return true;
    }
    
    // Si un slot validator est fourni
    if (slots.validator) {
        return true;
    }
    
    // Si validator est un objet (cas d'usage avancé)
    if (typeof props.validator === 'object' && props.validator !== null) {
        return true;
    }
    
    return false;
}

/**
 * Extrait les attributs HTML natifs pour un input à partir des props
 * @param {Object} props - Props du composant
 * @returns {Object} - Attributs HTML à appliquer sur <input>, <select>, <textarea>, etc.
 */
export function getInputAttrs(props) {
    return {
        name: props.name || undefined,
        placeholder: props.placeholder || undefined,
        required: props.required || undefined,
        readonly: props.readonly || undefined,
        disabled: props.disabled || undefined,
        autocomplete: props.autocomplete || undefined,
        autofocus: props.autofocus || undefined,
        min: props.min || undefined,
        max: props.max || undefined,
        step: props.step || undefined,
        inputmode: props.inputmode || undefined,
        pattern: props.pattern || undefined,
        maxlength: props.maxlength || undefined,
        minlength: props.minlength || undefined,
    };
}

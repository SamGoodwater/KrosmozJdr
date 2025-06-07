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
        help: { type: String, default: "" },
        theme: { type: String, default: "" },
    };
    return Object.fromEntries(
        Object.entries(allInputProps).filter(([key]) => !exclude.includes(key)),
    );
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

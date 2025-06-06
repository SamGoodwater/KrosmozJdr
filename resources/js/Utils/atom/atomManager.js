/**
 * atomManager.js — Atom Manager KrosmozJDR
 *
 * Gestion centralisée des utilitaires pour tous les Atoms du design system KrosmozJDR.
 * Fournit :
 *   - Les props communes (commonProps)
 *   - Les helpers pour props/attrs (getCommonProps, getCommonAttrs)
 *   - La fusion intelligente de classes (mergeClasses)
 *   - (Futurs helpers : gestion d'events, de slots, de validation, etc.)
 *
 * À importer dans chaque atom :
 *   import { getCommonProps, getCommonAttrs, mergeClasses, commonProps } from '@/Utils/atom/atomManager';
 *
 * Tous les helpers sont documentés et typés pour garantir la cohérence, la maintenabilité et l'évolutivité du design system.
 */

/**
 * Objet contenant toutes les props communes à tous les atoms (accessibilité, tooltip, etc.)
 * @type {Object}
 */
const allCommonProps = {
    /** Accessibilité */
    ariaLabel: { type: String, default: "" },
    role: { type: String, default: "" },
    tabindex: { type: [String, Number], default: 0 },
    id: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    /** Tooltip (obligatoire pour tous les atoms sauf Tooltip lui-même) */
    tooltip: { type: [String, Object], default: "" },
    tooltip_placement: {
        type: String,
        default: "top",
        validator: (v) => ["top", "right", "bottom", "left"].includes(v),
    },
};

/**
 * Export principal des props communes (pour destructuration)
 */
export { allCommonProps as commonProps };

/**
 * Retourne un objet avec les props communes, en excluant celles spécifiées
 * @param {Object} [options]
 * @param {Array} [options.exclude=[]] - Liste des props à exclure
 * @returns {Object} - Props communes filtrées
 */
export function getCommonProps({ exclude = [] } = {}) {
    return Object.fromEntries(
        Object.entries(allCommonProps).filter(
            ([key]) => !exclude.includes(key),
        ),
    );
}

/**
 * Retourne un objet avec les props HTML/accessibilité à appliquer sur le root
 * @param {Object} props - Props à transformer en attributs HTML
 * @returns {Object} - Attributs HTML/accessibilité
 */
export function getCommonAttrs(props) {
    return {
        role: props.role || undefined,
        "aria-label": props.ariaLabel || undefined,
        tabindex: props.tabindex ?? undefined,
        disabled: props.disabled ?? undefined,
        id: props.id || undefined,
    };
}

/**
 * Fusionne les classes DaisyUI par défaut et les classes custom, sans doublon (priorité à customClasses)
 * @param {string[]|string} defaultClasses - Classes par défaut (array ou string)
 * @param {string[]|string} customClasses - Classes custom (array ou string)
 * @returns {string} - Liste de classes fusionnées sans doublon
 */
export function mergeClasses(defaultClasses, customClasses) {
    const defaultArr = Array.isArray(defaultClasses)
        ? defaultClasses
        : defaultClasses.split(" ");
    const customArr = Array.isArray(customClasses)
        ? customClasses
        : customClasses
          ? customClasses.split(" ")
          : [];
    // On retire les doublons, priorité à customArr
    const set = new Set([
        ...defaultArr.filter((c) => !customArr.includes(c)),
        ...customArr,
    ]);
    return Array.from(set).join(" ");
}

/**
 * Variantes autorisées pour les utilitaires custom
 */
export const customUtilityVariants = [
    "",
    "xs",
    "sm",
    "md",
    "lg",
    "xl",
    "2xl",
    "3xl",
    "4xl",
];

/**
 * Props à inclure dans chaque atom pour les utilitaires custom
 * @returns {Object} - { shadow, backdrop, opacity }
 */
export function getCustomUtilityProps() {
    return {
        shadow: {
            type: String,
            default: "",
            validator: (v) => customUtilityVariants.includes(v),
        },
        backdrop: {
            type: String,
            default: "",
            validator: (v) => customUtilityVariants.includes(v),
        },
        opacity: {
            type: String,
            default: "",
            validator: (v) => customUtilityVariants.includes(v),
        },
    };
}

/**
 * Génère les classes utilitaires custom (box-shadow, backdrop-blur, opacity)
 * @param {Object} options - { shadow, backdrop, opacity }
 * @returns {string[]} - Liste des classes utilitaires à appliquer
 */
export function getCustomUtilityClasses({ shadow, backdrop, opacity }) {
    const classes = [];
    if (shadow && shadow !== "") classes.push(`box-shadow-${shadow}`);
    if (backdrop && backdrop !== "") classes.push(`bd-blur-${backdrop}`);
    if (opacity && opacity !== "") classes.push(`bd-opacity-${opacity}`);
    return classes;
}

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

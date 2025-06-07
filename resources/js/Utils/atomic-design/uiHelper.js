/**
 * uiHelper.js — UI Helper KrosmozJDR
 *
 * Helpers universels pour tous les composants UI du design system KrosmozJDR (atoms, molecules, organisms).
 * Fournit :
 *   - Les props communes (commonProps)
 *   - Les helpers pour props/attrs (getCommonProps, getCommonAttrs)
 *   - La fusion intelligente de classes (mergeClasses)
 *   - Les utilitaires custom (getCustomUtilityProps, getCustomUtilityClasses)
 *
 * À importer dans chaque composant UI (atom, molecule, organism) :
 *   import { getCommonProps, getCommonAttrs, mergeClasses, commonProps } from '@/Utils/atomic-design/uiHelper';
 *
 * Les helpers spécifiques (inputs, contextes, etc.) sont à placer dans des managers dédiés (ex : atomManager.js).
 *
 * DRY : tout helper réutilisable à plusieurs niveaux doit être factorisé ici.
 *
 * Certaines molecules sont des wrappers ou compositions de composants DaisyUI (ex : Fieldset, FileInput, AvatarGroup, Modal, etc.) et doivent suivre la même rigueur d'API et de documentation que les atoms.
 */

/**
 * Objet contenant toutes les props communes à tous les composants UI (accessibilité, tooltip, etc.)
 * @type {Object}
 */
const allCommonAttrs = {
    /** Accessibilité */
    ariaLabel: { type: String, default: "" },
    role: { type: String, default: "" },
    tabindex: { type: [String, Number], default: 0 },
    id: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    /** Tooltip (obligatoire pour tous les composants UI sauf Tooltip lui-même) */
    tooltip: { type: [String, Object], default: "" },
    tooltip_placement: {
        type: String,
        default: "top",
        validator: (v) => ["top", "right", "bottom", "left"].includes(v),
    },
    /** Props HTML standards */
    class: { type: String, default: "" },
    style: { type: [String, Object], default: "" },
};

/**
 * Export principal des props communes (pour destructuration) pour tous les composants UI
 */
export { allCommonAttrs as commonProps };

/**
 * Retourne un objet avec les props communes, en excluant celles spécifiées
 * @param {Object} [options]
 * @param {Array} [options.exclude=[]] - Liste des props à exclure
 * @returns {Object} - Props communes filtrées
 */
export function getCommonProps({ exclude = [] } = {}) {
    return Object.fromEntries(
        Object.entries(allCommonAttrs).filter(
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
 * Fusionne une infinité de listes de classes (array ou string), la dernière a priorité (vérité).
 * @param  {...(string[]|string)} classLists - Plusieurs listes de classes (array ou string)
 * @returns {string} - Liste de classes fusionnées sans doublon, priorité à la dernière
 */
export function mergeClasses(...classLists) {
    const all = classLists.map((list) =>
        Array.isArray(list) ? list : list ? list.split(" ") : [],
    );
    const flat = all.flat();
    // On garde la dernière occurrence de chaque classe (priorité à la fin)
    const seen = new Map();
    for (let i = 0; i < flat.length; i++) {
        seen.set(flat[i], i); // index de la dernière occurrence
    }
    // Trie selon l'ordre d'apparition, mais priorité à la dernière occurrence
    return Array.from(new Set(flat.filter((c, i) => seen.get(c) === i))).join(
        " ",
    );
}

/**
 * Variantes autorisées pour les utilitaires custom
 */
export const customUtilityVariants = [
    "",
    "none",
    "xs",
    "sm",
    "md",
    "lg",
    "xl",
    "2xl",
    "3xl",
    "4xl",
    "full",
];

/**
 * Props à inclure dans chaque composant UI pour les utilitaires custom
 * @returns {Object} - { shadow, backdrop, opacity, rounded }
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
        rounded: {
            type: String,
            default: "",
            validator: (v) => customUtilityVariants.includes(v),
        },
    };
}

/**
 * Génère les classes utilitaires custom (box-shadow, backdrop-blur, opacity, rounded)
 * @param {Object} options - { shadow, backdrop, opacity, rounded }
 * @returns {string[]} - Liste des classes utilitaires à appliquer
 */
export function getCustomUtilityClasses({
    shadow,
    backdrop,
    opacity,
    rounded,
}) {
    const classes = [];
    if (shadow && shadow !== "") classes.push(`box-shadow-${shadow}`);
    if (backdrop && backdrop !== "") classes.push(`bd-blur-${backdrop}`);
    if (opacity && opacity !== "") classes.push(`bd-opacity-${opacity}`);
    if (rounded && rounded !== "") classes.push(`bd-rounded-${rounded}`);
    return classes;
}

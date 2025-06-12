// data-inputMap.js
// Ce fichier ne contient QUE les listes et mappings spécifiques à la famille data-input (Atomic Design).
// Les listes transverses (colorList, sizeList, variantList, etc.) sont centralisées dans atomMap.js.
// À importer ici UNIQUEMENT ce qui est propre à data-input (ex: maskList pour Rating, stateList pour Validator, typeList pour InputField, etc.)

// --- LISTES SPÉCIFIQUES DATA-INPUT ---

// maskList : pour Rating.vue (étoiles, coeurs, etc.)
export const maskList = [
    "",
    "mask-star",
    "mask-heart",
    "mask-circle",
    "mask-square",
    "mask-diamond",
    "mask-triangle",
    "mask-hexagon",
    "mask-decagon",
    "mask-pentagon",
    "mask-star-2",
    "mask-triangle-2",
    "mask-triangle-3",
    "mask-triangle-4",
    "mask-half-1",
    "mask-half-2",
];
// stateList : pour Validator.vue (état de validation)
export const stateList = ["", "error", "success", "warning", "info"];
// typeList : pour InputField.vue (types d'input HTML supportés)
export const typeList = [
    "text",
    "email",
    "password",
    "number",
    "url",
    "tel",
    "search",
    "date",
    "datetime-local",
    "month",
    "week",
    "time",
    "color",
    "checkbox",
    "radio",
    "range",
    "hidden",
    "submit",
    "reset",
    "button",
];

export const sizeMap = {
    xs: "text-xs",
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
    xl: "text-xl",
};
export const colorMap = {
    primary: "text-primary",
    secondary: "text-secondary",
    accent: "text-accent",
    info: "text-info",
    success: "text-success",
    warning: "text-warning",
    error: "text-error",
    neutral: "text-neutral",
    "base-100": "text-base-100",
    "base-200": "text-base-200",
    "base-300": "text-base-300",
};

export const stateMap = {
    error: "text-error",
    success: "text-success",
    warning: "text-warning",
    info: "text-info",
    "": "",
};

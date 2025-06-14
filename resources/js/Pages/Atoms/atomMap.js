// atomMap.js
// Centralisation DRY des listes de valeurs très générales et transverses à la plupart des atoms.
// Usage : validation des props (taille, couleur, variant) dans les atoms qui partagent une API commune.
// Seules les listes VRAIMENT génériques doivent être ici (ex: tailles DaisyUI, couleurs DaisyUI, variants DaisyUI).
// Les cas particuliers ou propres à une famille d'atomes doivent rester dans leur map dédiée (ex: data-displayMap.js, actionMap.js).
// But : éviter la duplication, mais ne pas sur-généraliser ni complexifier inutilement l'API.

export const colorList = [
    "",
    "neutral",
    "primary",
    "secondary",
    "accent",
    "info",
    "success",
    "warning",
    "error",
];
// Variants DaisyUI (Badge, Card, etc.)
export const variantList = ["", "outline", "dash", "soft", "ghost", "glass"];
// Tailles DaisyUI génériques (Badge, Status, Card, Kbd, etc.)
export const sizeXlList = ["", "xs", "sm", "md", "lg", "xl"];
export const sizeList = [...sizeXlList];
export const size2XlList = ["", "xs", "sm", "md", "lg", "xl", "2xl"];
export const size3XlList = ["", "xs", "sm", "md", "lg", "xl", "2xl", "3xl"];
export const size4XlList = [
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
export const size5XlList = [
    "",
    "xs",
    "sm",
    "md",
    "lg",
    "xl",
    "2xl",
    "3xl",
    "4xl",
    "5xl",
];
export const size6XlList = [
    "",
    "xs",
    "sm",
    "md",
    "lg",
    "xl",
    "2xl",
    "3xl",
    "4xl",
    "5xl",
    "6xl",
];

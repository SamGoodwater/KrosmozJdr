// data-displayMap.js
// Ce fichier ne contient QUE les listes et mappings spécifiques à la famille data-display (Atomic Design).
// Les listes transverses (colorList, variantList, sizeList, cardSizeList, statusColorList, statusSizeList, kbdSizeList, iconSizeList, etc.) sont désormais centralisées dans atomMap.js.
// À importer ici UNIQUEMENT ce qui est propre à data-display (ex: maskList, breakpoints, mappings de classes, etc.)

// --- LISTES SPÉCIFIQUES DATA-DISPLAY ---
// maskList et breakpoints restent ici car spécifiques à data-display
export const maskList = [
    "",
    "mask",
    "mask-squircle",
    "mask-heart",
    "mask-hexagon",
    "mask-hexagon-2",
    "mask-decagon",
    "mask-pentagon",
    "mask-diamond",
    "mask-square",
    "mask-circle",
    "mask-star",
    "mask-star-2",
    "mask-triangle",
    "mask-triangle-2",
    "mask-triangle-3",
    "mask-triangle-4",
    "mask-half-1",
    "mask-half-2",
];
export const breakpoints = ["sm", "md", "lg", "xl", "2xl"];
// Pour les tailles/couleurs/variants, importer depuis atomMap.js !

// --- MAPPINGS DE CLASSES ---
// (inchangés, déjà DRY)
export const sizeMap = {
    xs: ["w-16", "h-16"],
    sm: ["w-24", "h-24"],
    md: ["w-32", "h-32"],
    lg: ["w-48", "h-48"],
    xl: ["w-64", "h-64"],
    "2xl": ["w-96", "h-96"],
    "3xl": ["w-128", "h-128"],
    "4xl": ["w-192", "h-192"],
    "5xl": ["w-256", "h-256"],
    "6xl": ["w-512", "h-512"],
};

export const ratioMap = {
    "1/1": "aspect-square",
    square: "aspect-square",
    "16/9": "aspect-video",
    video: "aspect-video",
    "4/3": "aspect-[4/3]",
    "3/2": "aspect-[3/2]",
    "2/1": "aspect-[2/1]",
    "3/4": "aspect-[3/4]",
    "9/16": "aspect-[9/16]",
};

export const roundedMap = {
    none: "",
    sm: "rounded-sm",
    md: "rounded-md",
    lg: "rounded-lg",
    xl: "rounded-xl",
    "2xl": "rounded-2xl",
    "3xl": "rounded-3xl",
    full: "rounded-full",
    circle: "rounded-full",
};

export const fitMap = {
    cover: "object-cover",
    contain: "object-contain",
    fill: "object-fill",
    none: "object-none",
    "scale-down": "object-scale-down",
};

export const positionMap = {
    center: "object-center",
    top: "object-top",
    right: "object-right",
    bottom: "object-bottom",
    left: "object-left",
    "top-left": "object-top-left",
    "top-right": "object-top-right",
    "bottom-left": "object-bottom-left",
    "bottom-right": "object-bottom-right",
};

export const filterClassMap = {
    grayscale: "filter grayscale",
    sepia: "filter sepia",
    blur: "filter blur",
    brightness: "filter brightness-150",
    contrast: "filter contrast-150",
    "hue-rotate": "filter hue-rotate-90",
    invert: "filter invert",
    saturate: "filter saturate-200",
};

export const ringMap = {
    xs: "ring-1",
    sm: "ring-2",
    md: "ring-4",
    lg: "ring-8",
    xl: "ring-12",
    "2xl": "ring-[16px]",
    "3xl": "ring-[24px]",
    "4xl": "ring-[32px]",
};
export const ringColorMap = {
    primary: "ring-primary",
    secondary: "ring-secondary",
    accent: "ring-accent",
    info: "ring-info",
    success: "ring-success",
    warning: "ring-warning",
    error: "ring-error",
    "base-100": "ring-base-100",
    "base-200": "ring-base-200",
    "base-300": "ring-base-300",
    neutral: "ring-neutral",
};
export const ringOffsetMap = {
    xs: "ring-offset-1",
    sm: "ring-offset-2",
    md: "ring-offset-4",
    lg: "ring-offset-8",
    xl: "ring-offset-12",
    "2xl": "ring-offset-[16px]",
    "3xl": "ring-offset-[24px]",
    "4xl": "ring-offset-[32px]",
};
export const ringOffsetColorMap = {
    primary: "ring-offset-primary",
    secondary: "ring-offset-secondary",
    accent: "ring-offset-accent",
    info: "ring-offset-info",
    success: "ring-offset-success",
    warning: "ring-offset-warning",
    error: "ring-offset-error",
    "base-100": "ring-offset-base-100",
    "base-200": "ring-offset-base-200",
    "base-300": "ring-offset-base-300",
    neutral: "ring-offset-neutral",
};
export const colorMap = {
    "": "",
    neutral: "text-neutral",
    primary: "text-primary",
    secondary: "text-secondary",
    accent: "text-accent",
    info: "text-info",
    success: "text-success",
    warning: "text-warning",
    error: "text-error",
};
export const sizeTitleMap = {
    xs: "text-base",
    sm: "text-lg",
    md: "text-xl",
    lg: "text-2xl",
    xl: "text-3xl",
    "": "text-xl",
};
export const sizeValueMap = {
    xs: "text-lg",
    sm: "text-2xl",
    md: "text-3xl",
    lg: "text-4xl",
    xl: "text-5xl",
    "": "text-3xl",
};
export const sizeDescMap = {
    xs: "text-xs",
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
    xl: "text-xl",
    "": "text-base",
};
export const sizeIconMap = {
    xs: "sm",
    sm: "md",
    md: "lg",
    lg: "xl",
    xl: "2xl",
    "": "lg",
};

export const sizeHeightMap = {
    xs: "0.75rem",
    sm: "1rem",
    md: "1.5rem",
    lg: "2rem",
    xl: "3rem",
    "2xl": "4rem",
    "3xl": "5rem",
    "4xl": "6rem",
    "5xl": "7rem",
    "6xl": "8rem",
};

// Mapping des tailles pour FontAwesome
export const faSizeMap = {
    xs: "text-xs",
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
    xl: "text-xl",
    "2xl": "text-2xl",
    "3xl": "text-3xl",
    "4xl": "text-4xl",
    "5xl": "text-5xl",
    "6xl": "text-6xl",
};

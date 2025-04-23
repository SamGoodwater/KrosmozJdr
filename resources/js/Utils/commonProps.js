import { VALID_COLORS, VALID_WEIGHTS } from "./extractTheme";

// Fonction pour valider une couleur
function isValidColor(value) {
    if (!value) return true;
    const parts = value.split("-");
    return VALID_COLORS.includes(parts[0]) && VALID_WEIGHTS.includes(parts[1]);
}

// Fonction pour valider une taille
function isValidSize(value) {
    return ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl"].includes(value);
}

// Fonction pour valider un arrondi
function isValidRounded(value) {
    return [
        "none",
        "xs",
        "sm",
        "md",
        "lg",
        "xl",
        "2xl",
        "3xl",
        "full",
    ].includes(value);
}

// Fonction pour valider un flou
function isValidBlur(value) {
    return ["none", "xs", "sm", "md", "lg", "xl", "2xl"].includes(value);
}

// Fonction pour valider une ombre
function isValidShadow(value) {
    return ["none", "xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl"].includes(
        value,
    );
}

// Fonction pour valider une opacité
function isValidOpacity(value) {
    if (!value) return true;
    const num = parseInt(value);
    return num >= 0 && num <= 100;
}

export const commonProps = {
    theme: {
        type: String,
        default: null,
    },
    // Taille et forme
    size: {
        type: String,
        default: "md",
        validator: isValidSize,
    },
    rounded: {
        type: String,
        default: "lg",
        validator: isValidRounded,
    },
    blur: {
        type: String,
        default: "lg",
        validator: isValidBlur,
    },
    shadow: {
        type: String,
        default: "sm",
        validator: isValidShadow,
    },
    // Couleurs
    bgColor: {
        type: String,
        default: null,
        validator: isValidColor,
    },
    textColor: {
        type: String,
        default: null,
        validator: isValidColor,
    },
    borderColor: {
        type: String,
        default: null,
        validator: isValidColor,
    },
    color: {
        type: String,
        default: null,
        validator: isValidColor,
    },
    // Opacité
    opacity: {
        type: [String, Number],
        default: null,
        validator: isValidOpacity,
    },
    // Tooltip
    tooltip: {
        type: [String, Object],
        default: null,
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) =>
            ["", "top", "right", "bottom", "left"].includes(value),
    },
};

// Fonction pour générer les classes CSS à partir des props
export function generateClasses(props) {
    const classes = [];

    // Taille et forme
    if (props.size) classes.push(`size-${props.size}`);
    if (props.rounded) classes.push(`rounded-${props.rounded}`);
    if (props.blur) classes.push(`backdrop-blur-${props.blur}`);
    if (props.shadow) classes.push(`shadow-${props.shadow}`);

    // Couleurs
    if (props.bgColor) classes.push(`bg-${props.bgColor}`);
    if (props.textColor) classes.push(`text-${props.textColor}`);
    if (props.borderColor) classes.push(`border-${props.borderColor}`);
    if (props.color) classes.push(`text-${props.color}`);

    // Opacité
    if (props.opacity) classes.push(`opacity-${props.opacity}`);

    return classes.join(" ");
}

// Fonction pour combiner les props avec le thème
export function combinePropsWithTheme(props, themeProps) {
    return {
        ...props,
        bgColor: props.bgColor || themeProps.bgColor,
        textColor: props.textColor || themeProps.textColor,
        borderColor: props.borderColor || themeProps.borderColor,
        color: props.color || themeProps.color,
        size: props.size || themeProps.size,
        rounded: props.rounded || themeProps.rounded,
        blur: props.blur || themeProps.blur,
        shadow: props.shadow || themeProps.shadow,
        opacity: props.opacity || themeProps.opacity,
    };
}

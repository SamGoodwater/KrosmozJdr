const THEME_PATTERNS = {
    // Apparence
    shadow: {
        regex: /(?:^|\s)(?<capture>(box-shadow-|shadow-|boxShadow-)(none|xs|sm|md|lg|xl|2xl|3xl|4xl))(?:\s|$)/,
    },
    "box-shadow": {
        regex: /(?:^|\s)(?<capture>(box-shadow-|shadow-|boxShadow-)(none|xs|sm|md|lg|xl|2xl|3xl|4xl))(?:\s|$)/,
    },
    blur: {
        regex: /(?:^|\s)(?<capture>(backdrop-blur-|blur-|backdropBlur-)(none|xs|sm|md|lg|xl|2xl|3xl|4xl))(?:\s|$)/,
    },
    "backdrop-blur": {
        regex: /(?:^|\s)(?<capture>(backdrop-blur-|blur-|backdropBlur-)(none|xs|sm|md|lg|xl|2xl|3xl|4xl))(?:\s|$)/,
    },
    opacity: {
        regex: /(?:^|\s)(?<capture>opacity-(\d{1,2}|100))(?:\s|$)/,
    },
    rounded: {
        regex: /(?:^|\s)(?<capture>rounded-(none|sm|md|lg|xl|2xl|3xl|4xl|full))(?:\s|$)/,
    },
    bordered: {
        regex: /(?:^|\s)(?<capture>border|bordered)(?:\s|$)/,
    },

    // Couleurs
    color: {
        regex: /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/,
    },
    bgColor: {
        regex: /(?:^|\s)(?<capture>bg-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    "bg-color": {
        regex: /(?:^|\s)(?<capture>bg-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    borderColor: {
        regex: /(?:^|\s)(?<capture>border-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    "border-color": {
        regex: /(?:^|\s)(?<capture>border-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    textColor: {
        regex: /(?:^|\s)(?<capture>text-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    "text-color": {
        regex: /(?:^|\s)(?<capture>text-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    colorAuto: {
        regex: /(?:^|\s)(?<capture>color-auto|colorAuto)(?:\s|$)/,
    },
    "color-auto": {
        regex: /(?:^|\s)(?<capture>color-auto|colorAuto)(?:\s|$)/,
    },

    // Dimensions
    width: {
        regex: /(?:^|\s)w-(?<capture>auto|\[?\d+(?:px|rem|em|vh|vw|%)\]?|\d+)(?:\s|$)/,
    },
    height: {
        regex: /(?:^|\s)h-(?<capture>auto|\[?\d+(?:px|rem|em|vh|vw|%)\]?|\d+)(?:\s|$)/,
    },
    size: {
        regex: /(?:^|\s)(?<capture>xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|full)(?:\s|$)/,
    },

    // Style spécifique
    styled: {
        regex: /(?:^|\s)(?<capture>glass|outline|link|ghost)(?:\s|$)/,
    },
    style: {
        regex: /(?:^|\s)(?<capture>glass|outline|link|ghost)(?:\s|$)/,
    },
    face: {
        regex: /(?:^|\s)(?<capture>block|wide|square|circle)(?:\s|$)/,
    },
    border: {
        regex: /(?:^|\s)(?<capture>border)(?:\s|$)/,
    },

    // Input spécifique
    type: {
        regex: /(?:^|\s)(?<capture>text|email|password|tel|url)(?:\s|$)/,
    },
    autofocus: {
        regex: /(?:^|\s)(?<capture>autofocus)(?:\s|$)/,
    },
    required: {
        regex: /(?:^|\s)(?<capture>required)(?:\s|$)/,
    },
    maxLength: {
        regex: /(?:^|\s)max:(?<capture>[0-9]+)(?:\s|$)/,
    },
    minLength: {
        regex: /(?:^|\s)min:(?<capture>[0-9]+)(?:\s|$)/,
    },

    // Filtres
    filter: {
        regex: /(?:^|\s)(?<capture>filter-(grayscale|sepia|blur|brightness|contrast|hue-rotate|invert|saturate))(?:\s|$)/,
    },
};

// Liste des couleurs valides
const VALID_COLORS = ['primary', 'secondary', 'base', 'neutral', 'success', 'error', 'warning', 'info', 'accent'];
const VALID_WEIGHTS = ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'];

// Fonction pour valider une couleur
function isValidColor(value) {
    if (!value) return true;
    const parts = value.split('-');
    return VALID_COLORS.includes(parts[0]) && VALID_WEIGHTS.includes(parts[1]);
}

// Fonction pour extraire la valeur d'une couleur
function extractColorValue(value) {
    if (!value) return null;
    const parts = value.split('-');
    if (parts.length === 2 && VALID_COLORS.includes(parts[0]) && VALID_WEIGHTS.includes(parts[1])) {
        return value;
    }
    return null;
}

export function extractTheme(theme) {
    const result = {};

    Object.entries(THEME_PATTERNS).forEach(([key, pattern]) => {
        const match = pattern.regex.exec(theme || "");
        result[key] = match?.groups?.capture || null;
    });

    // Traitement spécial pour les couleurs
    if (result.bgColor) {
        result.bgColor = extractColorValue(result.bgColor.replace("bg-", ""));
    }
    if (result.textColor) {
        result.textColor = extractColorValue(
            result.textColor.replace("text-", ""),
        );
    }
    if (result.borderColor) {
        result.borderColor = extractColorValue(
            result.borderColor.replace("border-", ""),
        );
    }
    if (result.color) {
        result.color = extractColorValue(result.color);
    }

    // Traitement spécial pour l'opacité
    if (result.opacity) {
        result.opacity = result.opacity.replace("opacity-", "");
    }

    return result;
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

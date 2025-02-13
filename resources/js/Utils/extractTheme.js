const THEME_PATTERNS = {
    // Apparence
    shadow: {
        regex: /(?:^|\s)(?<capture>shadow-(none|xs|sm|md|lg|xl|2xl))(?:\s|$)/,
    },
    blur: {
        regex: /(?:^|\s)(?<capture>blur-(none|xs|sm|md|lg|xl|2xl))(?:\s|$)/,
    },
    opacity: {
        regex: /(?:^|\s)(?<capture>opacity-(\d{1,2}|100))(?:\s|$)/,
    },
    rounded: {
        regex: /(?:^|\s)(?<capture>rounded-(none|sm|md|lg|xl|2xl|3xl|full))(?:\s|$)/,
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
    borderColor: {
        regex: /(?:^|\s)(?<capture>border-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    textColor: {
        regex: /(?:^|\s)(?<capture>text-(([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error))(?:\s|$)/,
    },
    colorAuto: {
        regex: /(?:^|\s)(?<capture>color-auto)(?:\s|$)/,
    },

    // Dimensions
    width: {
        regex: /(?:^|\s)w-(?<capture>auto|\[?\d+(?:px|rem|em|vh|vw|%)\]?|\d+)(?:\s|$)/,
    },
    height: {
        regex: /(?:^|\s)h-(?<capture>auto|\[?\d+(?:px|rem|em|vh|vw|%)\]?|\d+)(?:\s|$)/,
    },
    size: {
        regex: /(?:^|\s)(?<capture>xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl)(?:\s|$)/,
    },

    // Style spécifique
    styled: {
        regex: /(?:^|\s)(?<capture>glass|outline|link|ghost)(?:\s|$)/,
    },
    face: {
        regex: /(?:^|\s)(?<capture>block|wide|square|circle)(?:\s|$)/,
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
};

export function extractTheme(theme) {
    const result = {};

    Object.entries(THEME_PATTERNS).forEach(([key, pattern]) => {
        const match = pattern.regex.exec(theme || "");
        result[key] = match?.groups?.capture || null;
    });

    return result;
}

/**
 * useInputStyle — Système de style unifié pour tous les types d'input
 *
 * @description
 * Système centralisé pour la gestion des styles d'input avec support de tous les types :
 * - Types d'input : text, email, password, number, url, tel, search, date, textarea, select, file, range, rating, checkbox, radio, toggle
 * - Variants : glass, dash, outline, ghost, soft
 * - Tailles : xs, sm, md, lg, xl (DaisyUI)
 * - Couleurs : primary, secondary, accent, info, success, warning, error, neutral + personnalisées
 * - Animations : booléen (défaut) ou string (animation spécifique)
 * - Transmission automatique aux labels et helpers
 * - Organisation logique par type visuel avec raccourcis intelligents
 * - Fonction intelligente de fusion de styles
 *
 * @example
 * // API unifiée
 * getInputStyle('text', { variant: 'glass', color: 'primary', size: 'md', animation: true })
 * getInputStyle('textarea', { variant: 'outline', color: 'success', size: 'lg' })
 * 
 * // Validation et normalisation
 * validateInputStyle('text', { variant: 'invalid', color: 'custom' })
 * normalizeInputStyle('text', { variant: 'glass' })
 * 
 * // Fusion intelligente de styles
 * mergeStyleConfig({ color: 'primary' }, { color: 'success', size: 'lg' })
 * mergeStyleConfig({ variant: 'glass' }, 'success') // String = couleur
 *
 * @param {String} inputType - Type d'input ('text', 'email', 'password', etc.)
 * @param {Object} styleConfig - Configuration de style
 * @returns {String} - Classes CSS à appliquer
 */

/**
 * Types d'input supportés avec leurs valeurs par défaut
 */
export const INPUT_TYPES = {
    // Inputs textuels (visuellement identiques)
    text: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    email: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    password: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    url: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    tel: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    search: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    date: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    textarea: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    select: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    file: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    filter: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    
    // Inputs numériques
    number: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    range: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    rating: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    
    // Inputs de sélection
    checkbox: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    radio: { variant: 'glass', size: 'md', color: 'primary', animation: true },
    toggle: { variant: 'glass', size: 'md', color: 'primary', animation: true },
};

/**
 * Tailles DaisyUI supportées
 * Note: Les classes sont génériques (input-*) mais fonctionnent pour tous les types
 * Les composants Core appliquent les styles spécifiques via SCSS
 */
export const SIZES = {
    xs: 'input-xs',
    sm: 'input-sm', 
    md: 'input-md',
    lg: 'input-lg',
    xl: 'input-xl'
};

/**
 * Mapping des tailles par type d'input (pour compatibilité)
 */
export const SIZE_MAP = {
    select: {
        xs: 'select-xs',
        sm: 'select-sm',
        md: 'select-md',
        lg: 'select-lg',
        xl: 'select-xl'
    },
    textarea: {
        xs: 'textarea-xs',
        sm: 'textarea-sm',
        md: 'textarea-md',
        lg: 'textarea-lg',
        xl: 'textarea-xl'
    },
    // Par défaut, utilise les classes input-*
    default: SIZES
};

/**
 * Couleurs DaisyUI supportées + personnalisées
 * Note: Les classes sont génériques (input-*) mais fonctionnent pour tous les types
 * Les composants Core appliquent les styles spécifiques via SCSS
 */
export const COLORS = {
    neutral: 'input-neutral',
    primary: 'input-primary',
    secondary: 'input-secondary',
    accent: 'input-accent',
    info: 'input-info',
    success: 'input-success',
    warning: 'input-warning',
    error: 'input-error'
};

/**
 * Mapping des couleurs par type d'input (pour compatibilité)
 */
export const COLOR_MAP = {
    select: {
        neutral: 'select-neutral',
        primary: 'select-primary',
        secondary: 'select-secondary',
        accent: 'select-accent',
        info: 'select-info',
        success: 'select-success',
        warning: 'select-warning',
        error: 'select-error'
    },
    textarea: {
        neutral: 'textarea-neutral',
        primary: 'textarea-primary',
        secondary: 'textarea-secondary',
        accent: 'textarea-accent',
        info: 'textarea-info',
        success: 'textarea-success',
        warning: 'textarea-warning',
        error: 'textarea-error'
    },
    // Par défaut, utilise les classes input-*
    default: COLORS
};

/**
 * Variants supportés
 */
export const VARIANTS = {
    glass: 'glass',
    dash: 'dash',
    outline: 'outline',
    ghost: 'ghost',
    soft: 'soft'
};

/**
 * Classes communes pour tous les variants
 */
const COMMON_CLASSES = {
    glass: {
        base: ['bg-transparent', 'border', 'border-gray-300'],
        focus: ['focus:border-primary', 'focus:ring-2', 'focus:ring-primary/20'],
        hover: ['hover:border-primary/60'],
        animation: ['hover:scale-101', 'focus:scale-101', 'transition-all', 'duration-200']
    },
    dash: {
        base: ['border-dashed', 'border-2', 'bg-gray-50'],
        focus: ['focus:bg-white'],
        hover: ['hover:bg-gray-100'],
        animation: ['hover:scale-101', 'focus:scale-101', 'transition-all', 'duration-200']
    },
    outline: {
        base: ['border-2', 'bg-transparent'],
        focus: ['focus:border-primary', 'focus:ring-2', 'focus:ring-primary/20'],
        hover: ['hover:border-primary/60'],
        animation: ['hover:scale-101', 'focus:scale-101', 'transition-all', 'duration-200']
    },
    ghost: {
        base: ['border', 'border-transparent', 'bg-transparent'],
        focus: ['focus:bg-white', 'focus:border-gray-300'],
        hover: ['hover:bg-gray-50'],
        animation: ['hover:scale-101', 'focus:scale-101', 'transition-all', 'duration-200']
    },
    soft: {
        base: ['border-b-2', 'border-gray-300', 'bg-transparent', 'rounded-none'],
        focus: ['focus:border-primary', 'focus:ring-0'],
        hover: ['hover:border-primary/60'],
        animation: ['hover:scale-101', 'focus:scale-101', 'transition-all', 'duration-200']
    }
};

/**
 * Configuration pour les inputs textuels (input basique, select, file, textarea, filter)
 */
const TEXTUAL_INPUT_CONFIG = {
    glass: {
        classes: ['input', ...COMMON_CLASSES.glass.base, ...COMMON_CLASSES.glass.focus, ...COMMON_CLASSES.glass.hover],
        animations: COMMON_CLASSES.glass.animation
    },
    dash: {
        classes: ['input', ...COMMON_CLASSES.dash.base, ...COMMON_CLASSES.dash.focus, ...COMMON_CLASSES.dash.hover],
        animations: COMMON_CLASSES.dash.animation
    },
    outline: {
        classes: ['input', ...COMMON_CLASSES.outline.base, ...COMMON_CLASSES.outline.focus, ...COMMON_CLASSES.outline.hover],
        animations: COMMON_CLASSES.outline.animation
    },
    ghost: {
        classes: ['input', ...COMMON_CLASSES.ghost.base, ...COMMON_CLASSES.ghost.focus, ...COMMON_CLASSES.ghost.hover],
        animations: COMMON_CLASSES.ghost.animation
    },
    soft: {
        classes: ['input', ...COMMON_CLASSES.soft.base, ...COMMON_CLASSES.soft.focus, ...COMMON_CLASSES.soft.hover],
        animations: COMMON_CLASSES.soft.animation
    }
};

/**
 * Configuration pour les inputs numériques (range, rating)
 */
const NUMERICAL_INPUT_CONFIG = {
    glass: {
        classes: ['range', 'range-primary', 'bg-transparent'],
        animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
    },
    dash: {
        classes: ['range', 'range-primary', 'bg-gray-50'],
        animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
    },
    outline: {
        classes: ['range', 'range-primary', 'bg-transparent'],
        animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
    },
    ghost: {
        classes: ['range', 'range-primary', 'bg-transparent'],
        animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
    },
    soft: {
        classes: ['range', 'range-primary', 'bg-transparent'],
        animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
    }
};

/**
 * Configuration pour les inputs de sélection (checkbox, radio, toggle)
 */
const SELECTION_INPUT_CONFIG = {
    glass: {
        classes: ['checkbox', 'checkbox-primary', 'bg-transparent'],
        animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
    },
    dash: {
        classes: ['checkbox', 'checkbox-primary', 'bg-gray-50'],
        animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
    },
    outline: {
        classes: ['checkbox', 'checkbox-primary', 'bg-transparent'],
        animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
    },
    ghost: {
        classes: ['checkbox', 'checkbox-primary', 'bg-transparent'],
        animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
    },
    soft: {
        classes: ['checkbox', 'checkbox-primary', 'bg-transparent'],
        animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
    }
};

/**
 * Configuration spéciale pour les composants spécifiques
 */
const SPECIAL_COMPONENT_CONFIG = {
    // Textarea utilise la configuration textuelle mais avec la classe textarea
    textarea: {
        glass: {
            classes: ['textarea'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['textarea'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['textarea'], 
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['textarea'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }, 
        soft: {
            classes: ['textarea'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }
    },
    
    // Select utilise la configuration textuelle mais avec la classe select
    // La couleur sera ajoutée dynamiquement par getInputStyle
    select: {
        glass: {
            classes: ['select', 'select-variant-glass'],
            animations: COMMON_CLASSES.glass.animation
        },
        dash: {
            classes: ['select', 'select-variant-dash'],
            animations: COMMON_CLASSES.dash.animation
        },
        outline: {
            classes: ['select', 'select-variant-outline'],
            animations: COMMON_CLASSES.outline.animation
        },
        ghost: {
            classes: ['select', 'select-variant-ghost'],
            animations: COMMON_CLASSES.ghost.animation
        },
        soft: {
            classes: ['select', 'select-variant-soft'],
            animations: COMMON_CLASSES.soft.animation
        }
    },
    
    // File utilise la configuration textuelle mais avec la classe file-input
    file: {
        glass: {
            classes: ['file-input'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['file-input'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['file-input'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['file-input'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        soft: {
            classes: ['file-input'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }
    },
    
    // Filter utilise la configuration textuelle mais avec la classe filter
    filter: {
        glass: {
            classes: ['filter'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }, 
        dash: {
            classes: ['filter'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['filter'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }, 
        ghost: {
            classes: ['filter'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }, 
        soft: {
            classes: ['filter'], 
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }
    },
    
    // Date utilise la configuration textuelle mais avec la classe cally
    date: {
        glass: {
            classes: ['cally'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['cally'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['cally'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['cally'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        soft: {
            classes: ['cally'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        }
    },
    
    // Color utilise la configuration textuelle mais avec la classe color-picker-container
    color: {
        glass: {
            classes: ['color-picker-container'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['color-picker-container'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['color-picker-container'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['color-picker-container'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        },
        soft: {
            classes: ['color-picker-container'],
            animations: ['hover-scale-101', 'focus-scale-101', 'transition-transform', 'duration-200']
        }
    },
    
    // Range utilise la configuration numérique
    range: NUMERICAL_INPUT_CONFIG,
    
    // Rating utilise la configuration numérique mais avec la classe rating
    rating: {
        glass: {
            classes: ['rating'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['rating'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['rating'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['rating'],
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        },
        soft: {
            classes: ['rating'], 
            animations: ['hover:scale-101', 'focus:scale-101', 'transition-transform', 'duration-200']
        }  
    },
    
    // Radio utilise la configuration de sélection mais avec la classe radio
    radio: {
        glass: {
            classes: ['radio'],
            animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
        },
        dash: {
            classes: ['radio'],
            animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
        },
        outline: {
            classes: ['radio'],
            animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
        },
        ghost: {
            classes: ['radio'],
            animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
        },
        soft: {
            classes: ['radio'],
            animations: ['hover:scale-110', 'checked:scale-110', 'transition-transform', 'duration-200']
        }
    },
    
    // Toggle utilise la configuration de sélection mais avec la classe toggle
    toggle: {
        glass: {
            classes: ['toggle'],
            animations: ['hover:scale-101', 'checked:translate-x-6', 'transition-all', 'duration-300']
        },
        dash: {
            classes: ['toggle'],
            animations: ['hover:scale-101', 'checked:translate-x-6', 'transition-all', 'duration-300']
        },
        outline: {
            classes: ['toggle'],
            animations: ['hover:scale-101', 'checked:translate-x-6', 'transition-all', 'duration-300']
        },
        ghost: {
            classes: ['toggle'],
            animations: ['hover:scale-101', 'checked:translate-x-6', 'transition-all', 'duration-300']
        },
        soft: {
                classes: ['toggle'],
            animations: ['hover:scale-101', 'checked:translate-x-6', 'transition-all', 'duration-300']
        }
    }
};

/**
 * Tableau de configuration des styles par type d'input et variant
 * Utilise des raccourcis intelligents pour éviter la duplication
 */
export const STYLE_CONFIG = {
    // Inputs textuels (utilisent la même configuration de base)
    input: TEXTUAL_INPUT_CONFIG, // Ajout du type 'input' pour corriger le warning
    text: TEXTUAL_INPUT_CONFIG,
    email: TEXTUAL_INPUT_CONFIG,
    password: TEXTUAL_INPUT_CONFIG,
    url: TEXTUAL_INPUT_CONFIG,
    tel: TEXTUAL_INPUT_CONFIG,
    search: TEXTUAL_INPUT_CONFIG,
    number: TEXTUAL_INPUT_CONFIG,
    
    // Inputs de sélection (utilisent la même configuration de base)
    checkbox: SELECTION_INPUT_CONFIG,
    
    // Composants spéciaux (configuration personnalisée)
    ...SPECIAL_COMPONENT_CONFIG
};

// --- FONCTIONS PRINCIPALES ---

/**
 * Normalise une configuration de style pour un type d'input
 * @param {String} inputType - Type d'input
 * @param {Object} styleConfig - Configuration de style
 * @returns {Object} - Configuration normalisée
 */
export function normalizeInputStyle(inputType, styleConfig = {}) {
    // Valeurs par défaut pour le type d'input
    const defaults = INPUT_TYPES[inputType] || INPUT_TYPES.text;
    
    // Normalisation
    const normalized = {
        variant: styleConfig.variant || defaults.variant,
        size: styleConfig.size || defaults.size,
        color: styleConfig.color || defaults.color,
        animation: styleConfig.animation !== undefined ? styleConfig.animation : defaults.animation
    };
    
    // Validation et correction si nécessaire
    if (!STYLE_CONFIG[inputType] || !STYLE_CONFIG[inputType][normalized.variant]) {
        console.warn(`Variant invalide: ${normalized.variant} pour le type ${inputType}. Utilisation de 'glass' par défaut.`);
        normalized.variant = 'glass';
    }
    
    if (!SIZES[normalized.size]) {
        console.warn(`Taille invalide: ${normalized.size}. Utilisation de 'md' par défaut.`);
        normalized.size = 'md';
    }
    
    // Pour color, on accepte les couleurs DaisyUI + personnalisées
    if (normalized.color && !COLORS[normalized.color] && !normalized.color.startsWith('color-')) {
        console.warn(`Couleur invalide: ${normalized.color}. Utilisation de 'primary' par défaut.`);
        normalized.color = 'primary';
    }
    
    return normalized;
}

/**
 * Valide une configuration de style pour un type d'input
 * @param {String} inputType - Type d'input
 * @param {Object} styleConfig - Configuration de style
 * @returns {Boolean} - True si la configuration est valide
 */
export function validateInputStyle(inputType, styleConfig = {}) {
    if (!INPUT_TYPES[inputType]) {
        return false;
    }
    
    const { variant, size, color, animation } = styleConfig;
    
    if (variant && (!STYLE_CONFIG[inputType] || !STYLE_CONFIG[inputType][variant])) {
        return false;
    }
    
    if (size && !SIZES[size]) {
        return false;
    }
    
    if (color && !COLORS[color] && !color.startsWith('color-')) {
        return false;
    }
    
    return true;
}

/**
 * Génère les classes CSS pour un type d'input avec sa configuration de style
 * @param {String} inputType - Type d'input
 * @param {Object} styleConfig - Configuration de style
 * @param {Boolean} [error=false] - Si l'input est en erreur
 * @returns {String} - Classes CSS à appliquer
 */
export function getInputStyle(inputType, styleConfig = {}, error = false) {
    // Normalisation
    const normalized = normalizeInputStyle(inputType, styleConfig);
    const { variant, size, color, animation } = normalized;
    
    // Récupérer la configuration du style depuis le tableau
    const styleData = STYLE_CONFIG[inputType]?.[variant];
    if (!styleData) {
        console.warn(`Configuration de style non trouvée pour ${inputType}/${variant}`);
        return 'input';
    }
    
    // Classes de base
    const classes = [...styleData.classes];
    
    // Classe de taille (utilise le mapping spécifique si disponible)
    const sizeClass = SIZE_MAP[inputType]?.[size] || SIZE_MAP.default[size] || SIZES[size];
    classes.push(sizeClass);
    
    // Classe de couleur (utilise le mapping spécifique si disponible)
    // On génère à la fois la classe spécifique (input-primary, select-primary, etc.)
    // et la classe générique (color-primary) pour définir --color
    if (color) {
        const colorClass = COLOR_MAP[inputType]?.[color] || COLOR_MAP.default[color];
        if (colorClass) {
            classes.push(colorClass);
        } else if (color.startsWith('color-') || color.startsWith('bg-')) {
            // Couleur personnalisée
            classes.push(color);
        }
        
        // Ajouter la classe générique color-{name} pour définir --color
        // Cette classe sera utilisée dans les styles SCSS via var(--color)
        if (COLORS[color] || ['primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error', 'neutral'].includes(color)) {
            classes.push(`color-${color}`);
        }
    }
    
    // Animation
    if (animation) {
        if (typeof animation === 'boolean') {
            // Animation par défaut pour le type d'input et variant
            classes.push(...styleData.animations);
        } else if (typeof animation === 'string') {
            // Animation personnalisée
            classes.push(animation);
        }
    }
    
    // État d'erreur
    if (error) {
        classes.push('input-error');
    }
    
    return classes.join(' ');
}

/**
 * Extrait les propriétés de style pour transmission aux labels et helpers
 * @param {String} inputType - Type d'input
 * @param {Object} styleConfig - Configuration de style
 * @returns {Object} - Propriétés de style extraites
 */
export function getInputStyleProperties(inputType, styleConfig = {}) {
    const normalized = normalizeInputStyle(inputType, styleConfig);
    
    return {
        variant: normalized.variant,
        size: normalized.size,
        color: normalized.color,
        animation: normalized.animation,
        // Propriétés spécifiques pour les labels
        labelSize: normalized.size,
        labelColor: normalized.color,
        // Propriétés spécifiques pour les helpers
        helperSize: normalized.size,
        helperColor: normalized.color
    };
}

/**
 * Fusionne intelligemment deux configurations de style
 * Reconnaît automatiquement les propriétés et complète avec les valeurs par défaut
 * @param {Object} baseConfig - Configuration de base
 * @param {Object|String} overrideConfig - Configuration à fusionner (peut être un objet ou une string)
 * @param {Object} defaults - Valeurs par défaut si manquantes
 * @returns {Object} - Configuration fusionnée
 */
export function mergeStyleConfig(baseConfig = {}, overrideConfig = {}, defaults = { variant: 'glass', size: 'md', color: 'primary', animation: true }) {
    // Si overrideConfig est une string, on l'interprète comme une couleur
    if (typeof overrideConfig === 'string') {
        overrideConfig = { color: overrideConfig };
    }
    
    // Si overrideConfig n'est pas un objet valide, on retourne la base
    if (typeof overrideConfig !== 'object' || overrideConfig === null) {
        return { ...defaults, ...baseConfig };
    }
    
    // Fusion intelligente
    const merged = { ...defaults, ...baseConfig };
    
    // Reconnaissance automatique des propriétés
    Object.entries(overrideConfig).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            // Reconnaissance des types de propriétés
            if (key === 'variant' || VARIANTS[value]) {
                merged.variant = value;
            } else if (key === 'size' || SIZES[value]) {
                merged.size = value;
            } else if (key === 'color' || COLORS[value] || value.startsWith('color-') || value.startsWith('bg-')) {
                merged.color = value;
            } else if (key === 'animation' || typeof value === 'boolean' || typeof value === 'string') {
                merged.animation = value;
            } else {
                // Propriété personnalisée
                merged[key] = value;
            }
        }
    });
    
    return merged;
}

// --- HELPERS RAPIDES ---

/**
 * Crée une configuration de style rapide
 * @param {String} variant - Variant du style
 * @param {Object} options - Options supplémentaires
 * @returns {Object} - Configuration de style
 */
export function createInputStyle(variant, options = {}) {
    return {
        variant,
        size: options.size || 'md',
        color: options.color || 'primary',
        animation: options.animation !== undefined ? options.animation : true
    };
}

/**
 * Helpers rapides pour chaque variant
 */
export const quickStyles = {
    glass: (options = {}) => createInputStyle('glass', options),
    dash: (options = {}) => createInputStyle('dash', options),
    outline: (options = {}) => createInputStyle('outline', options),
    ghost: (options = {}) => createInputStyle('ghost', options),
    soft: (options = {}) => createInputStyle('soft', options)
};

// --- EXPORTS DES CONSTANTES ---
export { STYLE_CONFIG as styleMap, COLORS as colorMap, SIZES as sizeMap, VARIANTS as variantMap }; 
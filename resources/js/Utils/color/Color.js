import { colord } from 'colord';

/**
 * Utilitaire de gestion des couleurs basé sur colord
 * 
 * @description
 * Utilitaire complet pour la génération, manipulation et conversion de couleurs.
 * Utilise colord pour une gestion robuste des couleurs avec support de tous les formats.
 * 
 * @see https://github.com/omgovich/colord
 */

// Options par défaut
const DEFAULT_OPTIONS = {
  format: 'auto', // 'auto' = garde le format d'entrée, sinon 'hex', 'rgb', 'hsl', etc.
  fallback: '#3b82f6', // Couleur de fallback
  normalize: {
    minLightness: 0.2,
    maxLightness: 0.8,
    minSaturation: 0.25,
    maxSaturation: 0.95
  }
};

/**
 * Valide si une couleur est valide
 * 
 * @param {string} color - La couleur à valider
 * @param {string} format - Format attendu (optionnel)
 * @returns {boolean} - true si la couleur est valide
 */
export function isValidColor(color, format = null) {
  if (!color || typeof color !== 'string') {
    return false;
  }

  const colorObj = colord(color);
  
  if (!colorObj.isValid()) {
    return false;
  }

  if (format) {
    // Validation spécifique au format
    switch (format.toLowerCase()) {
      case 'hex':
        return color.startsWith('#');
      case 'rgb':
        return color.startsWith('rgb(') || color.startsWith('rgba(');
      case 'hsl':
        return color.startsWith('hsl(') || color.startsWith('hsla(');
      default:
        return true;
    }
  }

  return true;
}

/**
 * Génère une couleur à partir d'une chaîne de caractères
 * 
 * @param {string} input - La chaîne d'entrée
 * @param {Object} options - Options de génération
 * @param {Object} options.adjustments - Ajustements à appliquer
 * @param {number} options.adjustments.saturation - Ajustement de saturation (-1 à 1)
 * @param {number} options.adjustments.lightness - Ajustement de luminosité (-1 à 1)
 * @param {string} options.format - Format de sortie
 * @param {string} options.fallback - Couleur de fallback
 * @param {Object} options.normalize - Contraintes de normalisation
 * @returns {string} - La couleur générée
 */
export function generateColorFromString(input, options = {}) {
  const opts = { ...DEFAULT_OPTIONS, ...options };
  
  if (!input || typeof input !== 'string' || input.trim().length === 0) {
    return opts.fallback;
  }

  // Normaliser l'entrée
  const normalizedInput = input.toLowerCase().trim();
  
  // Générer un hash de la chaîne
  let hash = 0;
  for (let i = 0; i < normalizedInput.length; i++) {
    const char = normalizedInput.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convert to 32bit integer
  }

  // Convertir le hash en couleur hex (gestion des valeurs négatives)
  const r = Math.abs((hash & 0xFF0000) >> 16);
  const g = Math.abs((hash & 0x00FF00) >> 8);
  const b = Math.abs(hash & 0x0000FF);
  
  let color = colord(`rgb(${r}, ${g}, ${b})`);

  // Appliquer les ajustements
  if (opts.adjustments) {
    const adjustedColor = adjustColor(color.toHex(), opts.adjustments, { format: 'hex' });
    color = colord(adjustedColor);
  }

  // Normaliser si demandé
  if (opts.normalize) {
    const normalizedColor = normalizeColor(color.toHex(), opts.normalize, { format: 'hex' });
    color = colord(normalizedColor);
  }

  // Convertir au format demandé
  return convertColor(color.toHex(), opts.format, opts.fallback);
}

/**
 * Ajuste une couleur selon les paramètres fournis
 * 
 * @param {string} color - La couleur à ajuster
 * @param {Object} adjustments - Ajustements à appliquer
 * @param {number} adjustments.saturation - Ajustement de saturation (-1 à 1)
 * @param {number} adjustments.lightness - Ajustement de luminosité (-1 à 1)
 * @param {number} adjustments.hue - Ajustement de teinte (-360 à 360)
 * @param {Object} options - Options de conversion
 * @returns {string} - La couleur ajustée
 */
export function adjustColor(color, adjustments = {}, options = {}) {
  const opts = { ...DEFAULT_OPTIONS, ...options };
  
  if (!isValidColor(color)) {
    return opts.fallback;
  }

  let colorObj = colord(color);

  // Appliquer les ajustements
  if (adjustments.saturation !== undefined) {
    if (adjustments.saturation > 0) {
      colorObj = colorObj.saturate(adjustments.saturation);
    } else {
      colorObj = colorObj.desaturate(Math.abs(adjustments.saturation));
    }
  }

  if (adjustments.lightness !== undefined) {
    if (adjustments.lightness > 0) {
      colorObj = colorObj.lighten(adjustments.lightness);
    } else {
      colorObj = colorObj.darken(Math.abs(adjustments.lightness));
    }
  }

  if (adjustments.hue !== undefined) {
    colorObj = colorObj.rotate(adjustments.hue);
  }

  return convertColor(colorObj.toHex(), opts.format, opts.fallback);
}

/**
 * Normalise une couleur selon des contraintes
 * 
 * @param {string} color - La couleur à normaliser
 * @param {Object} constraints - Contraintes de normalisation
 * @param {number} constraints.minLightness - Luminosité minimale (0-1)
 * @param {number} constraints.maxLightness - Luminosité maximale (0-1)
 * @param {number} constraints.minSaturation - Saturation minimale (0-1)
 * @param {number} constraints.maxSaturation - Saturation maximale (0-1)
 * @param {Object} options - Options de conversion
 * @returns {string} - La couleur normalisée
 */
export function normalizeColor(color, constraints = {}, options = {}) {
  const opts = { ...DEFAULT_OPTIONS, ...options };
  
  if (!isValidColor(color)) {
    return opts.fallback;
  }

  let colorObj = colord(color);
  const hsl = colorObj.toHsl();

  // Ajuster la luminosité
  if (constraints.minLightness !== undefined && hsl.l < constraints.minLightness) {
    const adjustment = Math.min(constraints.minLightness - hsl.l, 0.3); // Limiter l'ajustement
    colorObj = colorObj.lighten(adjustment);
  } else if (constraints.maxLightness !== undefined && hsl.l > constraints.maxLightness) {
    const adjustment = Math.min(hsl.l - constraints.maxLightness, 0.3); // Limiter l'ajustement
    colorObj = colorObj.darken(adjustment);
  }

  // Ajuster la saturation
  if (constraints.minSaturation !== undefined && hsl.s < constraints.minSaturation) {
    const adjustment = Math.min(constraints.minSaturation - hsl.s, 0.3); // Limiter l'ajustement
    colorObj = colorObj.saturate(adjustment);
  } else if (constraints.maxSaturation !== undefined && hsl.s > constraints.maxSaturation) {
    const adjustment = Math.min(hsl.s - constraints.maxSaturation, 0.3); // Limiter l'ajustement
    colorObj = colorObj.desaturate(adjustment);
  }

  // Validation finale : s'assurer que la couleur est valide
  if (!colorObj.isValid()) {
    return opts.fallback;
  }

  return convertColor(colorObj.toHex(), opts.format, opts.fallback);
}

/**
 * Convertit une couleur vers un format spécifique
 * 
 * @param {string} color - La couleur à convertir
 * @param {string} targetFormat - Format de sortie ('auto', 'hex', 'rgb', 'hsl', etc.)
 * @param {string} fallback - Couleur de fallback
 * @returns {string} - La couleur convertie
 */
export function convertColor(color, targetFormat = 'auto', fallback = DEFAULT_OPTIONS.fallback) {
  if (!isValidColor(color)) {
    return fallback;
  }

  const colorObj = colord(color);

  // Si 'auto', garder le format d'entrée
  if (targetFormat === 'auto') {
    if (color.startsWith('#')) {
      return colorObj.toHex();
    } else if (color.startsWith('rgb')) {
      return colorObj.toRgbString();
    } else if (color.startsWith('hsl')) {
      return colorObj.toHslString();
    } else {
      return colorObj.toHex(); // Format par défaut
    }
  }

  // Conversion vers le format demandé
  switch (targetFormat.toLowerCase()) {
    case 'hex':
      return colorObj.toHex();
    case 'rgb':
      return colorObj.toRgbString();
    case 'hsl':
      return colorObj.toHslString();
    case 'hsv':
      return colorObj.toHsvString();
    case 'lab':
      return colorObj.toLabString();
    case 'lch':
      return colorObj.toLchString();
    case 'oklch':
      return colorObj.toOklchString();
    default:
      return colorObj.toHex();
  }
}

/**
 * Calcule le ratio de contraste entre deux couleurs
 * 
 * @param {string} color1 - Première couleur
 * @param {string} color2 - Deuxième couleur
 * @returns {number} - Ratio de contraste
 */
export function getContrastRatio(color1, color2) {
  if (!isValidColor(color1) || !isValidColor(color2)) {
    return 1; // Contraste minimal en cas d'erreur
  }

  return colord(color1).contrast(colord(color2));
}

/**
 * Vérifie si le contraste entre deux couleurs respecte les normes WCAG
 * 
 * @param {string} color1 - Première couleur
 * @param {string} color2 - Deuxième couleur
 * @param {string} level - Niveau WCAG ('AA' ou 'AAA')
 * @param {string} size - Taille du texte ('normal' ou 'large')
 * @returns {boolean} - true si le contraste est suffisant
 */
export function isContrastValid(color1, color2, level = 'AA', size = 'normal') {
  const ratio = getContrastRatio(color1, color2);
  
  // Seuils WCAG
  const thresholds = {
    'AA': { normal: 4.5, large: 3 },
    'AAA': { normal: 7, large: 4.5 }
  };

  const threshold = thresholds[level]?.[size] || thresholds.AA.normal;
  return ratio >= threshold;
}

/**
 * Ajuste une couleur pour obtenir un contraste suffisant avec une couleur cible
 * 
 * @param {string} baseColor - Couleur de base à ajuster
 * @param {string} targetColor - Couleur cible pour le contraste
 * @param {string} level - Niveau WCAG ('AA' ou 'AAA')
 * @param {string} size - Taille du texte ('normal' ou 'large')
 * @param {Object} options - Options d'ajustement
 * @returns {string} - La couleur ajustée
 */
export function adjustForContrast(baseColor, targetColor, level = 'AA', size = 'normal', options = {}) {
  const opts = { ...DEFAULT_OPTIONS, ...options };
  
  if (!isValidColor(baseColor) || !isValidColor(targetColor)) {
    return opts.fallback;
  }

  let adjustedColor = colord(baseColor);
  const maxAttempts = 10;
  let attempts = 0;

  while (!isContrastValid(adjustedColor.toHex(), targetColor, level, size) && attempts < maxAttempts) {
    // Alterner entre éclaircir et assombrir
    if (attempts % 2 === 0) {
      adjustedColor = adjustedColor.lighten(0.1);
    } else {
      adjustedColor = adjustedColor.darken(0.1);
    }
    attempts++;
  }

  return convertColor(adjustedColor.toHex(), opts.format, opts.fallback);
}

/**
 * Trouve la couleur Tailwind la plus proche
 * 
 * @param {string} color - La couleur à convertir
 * @param {Object} options - Options de conversion
 * @returns {string} - La couleur Tailwind (ex: "red-500")
 */
export function getNearestTailwindColor(color, options = {}) {
  const opts = { ...DEFAULT_OPTIONS, ...options };
  
  if (!isValidColor(color)) {
    return 'blue-500'; // Couleur Tailwind de fallback
  }

  // Palette de couleurs Tailwind (simplifiée)
  const tailwindColors = {
    red: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    orange: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    amber: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    yellow: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    lime: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    green: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    emerald: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    teal: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    cyan: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    sky: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    blue: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    indigo: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    violet: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    purple: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    fuchsia: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    pink: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    rose: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    slate: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    gray: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    zinc: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    neutral: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900],
    stone: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900]
  };

  const colorObj = colord(color);
  const hsl = colorObj.toHsl();
  
  let bestMatch = { color: 'blue', intensity: 500, distance: Infinity };

  // Trouver la couleur la plus proche
  for (const [colorName, intensities] of Object.entries(tailwindColors)) {
    for (const intensity of intensities) {
      // Convertir l'intensité Tailwind en HSL approximatif
      const tailwindHSL = intensityToHSL(intensity);
      
      // Calculer la distance (simplifié)
      const distance = Math.sqrt(
        Math.pow(hsl.h - tailwindHSL.h, 2) +
        Math.pow(hsl.s - tailwindHSL.s, 2) +
        Math.pow(hsl.l - tailwindHSL.l, 2)
      );

      if (distance < bestMatch.distance) {
        bestMatch = { color: colorName, intensity, distance };
      }
    }
  }

  return `${bestMatch.color}-${bestMatch.intensity}`;
}

/**
 * Convertit une couleur vers le format Tailwind
 * 
 * @param {string} color - La couleur à convertir
 * @param {number} intensity - Intensité spécifique (optionnel)
 * @param {Object} options - Options de conversion
 * @returns {string} - La couleur Tailwind
 */
export function convertToTailwind(color, intensity = null, options = {}) {
  if (!isValidColor(color)) {
    return 'blue-500';
  }

  if (intensity) {
    // Utiliser l'intensité spécifiée
    const colorObj = colord(color);
    const hsl = colorObj.toHsl();
    
    // Trouver la couleur de base la plus proche
    const baseColor = getNearestTailwindColor(color);
    const colorName = baseColor.split('-')[0];
    
    return `${colorName}-${intensity}`;
  }

  return getNearestTailwindColor(color, options);
}

/**
 * Obtient des informations détaillées sur une couleur
 * 
 * @param {string} color - La couleur à analyser
 * @returns {Object} - Informations sur la couleur
 */
export function getColorInfo(color) {
  if (!isValidColor(color)) {
    return null;
  }

  const colorObj = colord(color);
  
  return {
    hex: colorObj.toHex(),
    rgb: colorObj.toRgb(),
    hsl: colorObj.toHsl(),
    hsv: colorObj.toHsv(),
    lab: colorObj.toLab(),
    lch: colorObj.toLch(),
    oklch: colorObj.toOklch(),
    alpha: colorObj.alpha(),
    isDark: colorObj.isDark(),
    isLight: colorObj.isLight(),
    luminance: colorObj.luminance()
  };
}

// Fonctions utilitaires privées

/**
 * Convertit une intensité Tailwind en HSL approximatif
 * 
 * @param {number} intensity - Intensité Tailwind (50-900)
 * @returns {Object} - Valeurs HSL approximatives
 */
function intensityToHSL(intensity) {
  // Mapping approximatif des intensités Tailwind vers HSL
  const mappings = {
    50: { h: 0, s: 0, l: 0.98 },
    100: { h: 0, s: 0, l: 0.95 },
    200: { h: 0, s: 0, l: 0.9 },
    300: { h: 0, s: 0, l: 0.8 },
    400: { h: 0, s: 0, l: 0.7 },
    500: { h: 0, s: 0, l: 0.6 },
    600: { h: 0, s: 0, l: 0.5 },
    700: { h: 0, s: 0, l: 0.4 },
    800: { h: 0, s: 0, l: 0.3 },
    900: { h: 0, s: 0, l: 0.2 }
  };

  return mappings[intensity] || { h: 0, s: 0, l: 0.5 };
}

// Fonctions de compatibilité pour l'ancien système

/**
 * @deprecated Utilisez generateColorFromString à la place
 */
export function getColorFromString(input, intensity = 500) {
  console.warn('getColorFromString est déprécié, utilisez generateColorFromString');
  return generateColorFromString(input, { format: 'hex' });
}

/**
 * @deprecated Utilisez generateColorFromString avec normalize à la place
 */
export function getAvatarColor(label, fallbackColor = "primary-500") {
  console.warn('getAvatarColor est déprécié, utilisez generateColorFromString avec normalize');
  return generateColorFromString(label, {
    normalize: DEFAULT_OPTIONS.normalize,
    format: 'hex',
    fallback: fallbackColor
  });
}

/**
 * @deprecated Utilisez adjustColor à la place
 */
export function adjustIntensityColor(color, adjustment = 2, direction = "auto") {
  console.warn('adjustIntensityColor est déprécié, utilisez adjustColor');
  
  const adjustments = {};
  if (direction === 'auto') {
    adjustments.lightness = adjustment * 0.1;
  } else if (direction === 'decrease') {
    adjustments.lightness = -adjustment * 0.1;
  } else if (direction === 'augmentation') {
    adjustments.lightness = adjustment * 0.1;
  }
  
  return adjustColor(color, adjustments, { format: 'hex' });
}

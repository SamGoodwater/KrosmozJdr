/**
 * EntityDescriptorHelpers — Fonctions de formatage communes
 *
 * @description
 * Ce fichier centralise toutes les fonctions de formatage utilisées par les descriptors.
 * Ces fonctions peuvent être utilisées dans les descriptors ou dans les adapters.
 */

import { RARITY_OPTIONS, VISIBILITY_OPTIONS, HOSTILITY_OPTIONS, BREAKPOINTS, SCREEN_SIZES } from "./EntityDescriptorConstants.js";

/**
 * Tronque un texte avec des ellipses.
 *
 * @param {any} value - Valeur à tronquer (sera convertie en string)
 * @param {number} max - Longueur maximale (défaut: 40)
 * @returns {string} Texte tronqué avec "…" si nécessaire
 */
export function truncate(value, max = 40) {
  const s = String(value ?? "");
  if (!s) return "";
  if (s.length <= max) return s;
  return s.slice(0, Math.max(0, max - 1)) + "…";
}

/**
 * Capitalise la première lettre d'une chaîne.
 *
 * @param {string} value
 * @returns {string}
 */
export function capitalize(value) {
  if (!value) return "";
  return String(value).charAt(0).toUpperCase() + String(value).slice(1).toLowerCase();
}

/**
 * Formate une valeur de rareté.
 *
 * @param {number} value - Valeur de rareté (0-5)
 * @param {Object} options - Options de formatage
 * @param {boolean} [options.showLabel=true] - Afficher le label
 * @param {boolean} [options.showIcon=false] - Afficher l'icône
 * @returns {string|Object} Label ou objet avec label/color/icon
 */
export function formatRarity(value, options = {}) {
  const { showLabel = true, showIcon = false } = options;
  const option = RARITY_OPTIONS.find((opt) => opt.value === value);
  if (!option) return showLabel ? `Rareté ${value}` : "";

  if (showLabel && showIcon) {
    return { label: option.label, color: option.color, icon: option.icon };
  }
  if (showLabel) {
    return option.label;
  }
  return "";
}

/**
 * Formate une valeur de visibilité.
 *
 * @param {string} value - Valeur de visibilité
 * @returns {string} Label formaté
 */
export function formatVisibility(value) {
  const option = VISIBILITY_OPTIONS.find((opt) => opt.value === value);
  return option?.label || capitalize(value);
}

/**
 * Formate une valeur d'hostilité.
 *
 * @param {number} value - Valeur d'hostilité (0-4)
 * @returns {string} Label formaté
 */
export function formatHostility(value) {
  const option = HOSTILITY_OPTIONS.find((opt) => opt.value === value);
  return option?.label || `Hostilité ${value}`;
}

/**
 * Formate une date selon la taille d'écran.
 *
 * @param {string|Date} value - Date à formater
 * @param {string} size - Taille d'écran (xs, sm, md, lg, xl, auto)
 * @returns {string} Date formatée
 */
export function formatDate(value, size = "auto") {
  if (!value) return "";

  const date = value instanceof Date ? value : new Date(value);
  if (isNaN(date.getTime())) return "";

  const actualSize = size === "auto" ? getCurrentScreenSize() : size;

  // Format court pour petits écrans
  if (actualSize === "xs" || actualSize === "sm") {
    return date.toLocaleDateString("fr-FR", { day: "2-digit", month: "2-digit", year: "2-digit" });
  }

  // Format complet pour grands écrans
  return date.toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/**
 * Formate un nombre avec séparateurs de milliers.
 *
 * @param {number|string} value - Nombre à formater
 * @param {Object} options - Options de formatage
 * @param {number} [options.decimals=0] - Nombre de décimales
 * @returns {string} Nombre formaté
 */
export function formatNumber(value, options = {}) {
  const { decimals = 0 } = options;
  const num = typeof value === "string" ? parseFloat(value) : value;
  if (isNaN(num)) return "";

  return new Intl.NumberFormat("fr-FR", {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals,
  }).format(num);
}

/**
 * Formate une URL d'image pour l'affichage.
 *
 * @param {string} url - URL de l'image
 * @param {Object} options - Options de formatage
 * @param {string} [options.size="thumb"] - Taille (thumb, small, medium, large)
 * @returns {string} URL formatée
 */
export function formatImageUrl(url, options = {}) {
  const { size = "thumb" } = options;
  if (!url) return "";

  // Si l'URL est déjà complète, la retourner telle quelle
  if (url.startsWith("http://") || url.startsWith("https://")) {
    return url;
  }

  // Sinon, construire l'URL complète (à adapter selon votre système)
  return url;
}

/**
 * Obtient la taille d'écran actuelle.
 *
 * @returns {string} Taille d'écran (xs, sm, md, lg, xl)
 */
export function getCurrentScreenSize() {
  if (typeof window === "undefined") return "md"; // SSR fallback

  const width = window.innerWidth;
  if (width < BREAKPOINTS.sm) return "xs";
  if (width < BREAKPOINTS.md) return "sm";
  if (width < BREAKPOINTS.lg) return "md";
  if (width < BREAKPOINTS.xl) return "lg";
  return "xl";
}

/**
 * Soustrait une taille d'écran (adaptation progressive).
 *
 * @param {string} size - Taille actuelle (xs, sm, md, lg, xl)
 * @param {number} steps - Nombre de niveaux à soustraire (défaut: 1)
 * @returns {string} Taille résultante
 *
 * @example
 * subtractSize('lg', 1) // 'md'
 * subtractSize('md', 2) // 'xs'
 */
export function subtractSize(size, steps = 1) {
  const sizes = SCREEN_SIZES;
  const index = sizes.indexOf(size);
  if (index === -1) return size; // Taille invalide, retourner telle quelle
  return sizes[Math.max(0, index - steps)];
}

/**
 * Ajoute une taille d'écran (adaptation progressive).
 *
 * @param {string} size - Taille actuelle (xs, sm, md, lg, xl)
 * @param {number} steps - Nombre de niveaux à ajouter (défaut: 1)
 * @returns {string} Taille résultante
 *
 * @example
 * addSize('md', 1) // 'lg'
 * addSize('sm', 2) // 'lg'
 */
export function addSize(size, steps = 1) {
  const sizes = SCREEN_SIZES;
  const index = sizes.indexOf(size);
  if (index === -1) return size; // Taille invalide, retourner telle quelle
  return sizes[Math.min(sizes.length - 1, index + steps)];
}

/**
 * Formate une valeur selon la taille d'écran et les options.
 *
 * @param {any} value - Valeur à formater
 * @param {Object} options - Options de formatage
 * @param {string} [options.type="text"] - Type de formatage (text, number, date, etc.)
 * @param {string} [options.size="auto"] - Taille d'écran (xs, sm, md, lg, xl, auto)
 * @param {number} [options.truncate] - Longueur de troncature
 * @param {string} [options.mode] - Mode d'affichage (route, badge, etc.)
 * @returns {string|Object} Valeur formatée
 */
export function formatValue(value, options = {}) {
  const { type = "text", size = "auto", truncate: maxLength, mode } = options;
  const actualSize = size === "auto" ? getCurrentScreenSize() : size;

  // Formatage selon le type
  switch (type) {
    case "number":
      return formatNumber(value, options);

    case "date":
      return formatDate(value, actualSize);

    case "text":
    default:
      let formatted = String(value ?? "");
      if (maxLength) {
        formatted = truncate(formatted, maxLength);
      }
      return formatted;
  }
}

/**
 * Valide qu'une valeur est dans une liste d'options.
 *
 * @param {any} value - Valeur à valider
 * @param {Array} options - Liste d'options
 * @returns {boolean}
 */
export function validateOption(value, options) {
  if (!Array.isArray(options)) return false;
  return options.some((opt) => {
    const optValue = typeof opt === "object" ? opt.value : opt;
    return optValue === value;
  });
}

/**
 * Obtient le label d'une option depuis sa valeur.
 *
 * @param {any} value - Valeur de l'option
 * @param {Array} options - Liste d'options
 * @returns {string} Label ou valeur par défaut
 */
export function getOptionLabel(value, options) {
  if (!Array.isArray(options)) return String(value ?? "");
  const option = options.find((opt) => {
    const optValue = typeof opt === "object" ? opt.value : opt;
    return optValue === value;
  });
  return typeof option === "object" ? option.label : String(option ?? value);
}

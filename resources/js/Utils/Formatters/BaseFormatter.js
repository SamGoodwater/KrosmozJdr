/**
 * BaseFormatter — Classe abstraite de base pour tous les formatters
 *
 * @description
 * Classe abstraite qui fournit la structure commune à tous les formatters.
 * Tous les formatters doivent hériter de cette classe et implémenter les méthodes abstraites.
 *
 * @abstract
 */
export class BaseFormatter {
  /**
   * Nom du formatter (utilisé pour l'enregistrement dans le registre)
   * @abstract
   * @type {string}
   */
  static name = null;

  /**
   * Clés de champs supportées par ce formatter
   * @abstract
   * @type {string[]}
   */
  static fieldKeys = [];

  /**
   * Formate une valeur en label texte
   *
   * @param {any} value - Valeur à formater
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    throw new Error(`format() must be implemented by ${this.name || 'subclass'}`);
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {any} value - Valeur à formater
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {any} [options.sortValue] - Valeur pour le tri (par défaut: value)
   * @param {string} [options.searchValue] - Valeur pour la recherche (par défaut: label)
   * @param {any} [options.filterValue] - Valeur pour le filtre
   * @returns {Object|null} Objet Cell {type, value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    throw new Error(`toCell() must be implemented by ${this.name || 'subclass'}`);
  }

  /**
   * Vérifie si une valeur est valide pour ce formatter
   *
   * @param {any} value - Valeur à vérifier
   * @returns {boolean} true si la valeur est valide
   */
  static isValid(value) {
    return value !== null && value !== undefined;
  }

  /**
   * Normalise la taille d'écran
   *
   * @param {string} size - Taille d'écran (xs, sm, md, lg, xl, auto)
   * @returns {string} Taille normalisée (xs, sm, md, lg, xl)
   */
  static normalizeSize(size) {
    if (!size || size === 'auto') {
      // Par défaut, utiliser 'md' si auto ou non spécifié
      return 'md';
    }
    const validSizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    return validSizes.includes(size) ? size : 'md';
  }

  /**
   * Génère une cellule texte standardisée
   *
   * @param {string} text - Texte à afficher
   * @param {Object} [options={}] - Options
   * @param {any} [options.sortValue] - Valeur pour le tri
   * @param {string} [options.searchValue] - Valeur pour la recherche
   * @param {any} [options.filterValue] - Valeur pour le filtre
   * @returns {Object} Objet Cell {type: 'text', value, params}
   */
  static buildTextCell(text, options = {}) {
    const displayText = text === null || text === undefined || text === '' ? '-' : String(text);
    return {
      type: 'text',
      value: displayText,
      params: {
        sortValue: options.sortValue ?? displayText,
        searchValue: options.searchValue ?? (displayText === '-' ? '' : displayText),
        filterValue: options.filterValue,
      },
    };
  }

  /**
   * Génère une cellule badge standardisée
   *
   * @param {string} label - Label du badge
   * @param {string} color - Couleur DaisyUI (primary, secondary, success, warning, error, info, neutral)
   * @param {Object} [options={}] - Options
   * @param {any} [options.sortValue] - Valeur pour le tri
   * @param {any} [options.filterValue] - Valeur pour le filtre
   * @param {string} [options.icon] - Icône FontAwesome (optionnel)
   * @returns {Object} Objet Cell {type: 'badge', value, params}
   */
  static buildBadgeCell(label, color, options = {}) {
    return {
      type: 'badge',
      value: label,
      params: {
        color,
        sortValue: options.sortValue ?? label,
        filterValue: options.filterValue,
        icon: options.icon,
      },
    };
  }

  /**
   * Génère une cellule booléenne standardisée
   *
   * @param {any} value - Valeur booléenne
   * @param {string} [mode='badge'] - Mode d'affichage ('badge' ou 'text')
   * @param {Object} [options={}] - Options
   * @returns {Object} Objet Cell {type: 'badge'|'text', value, params}
   */
  static buildBoolCell(value, mode = 'badge', options = {}) {
    const boolValue = value === 1 || value === true || String(value) === '1';
    const label = boolValue ? 'Oui' : 'Non';
    const sortValue = boolValue ? 1 : 0;

    if (mode === 'badge') {
      return this.buildBadgeCell(label, boolValue ? 'success' : 'neutral', {
        sortValue,
        ...options,
      });
    }

    return this.buildTextCell(label, {
      sortValue,
      ...options,
    });
  }
}

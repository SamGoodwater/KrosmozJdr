/**
 * EntityDescriptor — Classe de base pour tous les descriptors d'entités
 *
 * @description
 * ⚠️ DÉPRÉCIÉ : Cette classe n'est plus utilisée dans le nouveau système.
 * Les descriptors sont maintenant des objets simples retournés par des fonctions (ex: getResourceFieldDescriptors).
 * 
 * Cette classe est conservée uniquement pour :
 * - Les constantes statiques (RARITY_OPTIONS, etc.) - utilisez EntityDescriptorConstants directement
 * - Les helpers de validation (validateFieldDescriptor) - peut être utile pour le debug
 * - La rétrocompatibilité temporaire
 *
 * @deprecated Utilisez directement les fonctions get*FieldDescriptors() et les formatters centralisés
 * @example
 * // ❌ Ancien système (déprécié)
 * class ResourceDescriptor extends EntityDescriptor { ... }
 * 
 * // ✅ Nouveau système
 * import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
 * const descriptors = getResourceFieldDescriptors(ctx);
 */

import {
  RARITY_OPTIONS,
  VISIBILITY_OPTIONS,
  BREAKPOINTS,
  SCREEN_SIZES,
  CELL_TYPES,
  FORM_TYPES,
  RECOMMENDED_GROUPS,
  FIELD_FORMATS,
} from "./EntityDescriptorConstants.js";

import {
  truncate,
  capitalize,
  formatRarity,
  formatVisibility,
  formatDate,
  formatNumber,
  formatValue,
  getCurrentScreenSize,
  subtractSize,
  addSize,
  validateOption,
  getOptionLabel,
} from "./EntityDescriptorHelpers.js";

/**
 * Classe de base EntityDescriptor
 */
export class EntityDescriptor {
  /**
   * @param {string} entityType - Type d'entité (ex: 'resource', 'item', 'spell')
   */
  constructor(entityType) {
    if (!entityType) {
      throw new Error("EntityDescriptor: 'entityType' est obligatoire");
    }

    this.entityType = entityType;
    this.errors = [];

    // Valeurs par défaut
    this.defaults = {
      format: "text",
      color: "auto",
      showInCompact: true,
      required: false,
      bulkEnabled: false,
      bulkNullable: true,
    };
  }

  // ============================================
  // CONSTANTES COMMUNES (accessibles statiquement)
  // ============================================

  static get RARITY_OPTIONS() {
    return RARITY_OPTIONS;
  }

  static get VISIBILITY_OPTIONS() {
    return VISIBILITY_OPTIONS;
  }

  static get BREAKPOINTS() {
    return BREAKPOINTS;
  }

  static get SCREEN_SIZES() {
    return SCREEN_SIZES;
  }

  static get CELL_TYPES() {
    return CELL_TYPES;
  }

  static get FORM_TYPES() {
    return FORM_TYPES;
  }

  static get RECOMMENDED_GROUPS() {
    return RECOMMENDED_GROUPS;
  }

  // ============================================
  // FONCTIONS COMMUNES (accessibles via instance)
  // ============================================

  /**
   * Tronque un texte.
   * @param {any} value
   * @param {number} max
   * @returns {string}
   */
  truncate(value, max = 40) {
    return truncate(value, max);
  }

  /**
   * Capitalise une chaîne.
   * @param {string} value
   * @returns {string}
   */
  capitalize(value) {
    return capitalize(value);
  }

  /**
   * Formate une rareté.
   * @param {number} value
   * @param {Object} options
   * @returns {string|Object}
   */
  formatRarity(value, options = {}) {
    return formatRarity(value, options);
  }

  /**
   * Formate une visibilité.
   * @param {string} value
   * @returns {string}
   */
  formatVisibility(value) {
    return formatVisibility(value);
  }

  /**
   * Formate une date.
   * @param {string|Date} value
   * @param {string} size
   * @returns {string}
   */
  formatDate(value, size = "auto") {
    return formatDate(value, size);
  }

  /**
   * Formate un nombre.
   * @param {number|string} value
   * @param {Object} options
   * @returns {string}
   */
  formatNumber(value, options = {}) {
    return formatNumber(value, options);
  }

  /**
   * Formate une valeur selon la taille d'écran.
   * @param {any} value
   * @param {Object} options
   * @returns {string|Object}
   */
  formatValue(value, options = {}) {
    return formatValue(value, options);
  }

  /**
   * Obtient la taille d'écran actuelle.
   * @returns {string}
   */
  getCurrentScreenSize() {
    return getCurrentScreenSize();
  }

  /**
   * Soustrait une taille d'écran.
   * @param {string} size
   * @param {number} steps
   * @returns {string}
   */
  subtractSize(size, steps = 1) {
    return subtractSize(size, steps);
  }

  /**
   * Ajoute une taille d'écran.
   * @param {string} size
   * @param {number} steps
   * @returns {string}
   */
  addSize(size, steps = 1) {
    return addSize(size, steps);
  }

  // ============================================
  // VALIDATION
  // ============================================

  /**
   * Valide un descriptor de champ.
   *
   * @param {Object} descriptor - Descriptor à valider
   * @param {string} fieldKey - Clé du champ (pour les messages d'erreur)
   * @returns {boolean} true si valide
   */
  validateFieldDescriptor(descriptor, fieldKey) {
    const errors = [];

    // Validation des champs obligatoires
    if (!descriptor.key) {
      errors.push(`Le champ 'key' est obligatoire pour ${fieldKey}`);
    }
    if (!descriptor.label) {
      errors.push(`Le champ 'label' est obligatoire pour ${fieldKey}`);
    }

    // Validation du format
    if (descriptor.format && !FIELD_FORMATS[descriptor.format]) {
      errors.push(`Format invalide pour ${fieldKey}: ${descriptor.format}`);
    }

    // Validation de edit.form si présent
    if (descriptor.edit?.form) {
      const form = descriptor.edit.form;
      if (!FORM_TYPES.includes(form.type)) {
        errors.push(`Type de formulaire invalide pour ${fieldKey}: ${form.type}`);
      }

      // Validation de bulk si présent
      // ⚠️ NOTE: bulk.build est déprécié, les transformations sont maintenant dans les mappers
      // On ne valide plus la présence de bulk.build
    }

    // Validation de visibleIf et editableIf
    if (descriptor.visibleIf && typeof descriptor.visibleIf !== "function") {
      errors.push(`visibleIf doit être une fonction pour ${fieldKey}`);
    }
    if (descriptor.editableIf && typeof descriptor.editableIf !== "function") {
      errors.push(`editableIf doit être une fonction pour ${fieldKey}`);
    }

    if (errors.length > 0) {
      this.errors.push(...errors.map((err) => ({ field: fieldKey, error: err })));
      return false;
    }

    return true;
  }

  /**
   * Valide l'ensemble des descriptors.
   *
   * @param {Record<string, Object>} descriptors - Descriptors à valider
   * @returns {Object} { valid: boolean, errors: Array }
   */
  validate(descriptors) {
    this.errors = [];

    for (const [key, descriptor] of Object.entries(descriptors || {})) {
      this.validateFieldDescriptor(descriptor, key);
    }

    return {
      valid: this.errors.length === 0,
      errors: this.errors,
    };
  }

  // ============================================
  // MÉTHODES ABSTRAITES (à surcharger)
  // ============================================

  /**
   * Retourne les descriptors de tous les champs de l'entité.
   * À surcharger dans les classes filles.
   *
   * @param {Object} ctx - Contexte
   * @returns {Record<string, Object>} Descriptors de champs
   */
  getFieldDescriptors(ctx = {}) {
    throw new Error(`${this.constructor.name}.getFieldDescriptors() doit être implémentée`);
  }

  /**
   * Retourne la configuration du tableau.
   * À surcharger dans les classes filles.
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration du tableau
   */
  getTableConfig(ctx = {}) {
    throw new Error(`${this.constructor.name}.getTableConfig() doit être implémentée`);
  }

  /**
   * Retourne la configuration d'une vue.
   * ⚠️ DÉPRÉCIÉ : Les vues sont maintenant des composants Vue manuels (Molecules).
   * Cette méthode n'est plus utilisée.
   *
   * @deprecated Les vues sont maintenant des composants Vue manuels dans Pages/Molecules/entity/{entity}/
   * @param {string} viewName - Nom de la vue (compact, minimal, large)
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration de la vue
   */
  getViewConfig(viewName, ctx = {}) {
    throw new Error(`${this.constructor.name}.getViewConfig() est déprécié. Les vues sont maintenant des composants Vue manuels.`);
  }

  /**
   * Retourne la configuration des formulaires.
   * À surcharger dans les classes filles.
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration des formulaires
   */
  getFormConfig(ctx = {}) {
    throw new Error(`${this.constructor.name}.getFormConfig() doit être implémentée`);
  }

  /**
   * Retourne la configuration de l'édition en masse (bulk).
   * À surcharger dans les classes filles.
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration bulk
   */
  getBulkConfig(ctx = {}) {
    throw new Error(`${this.constructor.name}.getBulkConfig() doit être implémentée`);
  }
}

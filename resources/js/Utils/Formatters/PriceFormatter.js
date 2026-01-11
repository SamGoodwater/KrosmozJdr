/**
 * PriceFormatter — Formatter pour les valeurs de prix
 *
 * @description
 * Formate les valeurs de prix (nombres) en texte avec formatage monétaire.
 * Utilisé par : Resource, Item, Consumable, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class PriceFormatter extends BaseFormatter {
  static name = 'PriceFormatter';
  static fieldKeys = ['price', 'cost', 'value'];

  /**
   * Formate une valeur de prix en texte
   *
   * @param {number|string|null} value - Valeur de prix
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.currency=''] - Symbole de devise (par défaut: pas de symbole)
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(numValue) || numValue < 0) {
      return null;
    }

    const { currency = '' } = options;
    const formatted = new Intl.NumberFormat('fr-FR', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    }).format(numValue);

    return currency ? `${formatted} ${currency}` : formatted;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur de prix
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.currency=''] - Symbole de devise
   * @returns {Object|null} Objet Cell {type: 'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(numValue) || numValue < 0) {
      return null;
    }

    const label = this.format(value, options);

    return this.buildTextCell(label, {
      sortValue: numValue,
      filterValue: numValue,
    });
  }
}

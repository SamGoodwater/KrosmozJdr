/**
 * WeightFormatter — Formatter pour les valeurs de poids
 *
 * @description
 * Formate les valeurs de poids (nombres) en texte avec unité.
 * Utilisé par : Resource, Item, Consumable, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class WeightFormatter extends BaseFormatter {
  static name = 'WeightFormatter';
  static fieldKeys = ['weight', 'poids'];

  /**
   * Formate une valeur de poids en texte
   *
   * @param {number|string|null} value - Valeur de poids
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.unit=''] - Unité de poids (par défaut: pas d'unité)
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

    const { unit = '' } = options;
    const formatted = new Intl.NumberFormat('fr-FR', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
    }).format(numValue);

    return unit ? `${formatted} ${unit}` : formatted;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur de poids
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.unit=''] - Unité de poids
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

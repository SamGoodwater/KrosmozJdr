/**
 * AutoUpdateFormatter — Formatter pour les valeurs booléennes "auto_update"
 *
 * @description
 * Formate les valeurs booléennes "auto_update" (mise à jour automatique) en badges ou texte.
 * Utilisé par : Resource, Item, Consumable, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class AutoUpdateFormatter extends BaseFormatter {
  static name = 'AutoUpdateFormatter';
  static fieldKeys = ['auto_update', 'is_auto_update'];

  /**
   * Formate une valeur booléenne "auto_update" en label
   *
   * @param {boolean|number|string|null} value - Valeur booléenne
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ("Oui" ou "Non") ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const boolValue = value === 1 || value === true || String(value) === '1';
    return boolValue ? 'Oui' : 'Non';
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {boolean|number|string|null} value - Valeur booléenne
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.mode='badge'] - Mode d'affichage ('badge' ou 'text')
   * @returns {Object|null} Objet Cell {type: 'badge'|'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const { mode = 'badge' } = options;
    return this.buildBoolCell(value, mode, {
      filterValue: value === 1 || value === true || String(value) === '1' ? 1 : 0,
    });
  }
}

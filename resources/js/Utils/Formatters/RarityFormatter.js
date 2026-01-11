/**
 * RarityFormatter — Formatter pour les valeurs de rareté
 *
 * @description
 * Formate les valeurs de rareté (0-5) en labels, badges et cellules de tableau.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 *
 * Valeurs :
 * - 0: Commun (gray)
 * - 1: Peu commun (blue)
 * - 2: Rare (green)
 * - 3: Très rare (purple)
 * - 4: Légendaire (orange)
 * - 5: Unique (red)
 */

import { BaseFormatter } from './BaseFormatter.js';
import { RARITY_OPTIONS } from '../../Entities/entity/EntityDescriptorConstants.js';

export class RarityFormatter extends BaseFormatter {
  static name = 'RarityFormatter';
  static fieldKeys = ['rarity'];

  /**
   * Options de rareté
   * @type {Array<{value: number, label: string, color: string, icon: string}>}
   */
  static options = RARITY_OPTIONS;

  /**
   * Formate une valeur de rareté en label
   *
   * @param {number|string|null} value - Valeur de rareté (0-5)
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    return option?.label || `Rareté ${numValue}`;
  }

  /**
   * Génère une cellule badge pour un tableau
   *
   * @param {number|string|null} value - Valeur de rareté (0-5)
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'badge', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    if (!option) {
      // Fallback : cellule texte avec la valeur brute
      return this.buildTextCell(`Rareté ${numValue}`, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    // Convertir la couleur en couleur DaisyUI
    const daisyColor = this._mapColorToDaisyUI(option.color);

    return this.buildBadgeCell(option.label, daisyColor, {
      sortValue: numValue,
      filterValue: numValue,
      icon: option.icon,
    });
  }

  /**
   * Convertit une couleur générique en couleur DaisyUI
   *
   * @param {string} color - Couleur générique (gray, blue, green, purple, orange, red)
   * @returns {string} Couleur DaisyUI (neutral, info, success, warning, error, etc.)
   * @private
   */
  static _mapColorToDaisyUI(color) {
    const colorMap = {
      gray: 'neutral',
      blue: 'info',
      green: 'success',
      purple: 'primary',
      orange: 'warning',
      red: 'error',
    };

    return colorMap[color] || 'neutral';
  }

  /**
   * Récupère l'option de rareté pour une valeur donnée
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {Object|null} Option de rareté ou null
   */
  static getOption(value) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    return this.options.find((opt) => opt.value === numValue) || null;
  }

  /**
   * Récupère la couleur DaisyUI pour une valeur de rareté
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {string} Couleur DaisyUI
   */
  static getColor(value) {
    const option = this.getOption(value);
    return option ? this._mapColorToDaisyUI(option.color) : 'neutral';
  }

  /**
   * Récupère l'icône pour une valeur de rareté
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {string|null} Icône FontAwesome ou null
   */
  static getIcon(value) {
    const option = this.getOption(value);
    return option?.icon || null;
  }
}

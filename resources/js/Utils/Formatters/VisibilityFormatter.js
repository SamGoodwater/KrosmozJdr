/**
 * VisibilityFormatter — Formatter pour les valeurs de visibilité
 *
 * @description
 * Formate les valeurs de visibilité (guest, user, game_master, admin) en labels et badges.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';
import { VISIBILITY_OPTIONS } from '../../Entities/entity/EntityDescriptorConstants.js';

export class VisibilityFormatter extends BaseFormatter {
  static name = 'VisibilityFormatter';
  static fieldKeys = ['visibility', 'visible_to'];

  /**
   * Options de visibilité
   * @type {Array<{value: string, label: string, color: string}>}
   */
  static options = VISIBILITY_OPTIONS;

  /**
   * Formate une valeur de visibilité en label
   *
   * @param {string|null} value - Valeur de visibilité
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);
    const option = this.options.find((opt) => opt.value === strValue);

    return option?.label || this._capitalize(strValue);
  }

  /**
   * Génère une cellule badge pour un tableau
   *
   * @param {string|null} value - Valeur de visibilité
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'badge', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);
    const option = this.options.find((opt) => opt.value === strValue);

    if (!option) {
      // Fallback : cellule texte avec la valeur brute capitalisée
      return this.buildTextCell(this._capitalize(strValue), {
        sortValue: strValue,
        filterValue: strValue,
      });
    }

    // Convertir la couleur en couleur DaisyUI
    const daisyColor = this._mapColorToDaisyUI(option.color);

    return this.buildBadgeCell(option.label, daisyColor, {
      sortValue: strValue,
      filterValue: strValue,
    });
  }

  /**
   * Convertit une couleur générique en couleur DaisyUI
   *
   * @param {string} color - Couleur générique
   * @returns {string} Couleur DaisyUI
   * @private
   */
  static _mapColorToDaisyUI(color) {
    const colorMap = {
      gray: 'neutral',
      blue: 'info',
      purple: 'primary',
      red: 'error',
    };

    return colorMap[color] || 'neutral';
  }

  /**
   * Capitalise la première lettre d'une chaîne
   *
   * @param {string} str - Chaîne à capitaliser
   * @returns {string} Chaîne capitalisée
   * @private
   */
  static _capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  }
}

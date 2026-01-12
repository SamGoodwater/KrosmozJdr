/**
 * HostilityFormatter — Formatter pour les valeurs d'hostilité
 *
 * @description
 * Formate les valeurs d'hostilité (0-4) en labels et badges colorés.
 * Utilisé par : Creature uniquement
 *
 * Valeurs :
 * - 0: Amical (green)
 * - 1: Curieux (blue)
 * - 2: Neutre (gray)
 * - 3: Hostile (orange)
 * - 4: Agressif (red)
 */

import { BaseFormatter } from './BaseFormatter.js';
import { HOSTILITY_OPTIONS } from '@/Utils/Entity/Constants.js';

export class HostilityFormatter extends BaseFormatter {
  static name = 'HostilityFormatter';
  static fieldKeys = ['hostility', 'hostile_level'];

  /**
   * Options d'hostilité
   * @type {Array<{value: number, label: string, color: string}>}
   */
  static options = HOSTILITY_OPTIONS;

  /**
   * Formate une valeur d'hostilité en label
   *
   * @param {number|string|null} value - Valeur d'hostilité (0-4)
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    return option?.label || `Hostilité ${numValue}`;
  }

  /**
   * Génère une cellule badge pour un tableau
   *
   * @param {number|string|null} value - Valeur d'hostilité (0-4)
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
      return this.buildTextCell(`Hostilité ${numValue}`, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    // Convertir la couleur en couleur DaisyUI
    const daisyColor = this._mapColorToDaisyUI(option.color);

    return this.buildBadgeCell(option.label, daisyColor, {
      sortValue: numValue,
      filterValue: numValue,
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
      green: 'success',
      blue: 'info',
      gray: 'neutral',
      orange: 'warning',
      red: 'error',
    };

    return colorMap[color] || 'neutral';
  }
}

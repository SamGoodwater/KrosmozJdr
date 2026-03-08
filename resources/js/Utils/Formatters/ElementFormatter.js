/**
 * ElementFormatter — Formatter pour les éléments (Spell, Capability)
 *
 * @description
 * Formate les valeurs d'élément (0-29) en badges colorés.
 * Aligné avec App\Support\ElementConstants.
 */

import { BaseFormatter } from './BaseFormatter.js';
import { getElementLabel, getElementColor } from '@/Utils/Entity/Elements.js';

export class ElementFormatter extends BaseFormatter {
  static name = 'ElementFormatter';
  static fieldKeys = ['element', 'element_type'];

  static isValid(value) {
    if (value === null || value === undefined) return false;
    const num = typeof value === 'string' ? parseInt(value, 10) : Number(value);
    return Number.isFinite(num) && num >= 0 && num <= 29;
  }

  /**
   * Formate une valeur d'élément en label
   *
   * @param {number|string|null} value - Valeur d'élément (0-29)
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const label = getElementLabel(value);
    return label ?? `Élément ${value}`;
  }

  /**
   * Génère une cellule element pour un tableau (rendue par ElementDisplay)
   *
   * @param {number|string|null} value - Valeur d'élément (0-29)
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'element', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const label = getElementLabel(numValue);

    if (!label) {
      return this.buildTextCell(`Élément ${numValue}`, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    return {
      type: 'element',
      value: label,
      params: {
        element: numValue,
        sortValue: numValue,
        filterValue: numValue,
        searchValue: label,
      },
    };
  }
}

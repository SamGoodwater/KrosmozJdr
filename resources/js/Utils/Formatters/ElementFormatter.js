/**
 * ElementFormatter — Formatter pour les éléments (sorts)
 *
 * @description
 * Formate les valeurs d'élément (Terre, Feu, Air, Eau, Neutre) en badges colorés.
 * Utilisé par : Spell uniquement
 */

import { BaseFormatter } from './BaseFormatter.js';

export class ElementFormatter extends BaseFormatter {
  static name = 'ElementFormatter';
  static fieldKeys = ['element', 'element_type'];

  /**
   * Options d'éléments
   * @type {Array<{value: number|string, label: string, color: string}>}
   */
  static options = [
    { value: 0, label: 'Neutre', color: 'neutral' },
    { value: 1, label: 'Terre', color: 'warning' },
    { value: 2, label: 'Feu', color: 'error' },
    { value: 3, label: 'Air', color: 'info' },
    { value: 4, label: 'Eau', color: 'primary' },
  ];

  /**
   * Formate une valeur d'élément en label
   *
   * @param {number|string|null} value - Valeur d'élément
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    return option?.label || `Élément ${numValue}`;
  }

  /**
   * Génère une cellule badge pour un tableau
   *
   * @param {number|string|null} value - Valeur d'élément
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
      return this.buildTextCell(`Élément ${numValue}`, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    return this.buildBadgeCell(option.label, option.color, {
      sortValue: numValue,
      filterValue: numValue,
    });
  }
}

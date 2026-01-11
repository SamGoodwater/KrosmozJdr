/**
 * CategoryFormatter — Formatter pour les catégories (sorts)
 *
 * @description
 * Formate les valeurs de catégorie de sort en badges ou texte.
 * Utilisé par : Spell uniquement
 */

import { BaseFormatter } from './BaseFormatter.js';

export class CategoryFormatter extends BaseFormatter {
  static name = 'CategoryFormatter';
  static fieldKeys = ['category', 'category_id', 'spell_category'];

  /**
   * Options de catégories (à adapter selon les besoins réels)
   * @type {Array<{value: number|string, label: string, color: string}>}
   */
  static options = [
    // Exemples - à adapter selon les catégories réelles
    { value: 1, label: 'Offensif', color: 'error' },
    { value: 2, label: 'Défensif', color: 'success' },
    { value: 3, label: 'Soin', color: 'info' },
    { value: 4, label: 'Buff', color: 'primary' },
    { value: 5, label: 'Debuff', color: 'warning' },
  ];

  /**
   * Formate une valeur de catégorie en label
   *
   * @param {number|string|null} value - Valeur de catégorie
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    return option?.label || `Catégorie ${numValue}`;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur de catégorie
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
    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    const label = option?.label || `Catégorie ${numValue}`;

    if (mode === 'badge' && option) {
      return this.buildBadgeCell(label, option.color, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    return this.buildTextCell(label, {
      sortValue: numValue,
      filterValue: numValue,
    });
  }
}

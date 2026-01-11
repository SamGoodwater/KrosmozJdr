/**
 * LevelFormatter — Formatter pour les valeurs de niveau
 *
 * @description
 * Formate les valeurs de niveau (entier positif) en texte et cellules de tableau.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class LevelFormatter extends BaseFormatter {
  static name = 'LevelFormatter';
  static fieldKeys = ['level', 'min_level', 'max_level'];

  /**
   * Formate une valeur de niveau en texte
   *
   * @param {number|string|null} value - Valeur de niveau
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    if (isNaN(numValue) || numValue < 0) {
      return null;
    }

    return `Niveau ${numValue}`;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur de niveau
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    if (isNaN(numValue) || numValue < 0) {
      return null;
    }

    const label = String(numValue);

    return this.buildTextCell(label, {
      sortValue: numValue,
      filterValue: numValue,
    });
  }
}

/**
 * DateFormatter — Formatter pour les dates
 *
 * @description
 * Formate les dates (ISO strings, timestamps) en texte français.
 * Utilisé par : Toutes les entités (created_at, updated_at, deleted_at)
 */

import { BaseFormatter } from './BaseFormatter.js';

export class DateFormatter extends BaseFormatter {
  static name = 'DateFormatter';
  static fieldKeys = ['created_at', 'updated_at', 'deleted_at', 'date'];

  /**
   * Formate une date en texte français
   *
   * @param {string|Date|number|null} value - Date à formater (ISO string, Date, timestamp)
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.format='auto'] - Format ('short', 'long', 'datetime', 'auto')
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl) - utilisé si format='auto'
   * @returns {string|null} Date formatée ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    let date;
    if (value instanceof Date) {
      date = value;
    } else if (typeof value === 'number') {
      date = new Date(value);
    } else {
      date = new Date(value);
    }

    if (isNaN(date.getTime())) {
      return null;
    }

    const { format = 'auto', size = 'md' } = options;
    const actualFormat = format === 'auto' ? this._getFormatForSize(size) : format;

    switch (actualFormat) {
      case 'short':
        return date.toLocaleDateString('fr-FR', {
          day: '2-digit',
          month: '2-digit',
          year: '2-digit',
        });
      case 'long':
        return date.toLocaleDateString('fr-FR', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
        });
      case 'datetime':
        return date.toLocaleString('fr-FR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        });
      default:
        return date.toLocaleDateString('fr-FR');
    }
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {string|Date|number|null} value - Date à formater
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.format='auto'] - Format ('short', 'long', 'datetime', 'auto')
   * @returns {Object|null} Objet Cell {type: 'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    let date;
    if (value instanceof Date) {
      date = value;
    } else if (typeof value === 'number') {
      date = new Date(value);
    } else {
      date = new Date(value);
    }

    if (isNaN(date.getTime())) {
      return null;
    }

    const label = this.format(value, options);
    const sortValue = date.getTime();

    return this.buildTextCell(label, {
      sortValue,
      filterValue: date.toISOString().split('T')[0], // YYYY-MM-DD pour le filtre
      searchValue: label,
    });
  }

  /**
   * Détermine le format selon la taille d'écran
   *
   * @param {string} size - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {string} Format ('short', 'long', 'datetime')
   * @private
   */
  static _getFormatForSize(size) {
    if (size === 'xs' || size === 'sm') {
      return 'short';
    }
    if (size === 'lg' || size === 'xl') {
      return 'datetime';
    }
    return 'short';
  }
}

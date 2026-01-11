/**
 * ImageFormatter — Formatter pour les URLs d'images
 *
 * @description
 * Formate les URLs d'images en cellules avec miniatures.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class ImageFormatter extends BaseFormatter {
  static name = 'ImageFormatter';
  static fieldKeys = ['image', 'image_url', 'thumbnail', 'avatar'];

  /**
   * Formate une URL d'image en texte
   *
   * @param {string|null} value - URL d'image
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} URL ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value).trim();
    if (!strValue || (!strValue.startsWith('http') && !strValue.startsWith('/'))) {
      return null;
    }

    return strValue;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {string|null} value - URL d'image
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.alt=''] - Texte alternatif
   * @param {number} [options.width] - Largeur de la miniature
   * @param {number} [options.height] - Hauteur de la miniature
   * @returns {Object|null} Objet Cell {type: 'image', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value).trim();
    if (!strValue || (!strValue.startsWith('http') && !strValue.startsWith('/'))) {
      return null;
    }

    const { alt = '', width, height } = options;

    return {
      type: 'image',
      value: strValue,
      params: {
        alt,
        width: width || (options.size === 'xs' || options.size === 'sm' ? 32 : 48),
        height: height || (options.size === 'xs' || options.size === 'sm' ? 32 : 48),
        sortValue: strValue,
        searchValue: alt || strValue,
        filterValue: strValue,
      },
    };
  }
}

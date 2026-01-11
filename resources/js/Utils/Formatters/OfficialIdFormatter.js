/**
 * OfficialIdFormatter — Formatter pour les IDs officiels
 *
 * @description
 * Formate les IDs officiels (identifiants du jeu) en texte.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class OfficialIdFormatter extends BaseFormatter {
  static name = 'OfficialIdFormatter';
  static fieldKeys = ['official_id', 'officialid', 'game_id'];

  /**
   * Formate une valeur d'ID officiel en texte
   *
   * @param {number|string|null} value - Valeur d'ID
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    return String(value);
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur d'ID
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);

    return this.buildTextCell(strValue, {
      sortValue: strValue,
      filterValue: strValue,
    });
  }
}

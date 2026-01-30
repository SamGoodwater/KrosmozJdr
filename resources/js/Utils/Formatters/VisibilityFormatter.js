/**
 * VisibilityFormatter — Formatter pour les valeurs de visibilité
 *
 * @description
 * Formate les niveaux d'accès (read_level / write_level) en labels et badges.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';
import { getRoleColor, getRoleLabel } from '@/Utils/Entity/SharedConstants.js';

export class VisibilityFormatter extends BaseFormatter {
  static name = 'VisibilityFormatter';
  static fieldKeys = ['read_level', 'write_level'];

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
    return getRoleLabel(value);
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
    const label = getRoleLabel(value);
    const daisyColor = getRoleColor(value);

    return this.buildBadgeCell(label, daisyColor, {
      sortValue: Number.isFinite(Number(value)) ? Number(value) : String(value),
      filterValue: Number.isFinite(Number(value)) ? Number(value) : String(value),
    });
  }
}

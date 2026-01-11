/**
 * DofusVersionFormatter — Formatter pour les versions Dofus
 *
 * @description
 * Formate les versions Dofus (1, 2, 2.0, etc.) en texte et badges.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class DofusVersionFormatter extends BaseFormatter {
  static name = 'DofusVersionFormatter';
  static fieldKeys = ['dofus_version', 'version', 'dofus_version_id'];

  /**
   * Options de version Dofus
   * @type {Array<{value: number|string, label: string, color: string}>}
   */
  static options = [
    { value: 1, label: 'Dofus 1', color: 'primary' },
    { value: 2, label: 'Dofus 2', color: 'success' },
    { value: '2.0', label: 'Dofus 2.0', color: 'success' },
  ];

  /**
   * Formate une valeur de version Dofus en texte
   *
   * @param {number|string|null} value - Valeur de version
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);
    const option = this.options.find((opt) => String(opt.value) === strValue);

    return option?.label || `Dofus ${strValue}`;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {number|string|null} value - Valeur de version
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
    const strValue = String(value);
    const option = this.options.find((opt) => String(opt.value) === strValue);

    const label = option?.label || `Dofus ${strValue}`;
    const color = option?.color || 'neutral';

    if (mode === 'badge') {
      return this.buildBadgeCell(label, color, {
        sortValue: strValue,
        filterValue: strValue,
      });
    }

    return this.buildTextCell(label, {
      sortValue: strValue,
      filterValue: strValue,
    });
  }
}

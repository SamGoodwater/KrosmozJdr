/**
 * UsableFormatter — Formatter pour le champ `state`
 *
 * @description
 * Formate les états d'entités (`raw`, `draft`, `playable`, `archived`) en badges ou texte.
 * NB: le nom historique "UsableFormatter" est conservé pour limiter les changements.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class UsableFormatter extends BaseFormatter {
  static name = 'UsableFormatter';
  static fieldKeys = ['state'];

  static options = Object.freeze([
    { value: 'raw', label: 'Brut', daisyColor: 'error' },
    { value: 'draft', label: 'Brouillon', daisyColor: 'warning' },
    { value: 'playable', label: 'Jouable', daisyColor: 'success' },
    { value: 'archived', label: 'Archivé', daisyColor: 'info' },
  ]);

  /**
   * Formate un état (`state`) en label
   *
   * @param {string|null} value - Valeur d'état
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);
    const option = this.options.find((opt) => opt.value === strValue);
    return option?.label || strValue;
  }

  /**
   * Génère une cellule pour un tableau
   *
   * @param {string|null} value - Valeur d'état
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @param {string} [options.mode='badge'] - Mode d'affichage ('badge' ou 'text')
   * @returns {Object|null} Objet Cell {type: 'badge'|'text', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const strValue = String(value);
    const option = this.options.find((opt) => opt.value === strValue);
    const label = option?.label || strValue;
    const daisyColor = option?.daisyColor || 'neutral';

    const { mode = 'badge' } = options;
    if (mode === 'text') {
      return this.buildTextCell(label, { sortValue: strValue, filterValue: strValue });
    }

    return this.buildBadgeCell(label, daisyColor, {
      sortValue: strValue,
      filterValue: strValue,
    });
  }
}

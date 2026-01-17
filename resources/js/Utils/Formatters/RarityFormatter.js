/**
 * RarityFormatter — Formatter pour les valeurs de rareté
 *
 * @description
 * Formate les valeurs de rareté (0-5) en labels, badges et cellules de tableau.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 *
 * Valeurs :
 * - 0: Commun (gray)
 * - 1: Peu commun (blue)
 * - 2: Rare (green)
 * - 3: Très rare (purple)
 * - 4: Légendaire (orange)
 * - 5: Unique (red)
 */

import { BaseFormatter } from './BaseFormatter.js';
import { getRarityOptions, getRarityConfig } from '@/Utils/Entity/SharedConstants.js';

export class RarityFormatter extends BaseFormatter {
  static name = 'RarityFormatter';
  static fieldKeys = ['rarity'];

  /**
   * Options de rareté
   * @type {Array<{value: number, label: string, color: string, icon: string, daisyColor: string}>}
   */
  static options = getRarityOptions();

  /**
   * Formate une valeur de rareté en label
   *
   * @param {number|string|null} value - Valeur de rareté (0-5)
   * @param {Object} [options={}] - Options de formatage
   * @returns {string|null} Label formaté ou null si valeur invalide
   */
  static format(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    const option = this.options.find((opt) => opt.value === numValue);

    return option?.label || `Rareté ${numValue}`;
  }

  /**
   * Génère une cellule badge pour un tableau
   *
   * @param {number|string|null} value - Valeur de rareté (0-5)
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'badge', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    // La valeur 0 est valide pour la rareté (Commun)
    if (value === null || value === undefined || (typeof value !== 'number' && typeof value !== 'string')) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    
    // Utiliser le nouveau système de gradient
    const config = getRarityConfig(numValue);

    if (!config) {
      // Fallback : cellule texte avec la valeur brute
      return this.buildTextCell(`Rareté ${numValue}`, {
        sortValue: numValue,
        filterValue: numValue,
      });
    }

    // Utiliser le gradient stable (token Tailwind) depuis le gradient
    return this.buildBadgeCell(config.label, config.color || config.daisyColor || 'neutral', {
      sortValue: numValue,
      filterValue: numValue,
      icon: config.icon,
    });
  }

  /**
   * Récupère l'option de rareté pour une valeur donnée
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {Object|null} Option de rareté ou null
   */
  static getOption(value) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    return this.options.find((opt) => opt.value === numValue) || null;
  }

  /**
   * Récupère la couleur DaisyUI pour une valeur de rareté
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {string} Couleur DaisyUI
   */
  static getColor(value) {
    const config = getRarityConfig(typeof value === 'string' ? parseInt(value, 10) : value);
    return config?.color || config?.daisyColor || 'neutral';
  }

  /**
   * Récupère l'icône pour une valeur de rareté
   *
   * @param {number|string} value - Valeur de rareté
   * @returns {string|null} Icône FontAwesome ou null
   */
  static getIcon(value) {
    const config = getRarityConfig(typeof value === 'string' ? parseInt(value, 10) : value);
    return config?.icon || null;
  }
}

/**
 * LevelFormatter — Formatter pour les valeurs de niveau
 *
 * @description
 * Formate les valeurs de niveau (1-30) en texte et cellules de tableau avec badges colorés.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 * 
 * Les niveaux sont affichés avec un dégradé de couleur :
 * - 1-5 : Gris (neutral)
 * - 6-10 : Bleu (info)
 * - 11-15 : Vert (success)
 * - 16-20 : Orange (warning)
 * - 21-25 : Rouge (error)
 * - 26-30 : Violet foncé (primary)
 * - >30 : Noir (fallback)
 */

import { BaseFormatter } from './BaseFormatter.js';
import { getLevelColor } from '@/Utils/Entity/SharedConstants.js';

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
   * Génère une cellule badge pour un tableau avec dégradé de couleur
   *
   * @param {number|string|null} value - Valeur de niveau (1-30)
   * @param {Object} [options={}] - Options de formatage
   * @param {string} [options.size='md'] - Taille d'écran (xs, sm, md, lg, xl)
   * @returns {Object|null} Objet Cell {type: 'badge', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const numValue = typeof value === 'string' ? parseInt(value, 10) : value;
    if (isNaN(numValue) || numValue < 0) {
      return null;
    }

    // Obtenir la couleur selon le niveau (0-30) ; au-delà => noir (voir SharedConstants)
    const color = getLevelColor(numValue);
    const label = String(numValue);

    return this.buildBadgeCell(label, color, {
      sortValue: numValue,
      filterValue: numValue,
      icon: 'fa-solid fa-level-up-alt',
    });
  }
}

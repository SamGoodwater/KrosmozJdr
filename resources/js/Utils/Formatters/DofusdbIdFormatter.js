/**
 * DofusdbIdFormatter — Formatter pour les IDs DofusDB
 *
 * @description
 * Formate les IDs DofusDB (identifiants externes) en texte avec lien optionnel.
 * Utilisé par : Resource, Item, Consumable, Spell, Monster, etc.
 */

import { BaseFormatter } from './BaseFormatter.js';

export class DofusdbIdFormatter extends BaseFormatter {
  static name = 'DofusdbIdFormatter';
  static fieldKeys = ['dofusdb_id', 'external_id', 'dofusdbid'];

  /**
   * Formate une valeur d'ID DofusDB en texte
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
   * @param {boolean} [options.asLink=false] - Afficher comme lien externe vers DofusDB
   * @returns {Object|null} Objet Cell {type: 'text'|'routeExternal', value, params} ou null si valeur invalide
   */
  static toCell(value, options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const { asLink = false } = options;
    const strValue = String(value);

    if (asLink) {
      // Générer un lien externe vers DofusDB (exemple d'URL)
      const href = `https://www.dofusdb.fr/fr/database/${strValue}`;
      return {
        type: 'routeExternal',
        value: strValue,
        params: {
          href,
          tooltip: `Voir sur DofusDB (ID: ${strValue})`,
          sortValue: strValue,
          searchValue: strValue,
          filterValue: strValue,
        },
      };
    }

    return this.buildTextCell(strValue, {
      sortValue: strValue,
      filterValue: strValue,
    });
  }
}

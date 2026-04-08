/**
 * ElementFormatter — Formatter pour les éléments (Spell, Capability)
 *
 * Masque 7 bits (0–127), aligné avec App\Support\ElementBitmask.
 */

import { BaseFormatter } from './BaseFormatter.js';
import {
  getElementLabel,
  normalizeElementStorageValue,
  ELEMENT_MASK_MAX,
} from '@/Utils/Entity/Elements.js';

export class ElementFormatter extends BaseFormatter {
  static name = 'ElementFormatter';
  static fieldKeys = ['element', 'element_type'];

  static isValid(value) {
    if (value === null || value === undefined) return false;
    const mask = normalizeElementStorageValue(value);
    return mask > 0 && mask <= ELEMENT_MASK_MAX;
  }

  /**
   * @param {number|string|null} value
   * @param {Object} [options={}]
   * @returns {string|null}
   */
  static format(value, _options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const mask = normalizeElementStorageValue(value);
    return getElementLabel(mask) ?? `Élément ${mask}`;
  }

  /**
   * @param {number|string|null} value
   * @param {Object} [options={}]
   * @returns {Object|null}
   */
  static toCell(value, _options = {}) {
    if (!this.isValid(value)) {
      return null;
    }

    const mask = normalizeElementStorageValue(value);
    const label = getElementLabel(mask);

    if (!label) {
      return this.buildTextCell(`Élément ${mask}`, {
        sortValue: mask,
        filterValue: mask,
      });
    }

    return {
      type: 'element',
      value: label,
      params: {
        element: mask,
        sortValue: mask,
        filterValue: mask,
        searchValue: label,
      },
    };
  }
}

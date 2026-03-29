/**
 * Service universel d'affichage des caractéristiques.
 *
 * @description
 * Centralise la résolution des styles (couleur, container) et le formatage des valeurs
 * pour les composants CharacteristicFormula, CharacteristicBoolean, CharacteristicBadges,
 * CharacteristicChip, CharacteristicEffectsGrid, PropertyDisplay.
 * Utilise useCharacteristicsStore (Inertia share) pour la résolution des définitions.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/AUDIT_SERVICE_AFFICHAGE_CARACTERISTIQUES.md
 */

import {
    hexToRgba,
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
} from "@/Utils/color/Color";
import {
    getByDbColumn,
    getByCharacteristicKey,
    getByDofusdbId,
    getByComputedKey,
} from "@/Composables/store/useCharacteristicsStore";

// Ré-exports pour usage direct
export { hexToRgba, getCharacteristicColorStyle, getCharacteristicContainerStyle };

/** Icône CAC (corps à corps) pour portée 1 ou 1-1. */
export const PO_CAC_ICON = 'icons/areas/samples/cac.svg';

/** Tooltip / label corps à corps. */
export const PO_CAC_TOOLTIP = 'Corps à corps';

/** @deprecated Alias pour PO_CAC_TOOLTIP */
export const PO_CAC_LABEL = PO_CAC_TOOLTIP;

/**
 * Indique si une valeur de portée (po) représente le corps à corps (1 ou 1-1).
 * @param {string|number|null} po
 * @returns {boolean}
 */
export function isPoCac(po) {
  if (po == null) return false;
  const s = String(po).trim().replace(/\s+/g, '');
  return s === '1' || s === '1-1';
}

/**
 * Valeur affichée selon la clé et les cas particuliers.
 *
 * @param {string} dbColumn - Clé db_column ou characteristic_key
 * @param {string|number|boolean} value - Valeur brute
 * @param {Object} [def] - Définition (value_available, etc.)
 * @returns {string} - Valeur formatée pour affichage
 */
export function getDisplayValue(dbColumn, value, def = {}) {
  if (value === null || value === undefined || value === '') return '—';

  // Bonus critique 0–3 → seuil (0=Nat 20, 1=Dès 19, 2=Dès 18, 3=Dès 17)
  if (dbColumn === 'critical_hit') {
    const v = parseInt(value, 10);
    return v === 0 ? 'Nat 20' : `Dès ${20 - v}`;
  }

  // Portée 1 ou 1-1 → CAC (corps à corps) : pas de nombre affiché, icône CAC, tooltip "corps à corps"
  const strVal = String(value).trim();
  if ((dbColumn === 'po' || dbColumn === 'po_max' || dbColumn === 'po_min' || dbColumn === 'range_spell') && (strVal === '1' || strVal === '1-1')) {
    return 'CAC';
  }

  // Booléen
  if (typeof value === 'boolean') {
    return value ? 'Oui' : 'Non';
  }

  // value_available : résolution label si déf fourni
  const available = def?.value_available;
  if (Array.isArray(available)) {
    const entry = available.find(
      (a) => (typeof a === 'object' && a?.value === value) || a === value
    );
    if (typeof entry === 'object' && entry?.label != null) return entry.label;
  }

  return String(value);
}

/** Groupes par défaut pour la résolution (ordre de priorité). */
const DEFAULT_SOURCE_GROUPS = {
    creature: ["creature"],
    spell: ["spell", "capability"],
    item: ["item", "resource"],
    resource: ["resource", "item"],
    consumable: ["consumable", "resource"],
    panoply: ["panoply", "item"],
};

/**
 * Résout une définition depuis le store (key, db_column ou dofusdb_id).
 *
 * @param {string|number} keyOrId - characteristic_key, db_column, ou dofusdb_characteristic_id
 * @param {string|number|boolean} [value] - Valeur (pour variantes : iconFalse si booléen, label depuis value_available)
 * @param {Object} [options] - { sourceGroups: string[] }
 * @returns {Object|null} - { key, db_column, name, short_name, icon, icon_false?, color, unit, type, helper, descriptions, value_available } ou null
 */
export function resolveDef(keyOrId, value, options = {}) {
    const sourceGroups = options?.sourceGroups ?? ["creature", "item", "resource", "spell", "capability"];
    const keyStr = keyOrId != null ? String(keyOrId).trim() : "";
    if (!keyStr) return null;

    for (const group of sourceGroups) {
        let def =
            getByDbColumn(group, keyStr) ??
            getByDbColumn(group, keyStr.replace(/_object$/, "")) ??
            getByCharacteristicKey(group, keyStr) ??
            getByCharacteristicKey(group, keyStr.replace(/_object$/, "") + "_object") ??
            (group in { item: 1, consumable: 1, resource: 1, panoply: 1 }
                ? getByDofusdbId(group, /^\d+$/.test(keyStr) ? Number(keyStr) : keyStr)
                : null) ??
            getByComputedKey(group, keyStr);
        if (def) {
            def = { ...def };
            if (typeof value === "boolean" && def.icon_false != null) {
                def._resolvedIcon = value ? def.icon : (def.icon_false ?? def.icon);
            } else if (Array.isArray(def.value_available)) {
                const entry = def.value_available.find(
                    (a) => (typeof a === "object" && a?.value === value) || a === value
                );
                if (typeof entry === "object" && entry != null) {
                    if (entry.icon != null) def._resolvedIcon = entry.icon;
                    if (entry.color != null) def._resolvedColor = entry.color;
                    if (entry.label != null) def._resolvedLabel = entry.label;
                }
            }
            return def;
        }
    }
    return null;
}

/**
 * Résout la définition complète + valeur formatée + styles.
 *
 * @param {string|number} keyOrId
 * @param {string|number|boolean} value
 * @param {Object} [options]
 * @returns {{ def: Object|null, displayValue: string, colorStyle: Object|undefined, containerStyle: Object }}
 */
export function resolveDisplay(keyOrId, value, options = {}) {
    const def = resolveDef(keyOrId, value, options);
    const displayValue = getDisplayValue(
        def?.db_column ?? keyOrId,
        value,
        def ?? {}
    );
    const color = def?._resolvedColor ?? def?.color;
    return {
        def,
        displayValue,
        colorStyle: getCharacteristicColorStyle(color),
        containerStyle: getCharacteristicContainerStyle(
            color && String(color).startsWith("#") ? color : null
        ),
    };
}

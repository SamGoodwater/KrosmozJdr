/**
 * Descriptor Validation — Validation des descriptors d'entités
 *
 * @description
 * Fonctions de validation pour vérifier la cohérence et la validité des descriptors.
 * Utile pour le debug et le développement.
 */

import { FORM_TYPES, FIELD_FORMATS } from './Constants.js';

/**
 * Valide un descriptor de champ.
 *
 * @param {Object} descriptor - Descriptor à valider
 * @param {string} fieldKey - Clé du champ (pour les messages d'erreur)
 * @returns {{valid: boolean, errors: Array<string>}} Résultat de la validation
 */
export function validateFieldDescriptor(descriptor, fieldKey) {
  const errors = [];

  // Validation des champs obligatoires
  if (!descriptor.key) {
    errors.push(`Le champ 'key' est obligatoire pour ${fieldKey}`);
  }
  if (!descriptor.label) {
    errors.push(`Le champ 'label' est obligatoire pour ${fieldKey}`);
  }

  // Validation du format (si présent dans l'ancienne structure)
  if (descriptor.format && !FIELD_FORMATS[descriptor.format]) {
    errors.push(`Format invalide pour ${fieldKey}: ${descriptor.format}`);
  }

  // Validation de edit.form si présent
  if (descriptor.edit?.form) {
    const form = descriptor.edit.form;
    if (!FORM_TYPES.includes(form.type)) {
      errors.push(`Type de formulaire invalide pour ${fieldKey}: ${form.type}`);
    }

    // Validation de bulk si présent
    // ⚠️ NOTE: bulk.build est déprécié, les transformations sont maintenant dans les mappers
    // On ne valide plus la présence de bulk.build
  }

  // Validation de table si présent (nouvelle structure)
  if (descriptor.table) {
    const table = descriptor.table;
    if (table.format) {
      // Vérifier que format est un objet avec des clés valides (xs, sm, md, lg, xl)
      const validSizes = ['xs', 'sm', 'md', 'lg', 'xl'];
      for (const size of Object.keys(table.format)) {
        if (!validSizes.includes(size)) {
          errors.push(`Taille d'écran invalide dans table.format pour ${fieldKey}: ${size}`);
        }
      }
    }
  }

  // Validation de quickedit si présent (nouvelle structure)
  if (descriptor.quickedit) {
    const quickedit = descriptor.quickedit;
    if (quickedit.enabled && !quickedit.type) {
      errors.push(`Type obligatoire pour quickedit.enabled=true dans ${fieldKey}`);
    }
    if (quickedit.enabled && !FORM_TYPES.includes(quickedit.type)) {
      errors.push(`Type de formulaire invalide pour quickedit dans ${fieldKey}: ${quickedit.type}`);
    }
  }

  // Validation de visibleIf et editableIf
  if (descriptor.visibleIf && typeof descriptor.visibleIf !== "function") {
    errors.push(`visibleIf doit être une fonction pour ${fieldKey}`);
  }
  if (descriptor.editableIf && typeof descriptor.editableIf !== "function") {
    errors.push(`editableIf doit être une fonction pour ${fieldKey}`);
  }

  return {
    valid: errors.length === 0,
    errors,
  };
}

/**
 * Valide l'ensemble des descriptors.
 *
 * @param {Record<string, Object>} descriptors - Descriptors à valider
 * @returns {{valid: boolean, errors: Array<{field: string, error: string}>}} Résultat de la validation
 */
export function validateDescriptors(descriptors) {
  const allErrors = [];

  for (const [key, descriptor] of Object.entries(descriptors || {})) {
    // Ignorer les clés qui commencent par _ (configurations globales)
    if (key.startsWith('_')) {
      continue;
    }

    const result = validateFieldDescriptor(descriptor, key);
    if (!result.valid) {
      allErrors.push(...result.errors.map((error) => ({ field: key, error })));
    }
  }

  return {
    valid: allErrors.length === 0,
    errors: allErrors,
  };
}

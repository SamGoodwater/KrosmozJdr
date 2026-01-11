/**
 * AttributeBulkConfig — Configuration de l'édition en masse (bulk) pour Attribute
 *
 * @description
 * Configuration de l'édition en masse pour les attributs.
 * Doit rester aligné avec le backend (bulk controller).
 * Utilise les descriptors simplifiés et ATTRIBUTE_QUICK_EDIT_FIELDS.
 */

import { BulkConfig } from "../entity/BulkConfig.js";
import { getAttributeFieldDescriptors, ATTRIBUTE_QUICK_EDIT_FIELDS } from "./attribute-descriptors.js";

/**
 * Crée la configuration bulk pour Attribute
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {BulkConfig} Configuration bulk
 */
export function createAttributeBulkConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir les configurations bulk
  const descriptors = getAttributeFieldDescriptors(ctx);

  const bulkConfig = new BulkConfig({
    entityType: "attribute",
  });

  // Champs bulk-editables (doit être aligné avec le backend)
  // Utiliser les configurations des descriptors
  for (const [key, descriptor] of Object.entries(descriptors)) {
    if (descriptor.edit?.form?.bulk?.enabled) {
      const bulkConfigField = descriptor.edit.form.bulk;
      bulkConfig.addField(key, {
        enabled: bulkConfigField.enabled,
        nullable: bulkConfigField.nullable,
        build: bulkConfigField.build || null,
        label: descriptor.label,
      });
    }
  }

  // Champs affichés dans quickEdit (doit être aligné avec le backend)
  bulkConfig.withQuickEditFields(ATTRIBUTE_QUICK_EDIT_FIELDS);

  return bulkConfig.build();
}

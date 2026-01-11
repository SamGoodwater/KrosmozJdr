/**
 * ResourceTypeBulkConfig — Configuration de l'édition en masse (bulk) pour ResourceType
 *
 * @description
 * Configuration de l'édition en masse pour les types de ressources.
 * Doit rester aligné avec le backend (bulk controller).
 * Utilise les descriptors simplifiés et RESOURCE_TYPE_QUICK_EDIT_FIELDS.
 */

import { BulkConfig } from "../entity/BulkConfig.js";
import { getResourceTypeFieldDescriptors, RESOURCE_TYPE_QUICK_EDIT_FIELDS } from "./resource-type-descriptors.js";

/**
 * Crée la configuration bulk pour ResourceType
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {BulkConfig} Configuration bulk
 */
export function createResourceTypeBulkConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir les configurations bulk
  const descriptors = getResourceTypeFieldDescriptors(ctx);

  const bulkConfig = new BulkConfig({
    entityType: "resource-type",
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
  bulkConfig.withQuickEditFields(RESOURCE_TYPE_QUICK_EDIT_FIELDS);

  return bulkConfig.build();
}

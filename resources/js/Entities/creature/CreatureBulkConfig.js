/**
 * CreatureBulkConfig — Configuration de l'édition en masse (bulk) pour Creature
 *
 * @description
 * Configuration de l'édition en masse pour les créatures.
 * Doit rester aligné avec le backend (bulk controller).
 * Utilise les descriptors simplifiés et CREATURE_QUICK_EDIT_FIELDS.
 */

import { BulkConfig } from "../entity/BulkConfig.js";
import { getCreatureFieldDescriptors, CREATURE_QUICK_EDIT_FIELDS } from "./creature-descriptors.js";

/**
 * Crée la configuration bulk pour Creature
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {BulkConfig} Configuration bulk
 */
export function createCreatureBulkConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir les configurations bulk
  const descriptors = getCreatureFieldDescriptors(ctx);

  const bulkConfig = new BulkConfig({
    entityType: "creature",
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
  bulkConfig.withQuickEditFields(CREATURE_QUICK_EDIT_FIELDS);

  return bulkConfig.build();
}

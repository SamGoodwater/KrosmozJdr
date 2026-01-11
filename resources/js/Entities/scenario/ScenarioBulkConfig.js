/**
 * ScenarioBulkConfig — Configuration de l'édition en masse (bulk) pour Scenario
 *
 * @description
 * Configuration de l'édition en masse pour les scénarios.
 * Doit rester aligné avec le backend (bulk controller).
 * Utilise les descriptors simplifiés et SCENARIO_QUICK_EDIT_FIELDS.
 */

import { BulkConfig } from "../entity/BulkConfig.js";
import { getScenarioFieldDescriptors, SCENARIO_QUICK_EDIT_FIELDS } from "./scenario-descriptors.js";

/**
 * Crée la configuration bulk pour Scenario
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {BulkConfig} Configuration bulk
 */
export function createScenarioBulkConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir les configurations bulk
  const descriptors = getScenarioFieldDescriptors(ctx);

  const bulkConfig = new BulkConfig({
    entityType: "scenario",
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
  bulkConfig.withQuickEditFields(SCENARIO_QUICK_EDIT_FIELDS);

  return bulkConfig.build();
}

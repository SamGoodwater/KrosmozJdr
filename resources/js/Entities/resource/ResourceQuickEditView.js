/**
 * ResourceQuickEditView — Configuration de la vue QuickEdit pour Resource
 *
 * @description
 * Vue spécialisée pour l'édition rapide en masse des ressources.
 * Peut être affichée en panneau latéral ou en modal.
 */

import { QuickEditViewConfig } from "../entity/ViewConfig.js";

/**
 * Crée la configuration de la vue QuickEdit pour Resource
 *
 * @param {Object} [options] - Options de configuration
 * @param {string} [options.layoutType="panel"] - Type de layout (panel, modal)
 * @param {Object} [options.layoutOptions] - Options de layout
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {QuickEditViewConfig} Configuration de la vue QuickEdit
 */
export function createResourceQuickEditView(options = {}, ctx = {}) {
  const { layoutType = "panel", layoutOptions = {} } = options;

  const quickEditView = new QuickEditViewConfig({
    name: "quickEdit",
    label: "Édition rapide",
  })
    .withFields([
      "resource_type_id",
      "rarity",
      "level",
      "usable",
      "auto_update",
      "is_visible",
      "price",
      "weight",
      "dofus_version",
      "description",
      "image",
      "dofusdb_id",
    ])
    .withOrder([
      "resource_type_id",
      "rarity",
      "level",
      "usable",
      "auto_update",
      "is_visible",
      "price",
      "weight",
      "dofus_version",
      "description",
      "image",
      "dofusdb_id",
    ]);

  // Configurer le layout selon le type
  if (layoutType === "modal") {
    quickEditView.withLayoutType("modal", {
      size: layoutOptions.size || "lg",
    });
  } else {
    quickEditView.withLayoutType("panel", {
      position: layoutOptions.position || "right",
      width: layoutOptions.width || "md",
    });
  }

  return quickEditView.build(ctx);
}

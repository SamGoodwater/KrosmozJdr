/**
 * ResourceViewLarge — Configuration de la vue large (étendue) pour Resource
 *
 * @description
 * Vue étendue affichant toutes les informations disponibles d'une ressource.
 */

import { ViewConfig } from "../entity/ViewConfig.js";

/**
 * Crée la configuration de la vue large pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {ViewConfig} Configuration de la vue large
 */
export function createResourceViewLarge(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  const fields = [
    "rarity",
    "resource_type",
    "level",
    "usable",
    "price",
    "weight",
    "dofus_version",
    "is_visible",
    "auto_update",
    "dofusdb_id",
  ];

  // Ajouter les champs conditionnels
  if (canCreateAny) {
    fields.push("created_by", "created_at", "updated_at");
  }

  return new ViewConfig({
    name: "large",
    label: "Vue étendue",
  })
    .withFields(fields)
    .withOrder(fields)
    .withActions({
      available: ["view", "edit", "quick-edit", "delete", "copy-link", "download-pdf", "refresh"],
      permission: "view",
      display: "icon-text",
    })
    .withLayout({
      columns: 3,
      spacing: "normal",
    })
    .build(ctx);
}

/**
 * ResourceViewCompact — Configuration de la vue compacte pour Resource
 *
 * @description
 * Vue compacte affichant les informations essentielles d'une ressource.
 */

import { ViewConfig } from "../entity/ViewConfig.js";

/**
 * Crée la configuration de la vue compacte pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {ViewConfig} Configuration de la vue compacte
 */
export function createResourceViewCompact(ctx = {}) {
  return new ViewConfig({
    name: "compact",
    label: "Vue compacte",
  })
    .withFields(["rarity", "resource_type", "level", "usable", "price", "weight", "dofus_version", "is_visible", "auto_update", "dofusdb_id"])
    .withOrder(["rarity", "resource_type", "level", "usable", "price", "weight", "dofus_version", "is_visible", "auto_update", "dofusdb_id"])
    .withActions({
      available: ["view", "edit", "quick-edit"],
      permission: "view",
      display: "icon-only",
    })
    .withLayout({
      columns: 2,
      spacing: "compact",
    })
    .build(ctx);
}

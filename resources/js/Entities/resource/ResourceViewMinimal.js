/**
 * ResourceViewMinimal — Configuration de la vue minimale pour Resource
 *
 * @description
 * Vue minimale affichant uniquement les informations les plus importantes.
 */

import { ViewConfig } from "../entity/ViewConfig.js";

/**
 * Crée la configuration de la vue minimale pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {ViewConfig} Configuration de la vue minimale
 */
export function createResourceViewMinimal(ctx = {}) {
  return new ViewConfig({
    name: "minimal",
    label: "Vue minimale",
  })
    .withFields(["rarity", "level", "usable"])
    .withOrder(["rarity", "level", "usable"])
    .withActions({
      available: ["view", "quick-edit"],
      permission: "view",
      display: "icon-only",
    })
    .build(ctx);
}

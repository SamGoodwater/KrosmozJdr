/**
 * ResourceDescriptor — Descriptor principal pour l'entité Resource
 *
 * @description
 * Descriptor principal qui assemble toutes les configurations :
 * - Tableau
 * - Vues (compact, minimal, large, quickEdit)
 * - Formulaires
 * - Bulk
 *
 * @example
 * import { ResourceDescriptor } from "@/Entities/resource/ResourceDescriptor";
 * const descriptor = ResourceDescriptor;
 * const tableConfig = descriptor.getTableConfig(ctx);
 * const viewConfig = descriptor.getViewConfig("compact", ctx);
 */

import { EntityDescriptor } from "../entity/EntityDescriptor.js";
import { createResourceTableConfig } from "./ResourceTableConfig.js";
import { createResourceViewCompact } from "./ResourceViewCompact.js";
import { createResourceViewMinimal } from "./ResourceViewMinimal.js";
import { createResourceViewLarge } from "./ResourceViewLarge.js";
import { createResourceQuickEditView } from "./ResourceQuickEditView.js";
import { createResourceFormConfig } from "./ResourceFormConfig.js";
import { createResourceBulkConfig } from "./ResourceBulkConfig.js";
import { getResourceFieldDescriptors } from "./resource-descriptors.js";

/**
 * Classe ResourceDescriptor
 */
class ResourceDescriptor extends EntityDescriptor {
  constructor() {
    super("resource");
  }

  /**
   * Retourne les descriptors de tous les champs de l'entité Resource.
   *
   * @param {Object} ctx - Contexte
   * @returns {Record<string, Object>} Descriptors de champs
   */
  getFieldDescriptors(ctx = {}) {
    return getResourceFieldDescriptors(ctx);
  }

  /**
   * Retourne la configuration du tableau.
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration du tableau
   */
  getTableConfig(ctx = {}) {
    const tableConfig = createResourceTableConfig(ctx);
    return tableConfig.build(ctx);
  }

  /**
   * Retourne la configuration d'une vue.
   *
   * @param {string} viewName - Nom de la vue (compact, minimal, large, quickEdit)
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration de la vue
   */
  getViewConfig(viewName, ctx = {}) {
    switch (viewName) {
      case "compact":
        return createResourceViewCompact(ctx);
      case "minimal":
        return createResourceViewMinimal(ctx);
      case "large":
      case "extended":
        return createResourceViewLarge(ctx);
      case "quickEdit":
        return createResourceQuickEditView({}, ctx);
      default:
        throw new Error(`Vue inconnue pour Resource: ${viewName}`);
    }
  }

  /**
   * Retourne la configuration des formulaires.
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration des formulaires
   */
  getFormConfig(ctx = {}) {
    const formConfig = createResourceFormConfig(ctx);
    return formConfig.build(ctx);
  }

  /**
   * Retourne la configuration de l'édition en masse (bulk).
   *
   * @param {Object} ctx - Contexte
   * @returns {Object} Configuration bulk
   */
  getBulkConfig(ctx = {}) {
    const bulkConfig = createResourceBulkConfig(ctx);
    return bulkConfig.build();
  }
}

// Export d'une instance unique (singleton)
export default new ResourceDescriptor();

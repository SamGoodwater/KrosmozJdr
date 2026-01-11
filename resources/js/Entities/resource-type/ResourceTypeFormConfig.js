/**
 * ResourceTypeFormConfig — Configuration des formulaires pour l'entité ResourceType
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les types de ressources.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getResourceTypeFieldDescriptors } from "./resource-type-descriptors.js";

/**
 * Crée la configuration des formulaires pour ResourceType
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createResourceTypeFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getResourceTypeFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "resource-type",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Informations générales", label: "Informations générales", order: 1 })
    .addGroup({ name: "Statut", label: "Statut", order: 2 })
    .addGroup({ name: "Métadonnées", label: "Métadonnées", order: 3 });

  // Champs de formulaire
  formConfig
    .addField(
      new FormFieldConfig({
        key: "name",
        type: "text",
        label: descriptors.name?.edit?.form?.label || descriptors.name?.label || "Nom",
      })
        .withGroup(descriptors.name?.edit?.form?.group || "Informations générales")
        .withRequired(descriptors.name?.edit?.form?.required ?? true)
        .withShowInCompact(true)
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "dofusdb_type_id",
        type: "number",
        label: descriptors.dofusdb_type_id?.edit?.form?.label || descriptors.dofusdb_type_id?.label || "DofusDB typeId",
      })
        .withGroup(descriptors.dofusdb_type_id?.edit?.form?.group || "Métadonnées")
        .withPermission("updateAny")
        .withShowInCompact(true)
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "decision",
        type: "select",
        label: descriptors.decision?.edit?.form?.label || descriptors.decision?.label || "Statut",
      })
        .withGroup(descriptors.decision?.edit?.form?.group || "Statut")
        .withOptions(descriptors.decision?.edit?.form?.options || [])
        .withDefaultValue(descriptors.decision?.edit?.form?.defaultValue || "pending")
        .withShowInCompact(true)
        .withBulk(descriptors.decision?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v,
        })
    )
    .addField(
      new FormFieldConfig({
        key: "usable",
        type: "checkbox",
        label: descriptors.usable?.edit?.form?.label || descriptors.usable?.label || "Utilisable",
      })
        .withGroup(descriptors.usable?.edit?.form?.group || "Statut")
        .withDefaultValue(descriptors.usable?.edit?.form?.defaultValue ?? true)
        .withShowInCompact(true)
        .withBulk(descriptors.usable?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v === "1" || v === true,
        })
    )
    .addField(
      new FormFieldConfig({
        key: "is_visible",
        type: "select",
        label: descriptors.is_visible?.edit?.form?.label || descriptors.is_visible?.label || "Visibilité",
      })
        .withGroup(descriptors.is_visible?.edit?.form?.group || "Statut")
        .withOptions(descriptors.is_visible?.edit?.form?.options || [])
        .withDefaultValue(descriptors.is_visible?.edit?.form?.defaultValue || "guest")
        .withShowInCompact(true)
        .withBulk(descriptors.is_visible?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v,
        })
    );

  return formConfig.build(ctx);
}

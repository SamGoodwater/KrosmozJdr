/**
 * SpecializationFormConfig — Configuration des formulaires pour l'entité Specialization
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les spécialisations.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getSpecializationFieldDescriptors } from "./specialization-descriptors.js";

/**
 * Crée la configuration des formulaires pour Specialization
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createSpecializationFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getSpecializationFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "specialization",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Contenu", label: "Contenu", order: 1 })
    .addGroup({ name: "Statut", label: "Statut", order: 2 })
    .addGroup({ name: "Médias", label: "Médias", order: 3 });

  // Champs de formulaire
  formConfig
    .addField(
      new FormFieldConfig({
        key: "name",
        type: "text",
        label: descriptors.name?.edit?.form?.label || descriptors.name?.label || "Nom",
      })
        .withRequired(descriptors.name?.edit?.form?.required ?? true)
        .withShowInCompact(true)
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "description",
        type: "textarea",
        label: descriptors.description?.edit?.form?.label || descriptors.description?.label || "Description",
      })
        .withGroup(descriptors.description?.edit?.form?.group || "Contenu")
        .withShowInCompact(false)
        .withBulk(descriptors.description?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "usable",
        type: "checkbox",
        label: descriptors.usable?.edit?.form?.label || descriptors.usable?.label || "Utilisable",
      })
        .withGroup(descriptors.usable?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withDefaultValue(descriptors.usable?.edit?.form?.defaultValue || false)
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
        label: descriptors.is_visible?.edit?.form?.label || descriptors.is_visible?.label || "Visible",
      })
        .withGroup(descriptors.is_visible?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withOptions(descriptors.is_visible?.edit?.form?.options || [
          { value: "guest", label: "Invité" },
          { value: "user", label: "Utilisateur" },
          { value: "player", label: "Joueur" },
          { value: "game_master", label: "Maître du jeu" },
          { value: "admin", label: "Administrateur" },
        ])
        .withDefaultValue(descriptors.is_visible?.edit?.form?.defaultValue || "guest")
        .withBulk(descriptors.is_visible?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "image",
        type: "file",
        label: descriptors.image?.edit?.form?.label || descriptors.image?.label || "Image",
      })
        .withGroup(descriptors.image?.edit?.form?.group || "Médias")
        .withShowInCompact(false)
        .withoutBulk()
    );

  return formConfig.build();
}

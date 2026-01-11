/**
 * ScenarioFormConfig — Configuration des formulaires pour l'entité Scenario
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les scénarios.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getScenarioFieldDescriptors } from "./scenario-descriptors.js";

/**
 * Crée la configuration des formulaires pour Scenario
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createScenarioFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getScenarioFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "scenario",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Statut", label: "Statut", order: 1 })
    .addGroup({ name: "Métadonnées", label: "Métadonnées", order: 2 })
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
        key: "slug",
        type: "text",
        label: descriptors.slug?.edit?.form?.label || descriptors.slug?.label || "Slug",
      })
        .withGroup(descriptors.slug?.edit?.form?.group || "Métadonnées")
        .withRequired(descriptors.slug?.edit?.form?.required ?? true)
        .withShowInCompact(false)
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "description",
        type: "textarea",
        label: descriptors.description?.edit?.form?.label || descriptors.description?.label || "Description",
      })
        .withShowInCompact(false)
        .withBulk(descriptors.description?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "keyword",
        type: "text",
        label: descriptors.keyword?.edit?.form?.label || descriptors.keyword?.label || "Mot-clé",
      })
        .withGroup(descriptors.keyword?.edit?.form?.group || "Métadonnées")
        .withPlaceholder(descriptors.keyword?.edit?.form?.placeholder || "Ex: aventure, fantasy")
        .withShowInCompact(false)
        .withBulk(descriptors.keyword?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "state",
        type: "select",
        label: descriptors.state?.edit?.form?.label || descriptors.state?.label || "État",
      })
        .withGroup(descriptors.state?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withOptions(descriptors.state?.edit?.form?.options || [
          { value: 0, label: "En cours" },
          { value: 1, label: "Terminée" },
          { value: 2, label: "En pause" },
          { value: 3, label: "Annulée" },
        ])
        .withDefaultValue(descriptors.state?.edit?.form?.defaultValue || 0)
        .withBulk(descriptors.state?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "is_public",
        type: "checkbox",
        label: descriptors.is_public?.edit?.form?.label || descriptors.is_public?.label || "Public",
      })
        .withGroup(descriptors.is_public?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withDefaultValue(descriptors.is_public?.edit?.form?.defaultValue || false)
        .withBulk(descriptors.is_public?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v === "1" || v === true,
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

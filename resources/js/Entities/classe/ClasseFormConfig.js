/**
 * ClasseFormConfig — Configuration des formulaires pour l'entité Classe
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les classes.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getClasseFieldDescriptors } from "./classe-descriptors.js";

/**
 * Crée la configuration des formulaires pour Classe
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createClasseFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getClasseFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "classe",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Caractéristiques", label: "Caractéristiques", order: 1 })
    .addGroup({ name: "Description", label: "Description", order: 2 })
    .addGroup({ name: "Statut", label: "Statut", order: 3 })
    .addGroup({ name: "Métadonnées", label: "Métadonnées", order: 4 })
    .addGroup({ name: "Médias", label: "Médias", order: 5 });

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
        .withGroup(descriptors.description?.edit?.form?.group || "Description")
        .withShowInCompact(false)
        .withBulk(descriptors.description?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "life",
        type: "text",
        label: descriptors.life?.edit?.form?.label || descriptors.life?.label || "Vie",
      })
        .withGroup(descriptors.life?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.life?.edit?.form?.placeholder || "Ex: 30")
        .withShowInCompact(true)
        .withBulk(descriptors.life?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "life_dice",
        type: "text",
        label: descriptors.life_dice?.edit?.form?.label || descriptors.life_dice?.label || "Dé de vie",
      })
        .withGroup(descriptors.life_dice?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.life_dice?.edit?.form?.placeholder || "Ex: d8")
        .withShowInCompact(false)
        .withBulk(descriptors.life_dice?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "specificity",
        type: "textarea",
        label: descriptors.specificity?.edit?.form?.label || descriptors.specificity?.label || "Spécificité",
      })
        .withGroup(descriptors.specificity?.edit?.form?.group || "Description")
        .withShowInCompact(false)
        .withBulk(descriptors.specificity?.edit?.form?.bulk || {
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
        key: "auto_update",
        type: "checkbox",
        label: descriptors.auto_update?.edit?.form?.label || descriptors.auto_update?.label || "Auto-update",
      })
        .withGroup(descriptors.auto_update?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withDefaultValue(descriptors.auto_update?.edit?.form?.defaultValue || false)
        .withBulk(descriptors.auto_update?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v === "1" || v === true,
        })
    )
    .addField(
      new FormFieldConfig({
        key: "dofus_version",
        type: "text",
        label: descriptors.dofus_version?.edit?.form?.label || descriptors.dofus_version?.label || "Version Dofus",
      })
        .withGroup(descriptors.dofus_version?.edit?.form?.group || "Métadonnées")
        .withShowInCompact(false)
        .withBulk(descriptors.dofus_version?.edit?.form?.bulk || {
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
    )
    .addField(
      new FormFieldConfig({
        key: "icon",
        type: "file",
        label: descriptors.icon?.edit?.form?.label || descriptors.icon?.label || "Icône",
      })
        .withGroup(descriptors.icon?.edit?.form?.group || "Médias")
        .withShowInCompact(false)
        .withoutBulk()
    );

  return formConfig.build();
}

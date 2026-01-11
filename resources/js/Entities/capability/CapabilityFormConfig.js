/**
 * CapabilityFormConfig — Configuration des formulaires pour l'entité Capability
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les capacités.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getCapabilityFieldDescriptors } from "./capability-descriptors.js";

/**
 * Crée la configuration des formulaires pour Capability
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createCapabilityFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getCapabilityFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "capability",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Métier", label: "Métier", order: 1 })
    .addGroup({ name: "Contenu", label: "Contenu", order: 2 })
    .addGroup({ name: "Statut", label: "Statut", order: 3 })
    .addGroup({ name: "Médias", label: "Médias", order: 4 });

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
        key: "level",
        type: "text",
        label: descriptors.level?.edit?.form?.label || descriptors.level?.label || "Niveau",
      })
        .withGroup(descriptors.level?.edit?.form?.group || "Métier")
        .withPlaceholder(descriptors.level?.edit?.form?.placeholder || "Ex: 1")
        .withShowInCompact(true)
        .withBulk(descriptors.level?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "pa",
        type: "text",
        label: descriptors.pa?.edit?.form?.label || descriptors.pa?.label || "PA",
      })
        .withGroup(descriptors.pa?.edit?.form?.group || "Métier")
        .withPlaceholder(descriptors.pa?.edit?.form?.placeholder || "Ex: 3")
        .withShowInCompact(true)
        .withBulk(descriptors.pa?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "po",
        type: "text",
        label: descriptors.po?.edit?.form?.label || descriptors.po?.label || "PO",
      })
        .withGroup(descriptors.po?.edit?.form?.group || "Métier")
        .withPlaceholder(descriptors.po?.edit?.form?.placeholder || "Ex: 0")
        .withShowInCompact(false)
        .withBulk(descriptors.po?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "element",
        type: "select",
        label: descriptors.element?.edit?.form?.label || descriptors.element?.label || "Élément",
      })
        .withGroup(descriptors.element?.edit?.form?.group || "Métier")
        .withShowInCompact(true)
        .withOptions(descriptors.element?.edit?.form?.options || [
          { value: "neutral", label: "Neutre" },
          { value: "fire", label: "Feu" },
          { value: "water", label: "Eau" },
          { value: "earth", label: "Terre" },
          { value: "air", label: "Air" },
        ])
        .withDefaultValue(descriptors.element?.edit?.form?.defaultValue || "neutral")
        .withBulk(descriptors.element?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
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
        key: "effect",
        type: "textarea",
        label: descriptors.effect?.edit?.form?.label || descriptors.effect?.label || "Effet",
      })
        .withGroup(descriptors.effect?.edit?.form?.group || "Contenu")
        .withShowInCompact(false)
        .withBulk(descriptors.effect?.edit?.form?.bulk || {
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

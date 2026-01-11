/**
 * CreatureFormConfig — Configuration des formulaires pour l'entité Creature
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les créatures.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getCreatureFieldDescriptors } from "./creature-descriptors.js";

/**
 * Crée la configuration des formulaires pour Creature
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createCreatureFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getCreatureFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "creature",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Caractéristiques", label: "Caractéristiques", order: 1 })
    .addGroup({ name: "Statut", label: "Statut", order: 2 });

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
        .withShowInCompact(false)
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "level",
        type: "text",
        label: descriptors.level?.edit?.form?.label || descriptors.level?.label || "Niveau",
      })
        .withGroup(descriptors.level?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.level?.edit?.form?.placeholder || "Ex: 50")
        .withShowInCompact(true)
        .withBulk(descriptors.level?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "hostility",
        type: "select",
        label: descriptors.hostility?.edit?.form?.label || descriptors.hostility?.label || "Hostilité",
      })
        .withGroup(descriptors.hostility?.edit?.form?.group || "Caractéristiques")
        .withShowInCompact(true)
        .withOptions(descriptors.hostility?.edit?.form?.options || [])
        .withDefaultValue(descriptors.hostility?.edit?.form?.defaultValue ?? 2)
        .withBulk(descriptors.hostility?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "location",
        type: "text",
        label: descriptors.location?.edit?.form?.label || descriptors.location?.label || "Localisation",
      })
        .withGroup(descriptors.location?.edit?.form?.group || "Caractéristiques")
        .withShowInCompact(false)
        .withoutBulk()
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
        key: "pa",
        type: "text",
        label: descriptors.pa?.edit?.form?.label || descriptors.pa?.label || "PA",
      })
        .withGroup(descriptors.pa?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.pa?.edit?.form?.placeholder || "Ex: 6")
        .withShowInCompact(false)
        .withBulk(descriptors.pa?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "pm",
        type: "text",
        label: descriptors.pm?.edit?.form?.label || descriptors.pm?.label || "PM",
      })
        .withGroup(descriptors.pm?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.pm?.edit?.form?.placeholder || "Ex: 3")
        .withShowInCompact(false)
        .withBulk(descriptors.pm?.edit?.form?.bulk || {
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
        .withGroup(descriptors.po?.edit?.form?.group || "Caractéristiques")
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
        key: "usable",
        type: "checkbox",
        label: descriptors.usable?.edit?.form?.label || descriptors.usable?.label || "Utilisable",
      })
        .withGroup(descriptors.usable?.edit?.form?.group || "Statut")
        .withShowInCompact(true)
        .withDefaultValue(descriptors.usable?.edit?.form?.defaultValue ?? false)
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
        .withOptions(descriptors.is_visible?.edit?.form?.options || [])
        .withDefaultValue(descriptors.is_visible?.edit?.form?.defaultValue ?? "guest")
        .withBulk(descriptors.is_visible?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    );

  return formConfig.build();
}

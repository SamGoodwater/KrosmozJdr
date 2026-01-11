/**
 * ConsumableFormConfig — Configuration des formulaires pour l'entité Consumable
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les consommables.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getConsumableFieldDescriptors } from "./consumable-descriptors.js";

/**
 * Crée la configuration des formulaires pour Consumable
 *
 * @param {Object} [ctx] - Contexte (permissions, consumableTypes, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createConsumableFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const consumableTypes = Array.isArray(ctx?.consumableTypes)
    ? ctx.consumableTypes
    : Array.isArray(ctx?.meta?.consumableTypes)
    ? ctx.meta.consumableTypes
    : [];

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getConsumableFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "consumable",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Informations générales", label: "Informations générales", order: 1 })
    .addGroup({ name: "Métier", label: "Métier", order: 2 })
    .addGroup({ name: "Statut", label: "Statut", order: 3 })
    .addGroup({ name: "Métadonnées", label: "Métadonnées", order: 4 })
    .addGroup({ name: "Contenu", label: "Contenu", order: 5 })
    .addGroup({ name: "Image", label: "Image", order: 6 });

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
        key: "level",
        type: "text",
        label: descriptors.level?.edit?.form?.label || descriptors.level?.label || "Niveau",
      })
        .withGroup(descriptors.level?.edit?.form?.group || "Métier")
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
        key: "consumable_type_id",
        type: "select",
        label: descriptors.consumable_type?.edit?.form?.label || descriptors.consumable_type?.label || "Type de consommable",
      })
        .withGroup(descriptors.consumable_type?.edit?.form?.group || "Métier")
        .withHelp(descriptors.consumable_type?.edit?.form?.help || "Définit le type du consommable.")
        .withShowInCompact(true)
        .withOptions(descriptors.consumable_type?.edit?.form?.options || (() => [{ value: "", label: "—" }, ...consumableTypes.map((t) => ({ value: t.id, label: t.name }))]))
        .withBulk(descriptors.consumable_type?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "rarity",
        type: "select",
        label: descriptors.rarity?.edit?.form?.label || descriptors.rarity?.label || "Rareté",
      })
        .withGroup(descriptors.rarity?.edit?.form?.group || "Métier")
        .withHelp(descriptors.rarity?.edit?.form?.help || "La rareté est un entier (0..5). En bulk, laisser vide n'applique aucun changement.")
        .withShowInCompact(true)
        .withOptions(descriptors.rarity?.edit?.form?.options || [])
        .withBulk(descriptors.rarity?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" || v === null ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "price",
        type: "text",
        label: descriptors.price?.edit?.form?.label || descriptors.price?.label || "Prix",
      })
        .withGroup(descriptors.price?.edit?.form?.group || "Métadonnées")
        .withShowInCompact(true)
        .withBulk(descriptors.price?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "dofus_version",
        type: "text",
        label: descriptors.dofus_version?.edit?.form?.label || descriptors.dofus_version?.label || "Version Dofus",
      })
        .withGroup(descriptors.dofus_version?.edit?.form?.group || "Métadonnées")
        .withShowInCompact(true)
        .withBulk(descriptors.dofus_version?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "is_visible",
        type: "select",
        label: descriptors.is_visible?.edit?.form?.label || descriptors.is_visible?.label || "Visibilité",
      })
        .withGroup(descriptors.is_visible?.edit?.form?.group || "Statut")
        .withHelp(descriptors.is_visible?.edit?.form?.help || "Contrôle la visibilité côté front. Le backend reste la vérité sécurité.")
        .withShowInCompact(true)
        .withOptions(descriptors.is_visible?.edit?.form?.options || [])
        .withBulk(descriptors.is_visible?.edit?.form?.bulk || {
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
        key: "image",
        type: "text",
        label: descriptors.image?.edit?.form?.label || descriptors.image?.label || "Image (URL)",
      })
        .withGroup(descriptors.image?.edit?.form?.group || "Image")
        .withShowInCompact(false)
        .withBulk(descriptors.image?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "official_id",
        type: "text",
        label: descriptors.official_id?.edit?.form?.label || descriptors.official_id?.label || "ID Officiel",
      })
        .withGroup(descriptors.official_id?.edit?.form?.group || "Métadonnées")
        .withShowInCompact(false)
        .withBulk(descriptors.official_id?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    );

  // Champs conditionnels (selon permissions)
  if (canUpdateAny && descriptors.auto_update) {
    formConfig
      .addField(
        new FormFieldConfig({
          key: "auto_update",
          type: "checkbox",
          label: descriptors.auto_update.edit?.form?.label || descriptors.auto_update.label || "Auto-update",
        })
          .withGroup(descriptors.auto_update.edit?.form?.group || "Statut")
          .withShowInCompact(true)
          .withDefaultValue(descriptors.auto_update.edit?.form?.defaultValue ?? false)
          .withBulk(descriptors.auto_update.edit?.form?.bulk || {
            enabled: true,
            nullable: false,
            build: (v) => v === "1" || v === true,
          })
      );
  }

  if (canUpdateAny && descriptors.dofusdb_id) {
    formConfig
      .addField(
        new FormFieldConfig({
          key: "dofusdb_id",
          type: "text",
          label: descriptors.dofusdb_id.edit?.form?.label || descriptors.dofusdb_id.label || "DofusDB ID",
        })
          .withGroup(descriptors.dofusdb_id.edit?.form?.group || "Métadonnées")
          .withHelp(descriptors.dofusdb_id.edit?.form?.help || "ID externe DofusDB. Généralement géré automatiquement par le scrapping.")
          .withShowInCompact(false)
          .withBulk(descriptors.dofusdb_id.edit?.form?.bulk || {
            enabled: true,
            nullable: true,
            build: (v) => (v === "" ? null : String(v)),
          })
      );
  }

  return formConfig.build();
}

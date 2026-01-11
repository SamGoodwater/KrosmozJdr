/**
 * SpellFormConfig — Configuration des formulaires pour l'entité Spell
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les sorts.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getSpellFieldDescriptors } from "./spell-descriptors.js";

/**
 * Crée la configuration des formulaires pour Spell
 *
 * @param {Object} [ctx] - Contexte (permissions, spellTypes, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createSpellFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const spellTypes = Array.isArray(ctx?.spellTypes)
    ? ctx.spellTypes
    : Array.isArray(ctx?.meta?.spellTypes)
    ? ctx.meta.spellTypes
    : [];

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getSpellFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "spell",
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
        .withPlaceholder(descriptors.po?.edit?.form?.placeholder || "Ex: 1-6")
        .withShowInCompact(true)
        .withBulk(descriptors.po?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "area",
        type: "number",
        label: descriptors.area?.edit?.form?.label || descriptors.area?.label || "Zone",
      })
        .withGroup(descriptors.area?.edit?.form?.group || "Métier")
        .withShowInCompact(true)
        .withBulk(descriptors.area?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
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
        .withBulk(descriptors.element?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "category",
        type: "select",
        label: descriptors.category?.edit?.form?.label || descriptors.category?.label || "Catégorie",
      })
        .withGroup(descriptors.category?.edit?.form?.group || "Métier")
        .withShowInCompact(true)
        .withBulk(descriptors.category?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
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

/**
 * PanoplyFormConfig — Configuration des formulaires pour l'entité Panoply
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les panoplies.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getPanoplyFieldDescriptors } from "./panoply-descriptors.js";

/**
 * Crée la configuration des formulaires pour Panoply
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createPanoplyFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getPanoplyFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "panoply",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Métier", label: "Métier", order: 1 })
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
        .withBulk(descriptors.description?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "bonus",
        type: "textarea",
        label: descriptors.bonus?.edit?.form?.label || descriptors.bonus?.label || "Bonus",
      })
        .withGroup(descriptors.bonus?.edit?.form?.group || "Métier")
        .withPlaceholder(descriptors.bonus?.edit?.form?.placeholder || "Ex: +10 Force, +5 Agilité")
        .withShowInCompact(true)
        .withBulk(descriptors.bonus?.edit?.form?.bulk || {
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
        key: "dofusdb_id",
        type: "text",
        label: descriptors.dofusdb_id?.edit?.form?.label || descriptors.dofusdb_id?.label || "DofusDB ID",
      })
        .withGroup(descriptors.dofusdb_id?.edit?.form?.group || "Métadonnées")
        .withShowInCompact(false)
        .withBulk(descriptors.dofusdb_id?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    );

  return formConfig.build();
}

/**
 * NpcFormConfig — Configuration des formulaires pour l'entité NPC
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les NPCs.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getNpcFieldDescriptors } from "./npc-descriptors.js";

/**
 * Crée la configuration des formulaires pour NPC
 *
 * @param {Object} [ctx] - Contexte (permissions, creatures, classes, specializations, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createNpcFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const creatures = Array.isArray(ctx?.creatures)
    ? ctx.creatures
    : Array.isArray(ctx?.meta?.creatures)
    ? ctx.meta.creatures
    : [];
  
  const classes = Array.isArray(ctx?.classes)
    ? ctx.classes
    : Array.isArray(ctx?.meta?.classes)
    ? ctx.meta.classes
    : [];
  
  const specializations = Array.isArray(ctx?.specializations)
    ? ctx.specializations
    : Array.isArray(ctx?.meta?.specializations)
    ? ctx.meta.specializations
    : [];

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getNpcFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "npc",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Relations", label: "Relations", order: 1 })
    .addGroup({ name: "Description", label: "Description", order: 2 })
    .addGroup({ name: "Caractéristiques", label: "Caractéristiques", order: 3 });

  // Champs de formulaire
  formConfig
    .addField(
      new FormFieldConfig({
        key: "creature_id",
        type: "select",
        label: descriptors.creature_name?.edit?.form?.label || descriptors.creature_name?.label || "Créature",
      })
        .withGroup(descriptors.creature_name?.edit?.form?.group || "Relations")
        .withRequired(descriptors.creature_name?.edit?.form?.required ?? true)
        .withShowInCompact(true)
        .withOptions(descriptors.creature_name?.edit?.form?.options || (() => [{ value: "", label: "—" }, ...creatures.map((c) => ({ value: c.id, label: c.name }))]))
        .withoutBulk()
    )
    .addField(
      new FormFieldConfig({
        key: "classe_id",
        type: "select",
        label: descriptors.classe?.edit?.form?.label || descriptors.classe?.label || "Classe",
      })
        .withGroup(descriptors.classe?.edit?.form?.group || "Relations")
        .withShowInCompact(true)
        .withOptions(descriptors.classe?.edit?.form?.options || (() => [{ value: "", label: "—" }, ...classes.map((c) => ({ value: c.id, label: c.name }))]))
        .withBulk(descriptors.classe?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "specialization_id",
        type: "select",
        label: descriptors.specialization?.edit?.form?.label || descriptors.specialization?.label || "Spécialisation",
      })
        .withGroup(descriptors.specialization?.edit?.form?.group || "Relations")
        .withShowInCompact(false)
        .withOptions(descriptors.specialization?.edit?.form?.options || (() => [{ value: "", label: "—" }, ...specializations.map((s) => ({ value: s.id, label: s.name }))]))
        .withBulk(descriptors.specialization?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "story",
        type: "textarea",
        label: descriptors.story?.edit?.form?.label || descriptors.story?.label || "Histoire",
      })
        .withGroup(descriptors.story?.edit?.form?.group || "Description")
        .withShowInCompact(false)
        .withBulk(descriptors.story?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "historical",
        type: "textarea",
        label: descriptors.historical?.edit?.form?.label || descriptors.historical?.label || "Historique",
      })
        .withGroup(descriptors.historical?.edit?.form?.group || "Description")
        .withShowInCompact(false)
        .withBulk(descriptors.historical?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "age",
        type: "text",
        label: descriptors.age?.edit?.form?.label || descriptors.age?.label || "Âge",
      })
        .withGroup(descriptors.age?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.age?.edit?.form?.placeholder || "Ex: 25 ans")
        .withShowInCompact(true)
        .withBulk(descriptors.age?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "size",
        type: "text",
        label: descriptors.size?.edit?.form?.label || descriptors.size?.label || "Taille",
      })
        .withGroup(descriptors.size?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.size?.edit?.form?.placeholder || "Ex: 1m75")
        .withShowInCompact(true)
        .withBulk(descriptors.size?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : String(v)),
        })
    );

  return formConfig.build();
}

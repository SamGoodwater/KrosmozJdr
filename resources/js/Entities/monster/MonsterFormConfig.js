/**
 * MonsterFormConfig — Configuration des formulaires pour l'entité Monster
 *
 * @description
 * Configuration des formulaires d'édition (simple et bulk) pour les monstres.
 * Utilise les descriptors simplifiés pour obtenir les labels, groupes, etc.
 */

import { FormConfig } from "../entity/FormConfig.js";
import { FormFieldConfig } from "../entity/FormFieldConfig.js";
import { getMonsterFieldDescriptors } from "./monster-descriptors.js";

/**
 * Crée la configuration des formulaires pour Monster
 *
 * @param {Object} [ctx] - Contexte (permissions, creatures, monsterRaces, etc.)
 * @returns {FormConfig} Configuration des formulaires
 */
export function createMonsterFormConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const creatures = Array.isArray(ctx?.creatures)
    ? ctx.creatures
    : Array.isArray(ctx?.meta?.creatures)
    ? ctx.meta.creatures
    : [];
  
  const monsterRaces = Array.isArray(ctx?.monsterRaces)
    ? ctx.monsterRaces
    : Array.isArray(ctx?.meta?.monsterRaces)
    ? ctx.meta.monsterRaces
    : [];

  // Récupérer les descriptors pour obtenir labels, groupes, etc.
  const descriptors = getMonsterFieldDescriptors(ctx);

  const formConfig = new FormConfig({
    entityType: "monster",
  });

  // Groupes de champs
  formConfig
    .addGroup({ name: "Relations", label: "Relations", order: 1 })
    .addGroup({ name: "Caractéristiques", label: "Caractéristiques", order: 2 })
    .addGroup({ name: "Statut", label: "Statut", order: 3 })
    .addGroup({ name: "Métadonnées", label: "Métadonnées", order: 4 });

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
        key: "monster_race_id",
        type: "select",
        label: descriptors.monster_race?.edit?.form?.label || descriptors.monster_race?.label || "Race",
      })
        .withGroup(descriptors.monster_race?.edit?.form?.group || "Relations")
        .withShowInCompact(true)
        .withOptions(descriptors.monster_race?.edit?.form?.options || (() => [{ value: "", label: "—" }, ...monsterRaces.map((r) => ({ value: r.id, label: r.name }))]))
        .withBulk(descriptors.monster_race?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "size",
        type: "select",
        label: descriptors.size?.edit?.form?.label || descriptors.size?.label || "Taille",
      })
        .withGroup(descriptors.size?.edit?.form?.group || "Caractéristiques")
        .withShowInCompact(true)
        .withOptions(descriptors.size?.edit?.form?.options || [])
        .withDefaultValue(descriptors.size?.edit?.form?.defaultValue ?? 2)
        .withBulk(descriptors.size?.edit?.form?.bulk || {
          enabled: true,
          nullable: true,
          build: (v) => (v === "" ? null : Number(v)),
        })
    )
    .addField(
      new FormFieldConfig({
        key: "is_boss",
        type: "checkbox",
        label: descriptors.is_boss?.edit?.form?.label || descriptors.is_boss?.label || "Boss",
      })
        .withGroup(descriptors.is_boss?.edit?.form?.group || "Caractéristiques")
        .withShowInCompact(true)
        .withDefaultValue(descriptors.is_boss?.edit?.form?.defaultValue ?? false)
        .withBulk(descriptors.is_boss?.edit?.form?.bulk || {
          enabled: true,
          nullable: false,
          build: (v) => v === "1" || v === true,
        })
    )
    .addField(
      new FormFieldConfig({
        key: "boss_pa",
        type: "text",
        label: descriptors.boss_pa?.edit?.form?.label || descriptors.boss_pa?.label || "PA Boss",
      })
        .withGroup(descriptors.boss_pa?.edit?.form?.group || "Caractéristiques")
        .withPlaceholder(descriptors.boss_pa?.edit?.form?.placeholder || "Ex: 6")
        .withShowInCompact(false)
        .withBulk(descriptors.boss_pa?.edit?.form?.bulk || {
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
        .withShowInCompact(false)
        .withBulk(descriptors.dofus_version?.edit?.form?.bulk || {
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

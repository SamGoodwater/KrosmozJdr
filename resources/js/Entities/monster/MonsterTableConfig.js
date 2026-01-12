/**
 * MonsterTableConfig — Configuration du tableau pour l'entité Monster
 *
 * @description
 * Configuration du tableau TanStack pour les monstres avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";
import { getMonsterFieldDescriptors } from "./monster-descriptors.js";

/**
 * Crée la configuration du tableau pour Monster
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createMonsterTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getMonsterFieldDescriptors(ctx);

  // Créer la configuration de base depuis _tableConfig dans les descriptors
  const tableConfigData = descriptors._tableConfig || {};
  
  const tableConfig = new TableConfig({
    id: tableConfigData.id || "monsters.index",
    entityType: tableConfigData.entityType || "monster",
  });

  // Appliquer les configurations globales depuis _tableConfig
  if (tableConfigData.quickEdit) {
    tableConfig.withQuickEdit(tableConfigData.quickEdit);
  }
  if (tableConfigData.actions) {
    tableConfig.withActions(tableConfigData.actions);
  }
  if (tableConfigData.features) {
    tableConfig.withFeatures(tableConfigData.features);
  }
  if (tableConfigData.ui) {
    tableConfig.withUI(tableConfigData.ui);
  }

  // Colonnes du tableau
  tableConfig
    .addColumn(
      new TableColumnConfig({
        key: "id",
        label: "ID",
        type: "text",
      })
        .withPermission("createAny")
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(0)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "created_at",
        label: "Créé le",
        type: "text",
      })
        .withPermission("createAny")
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(1)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "updated_at",
        label: "Modifié le",
        type: "text",
      })
        .withPermission("createAny")
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(2)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "creature_name",
        label: descriptors.creature_name?.label || "Créature",
        type: "route",
        icon: descriptors.creature_name?.icon || "fa-solid fa-dragon",
      })
        .asMain(true)
        .withOrder(3)
        .withSort(true)
        .withSearch(true)
        .withFormat({
          xs: { mode: "truncate", maxLength: 20 },
          sm: { mode: "truncate", maxLength: 30 },
          md: { mode: "truncate", maxLength: 44 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "monster_race",
        label: descriptors.monster_race?.label || "Race",
        type: "text",
        icon: descriptors.monster_race?.icon || "fa-solid fa-users",
      })
        .withOrder(4)
        .withSort(true)
        .withFilter({ id: "monster_race_id", type: "multi" })
        .withFormat({
          xs: { mode: "truncate", maxLength: 10 },
          sm: { mode: "truncate", maxLength: 15 },
          md: { mode: "truncate", maxLength: 20 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "size",
        label: descriptors.size?.label || "Taille",
        type: "badge",
        icon: descriptors.size?.icon || "fa-solid fa-expand",
      })
        .withOrder(5)
        .withSort(true)
        .withFilter({
          id: "size",
          type: "multi",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "size",
              autoLabelFrom: "value",
              glassy: true,
              variant: "soft",
            },
          },
        })
        .withFormat({
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_boss",
        label: descriptors.is_boss?.label || "Boss",
        type: "badge",
        icon: descriptors.is_boss?.icon || "fa-solid fa-crown",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(6)
        .withSort(true)
        .withFilter({ id: "is_boss", type: "boolean" })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "boss_pa",
        label: descriptors.boss_pa?.label || "PA Boss",
        type: "text",
        icon: descriptors.boss_pa?.icon || "fa-solid fa-bolt",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(7)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "dofus_version",
        label: descriptors.dofus_version?.label || "Version Dofus",
        type: "text",
        icon: descriptors.dofus_version?.icon || "fa-solid fa-code-branch",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(8)
        .withSort(true)
        
    );

  // Colonnes conditionnelles (selon permissions)
  if (canUpdateAny) {
    tableConfig
      .addColumn(
        new TableColumnConfig({
          key: "auto_update",
          label: descriptors.auto_update?.label || "Auto-update",
          type: "badge",
          icon: descriptors.auto_update?.icon || "fa-solid fa-arrows-rotate",
        })
          .withPermission("updateAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(9)
          .withSort(true)
          .withFilter({ id: "auto_update", type: "boolean" })
          
      )
      .addColumn(
        new TableColumnConfig({
          key: "dofusdb_id",
          label: descriptors.dofusdb_id?.label || "DofusDB",
          type: "routeExternal",
          icon: descriptors.dofusdb_id?.icon || "fa-solid fa-arrow-up-right-from-square",
        })
          .withPermission("updateAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(10)
          .withSort(true)
          .withFormat({
            xs: { mode: "truncate", maxLength: 10 },
            sm: { mode: "truncate", maxLength: 15 },
            md: { mode: "truncate", maxLength: 20 },
            lg: { mode: "full" },
            xl: { mode: "full" },
          })
          
      );
  }

  if (canCreateAny) {
    tableConfig
      .addColumn(
        new TableColumnConfig({
          key: "created_at",
          label: descriptors.created_at?.label || "Créé le",
          type: "date",
          icon: descriptors.created_at?.icon || "fa-solid fa-calendar",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(11)
          .withSort(true)
          
      )
      .addColumn(
        new TableColumnConfig({
          key: "updated_at",
          label: descriptors.updated_at?.label || "Modifié le",
          type: "date",
          icon: descriptors.updated_at?.icon || "fa-solid fa-clock",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(12)
          .withSort(true)
          
      );
  }

  return tableConfig;
}

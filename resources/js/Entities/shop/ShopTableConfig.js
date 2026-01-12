/**
 * ShopTableConfig — Configuration du tableau pour l'entité Shop
 *
 * @description
 * Configuration du tableau TanStack pour les boutiques avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";
import { getShopFieldDescriptors } from "./shop-descriptors.js";

/**
 * Crée la configuration du tableau pour Shop
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createShopTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getShopFieldDescriptors(ctx);

  // Créer la configuration de base depuis _tableConfig dans les descriptors
  const tableConfigData = descriptors._tableConfig || {};
  
  const tableConfig = new TableConfig({
    id: tableConfigData.id || "shops.index",
    entityType: tableConfigData.entityType || "shop",
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
        key: "name",
        label: descriptors.name?.label || "Nom",
        type: "route",
        icon: descriptors.name?.icon || "fa-solid fa-font",
      })
        .asMain(true)
        .withOrder(1)
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
        key: "location",
        label: descriptors.location?.label || "Localisation",
        type: "text",
        icon: descriptors.location?.icon || "fa-solid fa-map-marker-alt",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(2)
        .withSort(true)
        .withFormat({
          xs: { mode: "truncate", maxLength: 15 },
          sm: { mode: "truncate", maxLength: 20 },
          md: { mode: "truncate", maxLength: 30 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "npc_name",
        label: descriptors.npc_name?.label || "PNJ",
        type: "text",
        icon: descriptors.npc_name?.icon || "fa-solid fa-user-ninja",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(3)
        .withSort(true)
        .withFormat({
          xs: { mode: "truncate", maxLength: 15 },
          sm: { mode: "truncate", maxLength: 20 },
          md: { mode: "truncate", maxLength: 30 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "items_count",
        label: descriptors.items_count?.label || "Nb objets",
        type: "text",
        icon: descriptors.items_count?.icon || "fa-solid fa-boxes",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(4)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "price",
        label: descriptors.price?.label || "Prix",
        type: "text",
        icon: descriptors.price?.icon || "fa-solid fa-coins",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(5)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "usable",
        label: descriptors.usable?.label || "Utilisable",
        type: "badge",
        icon: descriptors.usable?.icon || "fa-solid fa-check-circle",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(6)
        .withSort(true)
        
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible?.label || "Visible",
        type: "badge",
        icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(7)
        .withSort(true)
        
    );

  // Colonnes conditionnelles (selon permissions)
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
          .withOrder(8)
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
          .withOrder(9)
          .withSort(true)
          
      );
  }

  return tableConfig;
}

/**
 * ItemTableConfig — Configuration du tableau pour l'entité Item
 *
 * @description
 * Configuration du tableau TanStack pour les items.
 * Utilise les informations des descriptors pour éviter la duplication (labels, icônes).
 * Les colonnes sont créées manuellement pour garder le contrôle total sur les configurations spéciales.
 */

import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";
import { getItemFieldDescriptors } from "./item-descriptors.js";

/**
 * Crée la configuration du tableau pour Item
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createItemTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getItemFieldDescriptors(ctx);

  // Créer la configuration de base depuis _tableConfig dans les descriptors
  const tableConfigData = descriptors._tableConfig || {};
  
  const tableConfig = new TableConfig({
    id: tableConfigData.id || "items.index",
    entityType: tableConfigData.entityType || "item",
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

  // Colonnes du tableau (utilisant les informations des descriptors)

  // Colonne ID (conditionnelle)
  if (canCreateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "id",
        label: descriptors.id?.label || "ID",
        type: "text",
        icon: descriptors.id?.icon || "fa-solid fa-hashtag",
      })
        .withPermission("createAny")
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(0)
        .withSort(true)
    );
  }

  // Colonne Image
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "image",
      label: descriptors.image?.label || "Image",
      type: "image",
      icon: descriptors.image?.icon || "fa-solid fa-image",
    })
      .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
      .withOrder(3)
  );

  // Colonne Name (principale)
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "name",
      label: descriptors.name?.label || "Nom",
      type: "route",
      icon: descriptors.name?.icon || "fa-solid fa-font",
    })
      .asMain(true)
      .withOrder(4)
      .withSort(true)
      .withSearch(true)
      .withFormat({
        xs: { mode: "truncate", maxLength: 20 },
        sm: { mode: "truncate", maxLength: 30 },
        md: { mode: "truncate", maxLength: 44 },
        lg: { mode: "full" },
        xl: { mode: "full" },
      })
  );

  // Colonne Level avec filtre complexe
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "level",
      label: descriptors.level?.label || "Niveau",
      type: "badge",
      icon: descriptors.level?.icon || "fa-solid fa-level-up-alt",
    })
      .withOrder(5)
      .withSort(true)
      .withFilter({
        id: "level",
        type: "multi",
        ui: {
          optionBadge: {
            enabled: true,
            color: "auto",
            autoScheme: "level",
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
  );

  // Colonne Item Type
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "item_type",
      label: descriptors.item_type?.label || "Type",
      type: "badge",
      icon: descriptors.item_type?.icon || "fa-solid fa-tag",
    })
      .withOrder(6)
      .withSort(true)
      .withFilter({ id: "item_type_id", type: "multi" })
      .withFormat({
        xs: { mode: "badge" },
        sm: { mode: "badge" },
        md: { mode: "badge" },
        lg: { mode: "badge" },
        xl: { mode: "badge" },
      })
  );

  // Colonne Rarity avec filtre complexe
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "rarity",
      label: descriptors.rarity?.label || "Rareté",
      type: "badge",
      icon: descriptors.rarity?.icon || "fa-solid fa-star",
    })
      .withOrder(7)
      .withSort(true)
      .withFilter({
        id: "rarity",
        type: "multi",
        ui: {
          optionBadge: {
            enabled: true,
            color: "auto",
            autoScheme: "rarity",
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
  );

  // Colonne Price
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "price",
      label: descriptors.price?.label || "Prix",
      type: "text",
      icon: descriptors.price?.icon || "fa-solid fa-coins",
    })
      .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
      .withOrder(8)
      .withSort(true)
  );

  // Colonne Usable
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "usable",
      label: descriptors.usable?.label || "Utilisable",
      type: "badge",
      icon: descriptors.usable?.icon || "fa-solid fa-check",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
      .withOrder(9)
      .withSort(true)
      .withFilter({ id: "usable", type: "boolean" })
  );

  // Colonne Is Visible
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "is_visible",
      label: descriptors.is_visible?.label || "Visibilité",
      type: "badge",
      icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
      .withOrder(10)
      .withSort(true)
      .withFilter({ id: "is_visible", type: "multi" })
  );

  // Colonne Dofus Version
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "dofus_version",
      label: descriptors.dofus_version?.label || "Version Dofus",
      type: "text",
      icon: descriptors.dofus_version?.icon || "fa-solid fa-code-branch",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
      .withOrder(11)
      .withSort(true)
  );

  // Colonnes conditionnelles selon permissions
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
          .withOrder(12)
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
          .withOrder(13)
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
          key: "created_by",
          label: descriptors.created_by?.label || "Créé par",
          type: "text",
          icon: descriptors.created_by?.icon || "fa-solid fa-user",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(14)
          .withSort(true)
          .withSearch(true)
      )
      .addColumn(
        new TableColumnConfig({
          key: "created_at",
          label: descriptors.created_at?.label || "Créé le",
          type: "date",
          icon: descriptors.created_at?.icon || "fa-solid fa-calendar",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(15)
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
          .withOrder(16)
          .withSort(true)
      );
  }

  return tableConfig;
}

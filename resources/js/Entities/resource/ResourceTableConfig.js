/**
 * ResourceTableConfig — Configuration du tableau pour l'entité Resource
 *
 * @description
 * Configuration du tableau TanStack pour les ressources avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getResourceFieldDescriptors } from "./resource-descriptors.js";

/**
 * Crée la configuration du tableau pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createResourceTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getResourceFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "resources.index",
    entityType: "resource",
  })
    .withQuickEdit({
      enabled: true,
      permission: "updateAny",
    })
    .withActions({
      enabled: true,
      permission: "view",
      available: ["view", "edit", "quick-edit", "delete", "copy-link", "download-pdf", "refresh"],
      defaultVisible: {
        xs: false,
        sm: true,
        md: true,
        lg: true,
        xl: true,
      },
    })
    .withFeatures({
      search: {
        enabled: true,
        placeholder: "Rechercher une ressource…",
        debounceMs: 200,
      },
      filters: { enabled: true },
      pagination: {
        enabled: true,
        perPage: { default: 25, options: [10, 25, 50, 100] },
      },
      selection: {
        enabled: true,
        checkboxMode: "auto",
        clickToSelect: true,
      },
      columnVisibility: {
        enabled: true,
        persist: true,
      },
      export: {
        csv: true,
        filename: "resources.csv",
      },
    })
    .withUI({
      skeletonRows: 10,
    });

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
        key: "image",
        label: "Image",
        type: "image",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(3)
    )
    .addColumn(
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
    )
    .addColumn(
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
    )
    .addColumn(
      new TableColumnConfig({
        key: "resource_type",
        label: descriptors.resource_type?.label || "Type",
        type: "badge",
        icon: descriptors.resource_type?.icon || "fa-solid fa-tag",
      })
        .withOrder(6)
        .withSort(true)
        .withFilter({ id: "resource_type_id", type: "multi" })
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
    )
    .addColumn(
      new TableColumnConfig({
        key: "price",
        label: descriptors.price?.label || "Prix",
        type: "text",
        icon: descriptors.price?.icon || "fa-solid fa-coins",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(8)
        .withSort(true)
    )
    .addColumn(
      new TableColumnConfig({
        key: "weight",
        label: descriptors.weight?.label || "Poids",
        type: "text",
        icon: descriptors.weight?.icon || "fa-solid fa-weight-hanging",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(9)
        .withSort(true)
    )
    .addColumn(
      new TableColumnConfig({
        key: "usable",
        label: descriptors.usable?.label || "Utilisable",
        type: "badge",
        icon: descriptors.usable?.icon || "fa-solid fa-check",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(10)
        .withSort(true)
        .withFilter({ id: "usable", type: "boolean" })
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible?.label || "Visibilité",
        type: "badge",
        icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(11)
        .withSort(true)
        .withFilter({ id: "is_visible", type: "multi" })
    )
    .addColumn(
      new TableColumnConfig({
        key: "dofus_version",
        label: descriptors.dofus_version?.label || "Version Dofus",
        type: "text",
        icon: descriptors.dofus_version?.icon || "fa-solid fa-code-branch",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(12)
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
          .withOrder(13)
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
          .withOrder(14)
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
          .withOrder(15)
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
          .withOrder(16)
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
          .withOrder(17)
          .withSort(true)
      );
  }

  return tableConfig;
}

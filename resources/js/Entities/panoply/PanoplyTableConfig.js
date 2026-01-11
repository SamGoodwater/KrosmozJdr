/**
 * PanoplyTableConfig — Configuration du tableau pour l'entité Panoply
 *
 * @description
 * Configuration du tableau TanStack pour les panoplies avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getPanoplyFieldDescriptors } from "./panoply-descriptors.js";

/**
 * Crée la configuration du tableau pour Panoply
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createPanoplyTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getPanoplyFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "panoplies.index",
    entityType: "panoply",
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
        placeholder: "Rechercher une panoplie…",
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
        filename: "panoplies.csv",
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
        .build()
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
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "bonus",
        label: descriptors.bonus?.label || "Bonus",
        type: "text",
        icon: descriptors.bonus?.icon || "fa-solid fa-star",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(2)
        .withSort(true)
        .withFormat({
          xs: { mode: "truncate", maxLength: 15 },
          sm: { mode: "truncate", maxLength: 20 },
          md: { mode: "truncate", maxLength: 30 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "items_count",
        label: descriptors.items_count?.label || "Nb objets",
        type: "text",
        icon: descriptors.items_count?.icon || "fa-solid fa-boxes",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(3)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "usable",
        label: descriptors.usable?.label || "Utilisable",
        type: "badge",
        icon: descriptors.usable?.icon || "fa-solid fa-check-circle",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(4)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible?.label || "Visible",
        type: "badge",
        icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(5)
        .withSort(true)
        .build()
    );

  // Colonnes conditionnelles (selon permissions)
  if (canUpdateAny) {
    tableConfig
      .addColumn(
        new TableColumnConfig({
          key: "dofusdb_id",
          label: descriptors.dofusdb_id?.label || "DofusDB ID",
          type: "text",
          icon: descriptors.dofusdb_id?.icon || "fa-solid fa-link",
        })
          .withPermission("updateAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(6)
          .withSort(true)
          .build()
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
          .withOrder(7)
          .withSort(true)
          .build()
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
          .withOrder(8)
          .withSort(true)
          .build()
      );
  }

  return tableConfig;
}

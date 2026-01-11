/**
 * CampaignTableConfig — Configuration du tableau pour l'entité Campaign
 *
 * @description
 * Configuration du tableau TanStack pour les campagnes avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getCampaignFieldDescriptors } from "./campaign-descriptors.js";

/**
 * Crée la configuration du tableau pour Campaign
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createCampaignTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getCampaignFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "campaigns.index",
    entityType: "campaign",
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
        placeholder: "Rechercher une campagne…",
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
        filename: "campaigns.csv",
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
        key: "slug",
        label: descriptors.slug?.label || "Slug",
        type: "text",
        icon: descriptors.slug?.icon || "fa-solid fa-link",
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
        key: "state",
        label: descriptors.state?.label || "État",
        type: "badge",
        icon: descriptors.state?.icon || "fa-solid fa-info-circle",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(3)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_public",
        label: descriptors.is_public?.label || "Public",
        type: "badge",
        icon: descriptors.is_public?.icon || "fa-solid fa-globe",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(4)
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
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(5)
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
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(6)
        .withSort(true)
        .build()
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

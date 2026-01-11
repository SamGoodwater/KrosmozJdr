/**
 * CreatureTableConfig — Configuration du tableau pour l'entité Creature
 *
 * @description
 * Configuration du tableau TanStack pour les créatures avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getCreatureFieldDescriptors } from "./creature-descriptors.js";

/**
 * Crée la configuration du tableau pour Creature
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createCreatureTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getCreatureFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "creatures.index",
    entityType: "creature",
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
        placeholder: "Rechercher une créature…",
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
        filename: "creatures.csv",
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
        key: "level",
        label: descriptors.level?.label || "Niveau",
        type: "text",
        icon: descriptors.level?.icon || "fa-solid fa-level-up-alt",
      })
        .withOrder(2)
        .withSort(true)
        .withFilter({ id: "level", type: "text" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "hostility",
        label: descriptors.hostility?.label || "Hostilité",
        type: "badge",
        icon: descriptors.hostility?.icon || "fa-solid fa-exclamation-triangle",
      })
        .withOrder(3)
        .withSort(true)
        .withFilter({
          id: "hostility",
          type: "multi",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "hostility",
              autoLabelFrom: "value",
              glassy: true,
              variant: "soft",
            },
          },
        })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "life",
        label: descriptors.life?.label || "Vie",
        type: "text",
        icon: descriptors.life?.icon || "fa-solid fa-heart",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(4)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "pa",
        label: descriptors.pa?.label || "PA",
        type: "text",
        icon: descriptors.pa?.icon || "fa-solid fa-running",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(5)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "pm",
        label: descriptors.pm?.label || "PM",
        type: "text",
        icon: descriptors.pm?.icon || "fa-solid fa-walking",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(6)
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
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(7)
        .withSort(true)
        .withFilter({ id: "usable", type: "boolean" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible?.label || "Visible",
        type: "badge",
        icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(8)
        .withSort(true)
        .withFilter({ id: "is_visible", type: "multi" })
        .build()
    );

  // Colonnes conditionnelles (selon permissions)
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
          .withOrder(9)
          .withSort(true)
          .build()
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
          .withOrder(10)
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
          .withOrder(11)
          .withSort(true)
          .build()
      );
  }

  return tableConfig;
}

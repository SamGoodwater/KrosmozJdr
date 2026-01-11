/**
 * SpellTableConfig — Configuration du tableau pour l'entité Spell
 *
 * @description
 * Configuration du tableau TanStack pour les sorts avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getSpellFieldDescriptors } from "./spell-descriptors.js";

/**
 * Crée la configuration du tableau pour Spell
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createSpellTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getSpellFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "spells.index",
    entityType: "spell",
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
        placeholder: "Rechercher un sort…",
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
        filename: "spells.csv",
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
        key: "created_at",
        label: "Créé le",
        type: "text",
      })
        .withPermission("createAny")
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
        .withOrder(1)
        .withSort(true)
        .build()
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
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "image",
        label: "Image",
        type: "image",
      })
        .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
        .withOrder(3)
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
        .build()
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
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "pa",
        label: descriptors.pa?.label || "PA",
        type: "text",
        icon: descriptors.pa?.icon || "fa-solid fa-bolt",
      })
        .withOrder(6)
        .withSort(true)
        .withFilter({ id: "pa", type: "multi" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "po",
        label: descriptors.po?.label || "PO",
        type: "text",
        icon: descriptors.po?.icon || "fa-solid fa-crosshairs",
      })
        .withOrder(7)
        .withSort(true)
        .withFilter({ id: "po", type: "multi" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "area",
        label: descriptors.area?.label || "Zone",
        type: "text",
        icon: descriptors.area?.icon || "fa-solid fa-expand",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(8)
        .withSort(true)
        .withFilter({ id: "area", type: "multi" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "element",
        label: descriptors.element?.label || "Élément",
        type: "badge",
        icon: descriptors.element?.icon || "fa-solid fa-fire",
      })
        .withOrder(9)
        .withSort(true)
        .withFilter({
          id: "element",
          type: "multi",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "element",
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
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "category",
        label: descriptors.category?.label || "Catégorie",
        type: "badge",
        icon: descriptors.category?.icon || "fa-solid fa-tag",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(10)
        .withSort(true)
        .withFilter({
          id: "category",
          type: "multi",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "category",
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
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "usable",
        label: descriptors.usable?.label || "Utilisable",
        type: "badge",
        icon: descriptors.usable?.icon || "fa-solid fa-check",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(11)
        .withSort(true)
        .withFilter({ id: "usable", type: "boolean" })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible?.label || "Visibilité",
        type: "badge",
        icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
        .withOrder(12)
        .withSort(true)
        .withFilter({ id: "is_visible", type: "multi" })
        .build()
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
          .build()
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
          .build()
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
          .withOrder(16)
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
          .withOrder(17)
          .withSort(true)
          .build()
      );
  }

  return tableConfig;
}

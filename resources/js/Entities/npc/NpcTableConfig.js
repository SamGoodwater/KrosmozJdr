/**
 * NpcTableConfig — Configuration du tableau pour l'entité NPC
 *
 * @description
 * Configuration du tableau TanStack pour les NPCs avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getNpcFieldDescriptors } from "./npc-descriptors.js";

/**
 * Crée la configuration du tableau pour NPC
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createNpcTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getNpcFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "npcs.index",
    entityType: "npc",
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
        placeholder: "Rechercher un NPC…",
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
        filename: "npcs.csv",
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
        key: "creature_name",
        label: descriptors.creature_name?.label || "Créature",
        type: "route",
        icon: descriptors.creature_name?.icon || "fa-solid fa-user",
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
        key: "classe",
        label: descriptors.classe?.label || "Classe",
        type: "text",
        icon: descriptors.classe?.icon || "fa-solid fa-user-tie",
      })
        .withOrder(2)
        .withSort(true)
        .withFilter({ id: "classe_id", type: "multi" })
        .withFormat({
          xs: { mode: "truncate", maxLength: 10 },
          sm: { mode: "truncate", maxLength: 15 },
          md: { mode: "truncate", maxLength: 20 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "specialization",
        label: descriptors.specialization?.label || "Spécialisation",
        type: "text",
        icon: descriptors.specialization?.icon || "fa-solid fa-star",
      })
        .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
        .withOrder(3)
        .withSort(true)
        .withFilter({ id: "specialization_id", type: "multi" })
        .withFormat({
          xs: { mode: "truncate", maxLength: 10 },
          sm: { mode: "truncate", maxLength: 15 },
          md: { mode: "truncate", maxLength: 20 },
          lg: { mode: "full" },
          xl: { mode: "full" },
        })
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "age",
        label: descriptors.age?.label || "Âge",
        type: "text",
        icon: descriptors.age?.icon || "fa-solid fa-birthday-cake",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(4)
        .withSort(true)
        .build()
    )
    .addColumn(
      new TableColumnConfig({
        key: "size",
        label: descriptors.size?.label || "Taille",
        type: "text",
        icon: descriptors.size?.icon || "fa-solid fa-expand",
      })
        .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: true })
        .withOrder(5)
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
          .withOrder(6)
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
          .withOrder(7)
          .withSort(true)
          .build()
      );
  }

  return tableConfig;
}

/**
 * ResourceTypeTableConfig — Configuration du tableau pour l'entité ResourceType
 *
 * @description
 * Configuration du tableau TanStack pour les types de ressources avec :
 * - Colonnes avec permissions et formatage responsive (xs-xl)
 * - Configuration quickEdit
 * - Configuration actions
 * 
 * Utilise les descriptors simplifiés pour obtenir les labels, icônes et configurations.
 */

import { TableConfig } from "../entity/TableConfig.js";
import { TableColumnConfig } from "../entity/TableColumnConfig.js";
import { getResourceTypeFieldDescriptors } from "./resource-type-descriptors.js";

/**
 * Crée la configuration du tableau pour ResourceType
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createResourceTypeTableConfig(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  // Récupérer les descriptors pour obtenir labels, icônes, etc.
  const descriptors = getResourceTypeFieldDescriptors(ctx);

  const tableConfig = new TableConfig({
    id: "resource-types.index",
    entityType: "resource-type",
  })
    .withQuickEdit({
      enabled: true,
      permission: "updateAny",
    })
    .withActions({
      enabled: true,
      permission: "view",
      available: ["view", "edit", "quick-edit", "delete", "copy-link", "refresh"],
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
        placeholder: "Rechercher un type de ressource…",
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
    })
    .withUI({
      skeletonRows: 10,
    });

  // Colonne principale : name
  if (descriptors.name) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "name",
        label: descriptors.name.label || "Nom",
        type: "route",
        icon: descriptors.name.icon || "fa-solid fa-tag",
      })
        .withIsMain(true)
        .withHideable(false)
        .withSort(true)
        .withSearch(true)
        .withDefaultVisible({
          xs: true,
          sm: true,
          md: true,
          lg: true,
          xl: true,
        })
    );
  }

  // Colonne : decision
  if (descriptors.decision) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "decision",
        label: descriptors.decision.label || "Statut",
        type: "badge",
        icon: descriptors.decision.icon || "fa-solid fa-circle-check",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: true,
          sm: true,
          md: true,
          lg: true,
          xl: true,
        })
    );
  }

  // Colonne : usable
  if (descriptors.usable) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "usable",
        label: descriptors.usable.label || "Utilisable",
        type: "badge",
        icon: descriptors.usable.icon || "fa-solid fa-check-circle",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: true,
          lg: true,
          xl: true,
        })
    );
  }

  // Colonne : is_visible
  if (descriptors.is_visible) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "is_visible",
        label: descriptors.is_visible.label || "Visibilité",
        type: "badge",
        icon: descriptors.is_visible.icon || "fa-solid fa-eye",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: true,
          xl: true,
        })
    );
  }

  // Colonne : resources_count
  if (descriptors.resources_count) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "resources_count",
        label: descriptors.resources_count.label || "Ressources",
        type: "text",
        icon: descriptors.resources_count.icon || "fa-solid fa-cubes",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: true,
          xl: true,
        })
    );
  }

  // Colonne : dofusdb_type_id (conditionnelle)
  if (descriptors.dofusdb_type_id && canUpdateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "dofusdb_type_id",
        label: descriptors.dofusdb_type_id.label || "DofusDB typeId",
        type: "text",
        icon: descriptors.dofusdb_type_id.icon || "fa-solid fa-database",
      })
        .withPermission("updateAny")
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: true,
        })
    );
  }

  // Colonne : seen_count
  if (descriptors.seen_count) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "seen_count",
        label: descriptors.seen_count.label || "Détections",
        type: "text",
        icon: descriptors.seen_count.icon || "fa-solid fa-eye",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: true,
        })
    );
  }

  // Colonne : last_seen_at
  if (descriptors.last_seen_at) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "last_seen_at",
        label: descriptors.last_seen_at.label || "Dernière détection",
        type: "date",
        icon: descriptors.last_seen_at.icon || "fa-solid fa-clock",
      })
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: true,
        })
    );
  }

  // Colonne : id (conditionnelle)
  if (descriptors.id && canUpdateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "id",
        label: descriptors.id.label || "ID",
        type: "text",
        icon: descriptors.id.icon || "fa-solid fa-hashtag",
      })
        .withPermission("updateAny")
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        })
    );
  }

  // Colonne : created_at (conditionnelle)
  if (descriptors.created_at && canCreateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "created_at",
        label: descriptors.created_at.label || "Créé le",
        type: "date",
        icon: descriptors.created_at.icon || "fa-solid fa-calendar-plus",
      })
        .withPermission("createAny")
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        })
    );
  }

  // Colonne : updated_at (conditionnelle)
  if (descriptors.updated_at && canCreateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "updated_at",
        label: descriptors.updated_at.label || "Modifié le",
        type: "date",
        icon: descriptors.updated_at.icon || "fa-solid fa-calendar-check",
      })
        .withPermission("createAny")
        .withSort(true)
        .withDefaultVisible({
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        })
    );
  }

  return tableConfig.build(ctx);
}

/**
 * ResourceTypeTableConfig — Configuration du tableau pour l'entité ResourceType
 *
 * @description
 * Configuration du tableau TanStack pour les types de ressources.
 * Utilise les informations des descriptors pour éviter la duplication (labels, icônes).
 * Les colonnes sont créées manuellement pour garder le contrôle total sur les configurations spéciales.
 */

import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";
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

  // Créer la configuration de base depuis _tableConfig dans les descriptors
  const tableConfigData = descriptors._tableConfig || {};
  
  const tableConfig = new TableConfig({
    id: tableConfigData.id || "resource-types.index",
    entityType: tableConfigData.entityType || "resource-type",
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

  // Colonne Name (principale)
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "name",
      label: descriptors.name?.label || "Nom",
      type: "route",
      icon: descriptors.name?.icon || "fa-solid fa-tag",
    })
      .asMain(true)
      .withSort(true)
      .withSearch(true)
      .withDefaultVisible({
        xs: true,
        sm: true,
        md: true,
        lg: true,
        xl: true,
      })
      .withFormat({
        xs: { mode: "truncate", maxLength: 15 },
        sm: { mode: "truncate", maxLength: 20 },
        md: { mode: "truncate", maxLength: 30 },
        lg: { mode: "full" },
        xl: { mode: "full" },
      })
  );

  // Colonne Decision
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "decision",
      label: descriptors.decision?.label || "Statut",
      type: "badge",
      icon: descriptors.decision?.icon || "fa-solid fa-circle-check",
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

  // Colonne Usable
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "usable",
      label: descriptors.usable?.label || "Utilisable",
      type: "badge",
      icon: descriptors.usable?.icon || "fa-solid fa-check-circle",
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

  // Colonne Is Visible
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "is_visible",
      label: descriptors.is_visible?.label || "Visibilité",
      type: "badge",
      icon: descriptors.is_visible?.icon || "fa-solid fa-eye",
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

  // Colonne Resources Count
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "resources_count",
      label: descriptors.resources_count?.label || "Ressources",
      type: "text",
      icon: descriptors.resources_count?.icon || "fa-solid fa-cubes",
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

  // Colonnes conditionnelles selon permissions
  if (canUpdateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "dofusdb_type_id",
        label: descriptors.dofusdb_type_id?.label || "DofusDB typeId",
        type: "text",
        icon: descriptors.dofusdb_type_id?.icon || "fa-solid fa-database",
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

  // Colonne Seen Count
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "seen_count",
      label: descriptors.seen_count?.label || "Détections",
      type: "text",
      icon: descriptors.seen_count?.icon || "fa-solid fa-eye",
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

  // Colonne Last Seen At
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "last_seen_at",
      label: descriptors.last_seen_at?.label || "Dernière détection",
      type: "date",
      icon: descriptors.last_seen_at?.icon || "fa-solid fa-clock",
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

  // Colonnes conditionnelles selon permissions
  if (canUpdateAny) {
    tableConfig.addColumn(
      new TableColumnConfig({
        key: "id",
        label: descriptors.id?.label || "ID",
        type: "text",
        icon: descriptors.id?.icon || "fa-solid fa-hashtag",
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

  if (canCreateAny) {
    tableConfig
      .addColumn(
        new TableColumnConfig({
          key: "created_at",
          label: descriptors.created_at?.label || "Créé le",
          type: "date",
          icon: descriptors.created_at?.icon || "fa-solid fa-calendar-plus",
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
      )
      .addColumn(
        new TableColumnConfig({
          key: "updated_at",
          label: descriptors.updated_at?.label || "Modifié le",
          type: "date",
          icon: descriptors.updated_at?.icon || "fa-solid fa-calendar-check",
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

  return tableConfig;
}

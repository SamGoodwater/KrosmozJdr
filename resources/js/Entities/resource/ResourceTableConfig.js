/**
 * ResourceTableConfig — Configuration du tableau pour l'entité Resource
 *
 * @description
 * Configuration du tableau TanStack pour les ressources.
 * Utilise les informations des descriptors pour éviter la duplication (labels, icônes).
 * Les colonnes sont créées manuellement pour garder le contrôle total sur les configurations spéciales.
 */

import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";
import { getResourceFieldDescriptors } from "./resource-descriptors.js";
import { FIELD_LABELS, FIELD_ICONS } from '@/Utils/Entity/SharedConstants.js';

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

  // Créer la configuration de base depuis _tableConfig dans les descriptors
  const tableConfigData = descriptors._tableConfig || {};
  
  const tableConfig = new TableConfig({
    id: tableConfigData.id || "resources.index",
    entityType: tableConfigData.entityType || "resource",
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
        label: "ID",
        type: "text",
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
      label: descriptors.image?.general?.label || "Image",
      type: "image",
      icon: descriptors.image?.general?.icon || "fa-solid fa-image",
    })
      .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
      .withOrder(3)
  );

  // Colonne Name (principale)
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "name",
      label: descriptors.name?.general?.label || "Nom",
      type: "route",
      icon: descriptors.name?.general?.icon || "fa-solid fa-font",
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
      label: FIELD_LABELS.level, // Utilise la traduction depuis SharedConstants
      type: "badge",
      icon: FIELD_ICONS.level, // Utilise l'icône depuis SharedConstants
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

  // Colonne Resource Type
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "resource_type",
      label: descriptors.resource_type?.general?.label || "Type",
      type: "badge",
      icon: descriptors.resource_type?.general?.icon || "fa-solid fa-tag",
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
  );

  // Colonne Rarity avec filtre complexe
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "rarity",
      label: FIELD_LABELS.rarity, // Utilise la traduction depuis SharedConstants
      type: "badge",
      icon: FIELD_ICONS.rarity, // Utilise l'icône depuis SharedConstants
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
      .withDefaultVisible({ xs: false, sm: true, md: true, lg: true, xl: true })
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
      label: descriptors.price?.general?.label || "Prix",
      type: "text",
      icon: descriptors.price?.general?.icon || "fa-solid fa-coins",
    })
      .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
      .withOrder(8)
      .withSort(true)
  );

  // Colonne Weight
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "weight",
      label: descriptors.weight?.general?.label || "Poids",
      type: "text",
      icon: descriptors.weight?.general?.icon || "fa-solid fa-weight-hanging",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
      .withOrder(9)
      .withSort(true)
  );

  // Colonne Usable
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "usable",
      label: descriptors.usable?.general?.label || "Utilisable",
      type: "badge",
      icon: descriptors.usable?.general?.icon || "fa-solid fa-check",
    })
      .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
      .withOrder(10)
      .withSort(true)
      .withFilter({ id: "usable", type: "boolean" })
      .withFormat({
        xs: { mode: "icon" },
        sm: { mode: "icon" },
        md: { mode: "icon" },
        lg: { mode: "icon" },
        xl: { mode: "icon" },
      })
  );

  // Colonne Is Visible
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "is_visible",
      label: descriptors.is_visible?.general?.label || "Visibilité",
      type: "badge",
      icon: descriptors.is_visible?.general?.icon || "fa-solid fa-eye",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: true, xl: true })
      .withOrder(11)
      .withSort(true)
      .withFilter({ id: "is_visible", type: "multi" })
  );

  // Colonne Dofus Version
  tableConfig.addColumn(
    new TableColumnConfig({
      key: "dofus_version",
      label: descriptors.dofus_version?.general?.label || "Version Dofus",
      type: "text",
      icon: descriptors.dofus_version?.general?.icon || "fa-solid fa-code-branch",
    })
      .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
      .withOrder(12)
      .withSort(true)
  );

  // Colonnes conditionnelles selon permissions
  if (canUpdateAny) {
    tableConfig
      .addColumn(
        new TableColumnConfig({
          key: "auto_update",
          label: descriptors.auto_update?.general?.label || "Auto-update",
          type: "badge",
          icon: descriptors.auto_update?.general?.icon || "fa-solid fa-arrows-rotate",
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
          label: descriptors.dofusdb_id?.general?.label || "DofusDB",
          type: "routeExternal",
          icon: descriptors.dofusdb_id?.general?.icon || "fa-solid fa-arrow-up-right-from-square",
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
          label: descriptors.created_by?.general?.label || "Créé par",
          type: "text",
          icon: descriptors.created_by?.general?.icon || "fa-solid fa-user",
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
          label: descriptors.created_at?.general?.label || "Créé le",
          type: "date",
          icon: descriptors.created_at?.general?.icon || "fa-solid fa-calendar",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(16)
          .withSort(true)
      )
      .addColumn(
        new TableColumnConfig({
          key: "updated_at",
          label: descriptors.updated_at?.general?.label || "Modifié le",
          type: "date",
          icon: descriptors.updated_at?.general?.icon || "fa-solid fa-clock",
        })
          .withPermission("createAny")
          .withDefaultVisible({ xs: false, sm: false, md: false, lg: false, xl: false })
          .withOrder(17)
          .withSort(true)
      );
  }

  return tableConfig;
}

/**
 * Exemples d'utilisation de ViewConfig et QuickEditViewConfig
 *
 * @description
 * Ce fichier montre comment utiliser les classes ViewConfig pour créer
 * des configurations de vues pour une entité.
 */

import { ViewConfig, QuickEditViewConfig } from "./ViewConfig.js";

// ============================================
// EXEMPLE 1 : Vue compacte
// ============================================

export function createCompactView() {
  return new ViewConfig({
    name: "compact",
    label: "Vue compacte",
  })
    .withFields(["rarity", "resource_type", "level", "usable", "price"])
    .withOrder(["rarity", "resource_type", "level", "usable", "price"])
    .withActions({
      available: ["view", "edit", "quick-edit"],
      permission: "view",
      display: "icon-only",
    })
    .withLayout({
      columns: 2,
      spacing: "compact",
    })
    .build();
}

// ============================================
// EXEMPLE 2 : Vue minimale
// ============================================

export function createMinimalView() {
  return new ViewConfig({
    name: "minimal",
    label: "Vue minimale",
  })
    .withFields(["rarity", "level", "usable"])
    .withActions({
      available: ["view", "quick-edit"],
      permission: "view",
      display: "icon-only",
    })
    .build();
}

// ============================================
// EXEMPLE 3 : Vue large (étendue)
// ============================================

export function createLargeView() {
  return new ViewConfig({
    name: "large",
    label: "Vue étendue",
  })
    .withFields([
      "rarity",
      "resource_type",
      "level",
      "usable",
      "price",
      "weight",
      "dofus_version",
      "is_visible",
      "auto_update",
      "description",
      "image",
    ])
    .withActions({
      available: ["view", "edit", "quick-edit", "delete", "copy-link", "download-pdf"],
      permission: "view",
      display: "icon-text",
    })
    .withLayout({
      columns: 3,
      spacing: "normal",
    })
    .build();
}

// ============================================
// EXEMPLE 4 : Vue QuickEdit (panneau latéral)
// ============================================

export function createQuickEditPanelView() {
  return new QuickEditViewConfig({
    name: "quickEdit",
    label: "Édition rapide",
  })
    .withFields([
      "resource_type_id",
      "rarity",
      "level",
      "usable",
      "auto_update",
      "is_visible",
      "price",
      "weight",
      "dofus_version",
      "description",
      "image",
      "dofusdb_id",
    ])
    .withLayoutType("panel", {
      position: "right",
      width: "md",
    })
    .build();
}

// ============================================
// EXEMPLE 5 : Vue QuickEdit (modal)
// ============================================

export function createQuickEditModalView() {
  return new QuickEditViewConfig({
    name: "quickEdit",
    label: "Édition rapide",
  })
    .withFields([
      "resource_type_id",
      "rarity",
      "level",
      "usable",
      "is_visible",
      "price",
    ])
    .withLayoutType("modal", {
      size: "lg",
    })
    .build();
}

// ============================================
// EXEMPLE 6 : Utilisation dans un descriptor
// ============================================

export function exampleInDescriptor() {
  // Dans ResourceDescriptor.js
  return {
    viewCompact: createCompactView(),
    viewMinimal: createMinimalView(),
    viewLarge: createLargeView(),
    viewQuickEdit: createQuickEditPanelView(),
  };
}

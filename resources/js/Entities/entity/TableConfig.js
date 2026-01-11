/**
 * TableConfig — Classe pour configurer un tableau d'entités
 *
 * @description
 * Cette classe permet de configurer un tableau avec :
 * - Configuration quickEdit (permission, activation)
 * - Configuration actions (menu, permissions, visibilité responsive)
 * - Liste des colonnes
 * - Features du tableau (search, filters, pagination, etc.)
 *
 * @example
 * const tableConfig = new TableConfig({
 *   id: "resources.index",
 *   entityType: "resource"
 * })
 *   .withQuickEdit({ enabled: true, permission: "updateAny" })
 *   .withActions({ enabled: true, permission: "view", available: ["view", "edit", "delete"] })
 *   .addColumn(new TableColumnConfig({ key: "name", label: "Nom", type: "route" }))
 *   .build();
 */

import { TableColumnConfig } from "./TableColumnConfig.js";
import { getCurrentScreenSize } from "./EntityDescriptorHelpers.js";

/**
 * Classe TableConfig
 */
export class TableConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.id - ID unique du tableau (obligatoire)
   * @param {string} base.entityType - Type d'entité (obligatoire)
   */
  constructor(base = {}) {
    if (!base.id) throw new Error("TableConfig: 'id' est obligatoire");
    if (!base.entityType) throw new Error("TableConfig: 'entityType' est obligatoire");

    this.id = base.id;
    this.entityType = base.entityType;

    // Configuration quickEdit
    this.quickEdit = {
      enabled: false,
      permission: null,
    };

    // Configuration actions
    this.actions = {
      enabled: false,
      permission: null,
      available: [],
      defaultVisible: {
        xs: false,
        sm: true,
        md: true,
        lg: true,
        xl: true,
      },
    };

    // Colonnes
    this.columns = [];

    // Features du tableau
    this.features = {
      search: { enabled: true, placeholder: `Rechercher…`, debounceMs: 200 },
      filters: { enabled: true },
      pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
      selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
      columnVisibility: { enabled: true, persist: true },
      export: { csv: false, filename: null },
    };

    // UI
    this.ui = {
      skeletonRows: 10,
    };
  }

  /**
   * Configure le mode quickEdit.
   *
   * @param {Object} config - Configuration quickEdit
   * @param {boolean} config.enabled - Activer quickEdit
   * @param {string} config.permission - Permission requise
   * @returns {TableConfig} Instance pour chaînage
   */
  withQuickEdit(config) {
    this.quickEdit = {
      enabled: Boolean(config.enabled),
      permission: config.permission || null,
    };
    return this;
  }

  /**
   * Configure le menu actions.
   *
   * @param {Object} config - Configuration actions
   * @param {boolean} config.enabled - Activer le menu
   * @param {string} config.permission - Permission requise
   * @param {string[]} config.available - Actions disponibles
   * @param {Object} [config.defaultVisible] - Visibilité par taille d'écran
   * @returns {TableConfig} Instance pour chaînage
   */
  withActions(config) {
    this.actions = {
      enabled: Boolean(config.enabled),
      permission: config.permission || null,
      available: Array.isArray(config.available) ? config.available : [],
      defaultVisible: {
        ...this.actions.defaultVisible,
        ...(config.defaultVisible || {}),
      },
    };
    return this;
  }

  /**
   * Ajoute une colonne au tableau.
   *
   * @param {TableColumnConfig|Object} column - Colonne à ajouter
   * @returns {TableConfig} Instance pour chaînage
   */
  addColumn(column) {
    if (column instanceof TableColumnConfig) {
      this.columns.push(column);
    } else {
      // Si c'est un objet, créer une instance de TableColumnConfig
      const col = new TableColumnConfig(column);
      this.columns.push(col);
    }
    return this;
  }

  /**
   * Ajoute plusieurs colonnes.
   *
   * @param {Array<TableColumnConfig|Object>} columns - Colonnes à ajouter
   * @returns {TableConfig} Instance pour chaînage
   */
  addColumns(columns) {
    columns.forEach((col) => this.addColumn(col));
    return this;
  }

  /**
   * Configure les features du tableau.
   *
   * @param {Object} features - Configuration des features
   * @returns {TableConfig} Instance pour chaînage
   */
  withFeatures(features) {
    this.features = { ...this.features, ...features };
    return this;
  }

  /**
   * Configure l'UI du tableau.
   *
   * @param {Object} ui - Configuration UI
   * @returns {TableConfig} Instance pour chaînage
   */
  withUI(ui) {
    this.ui = { ...this.ui, ...ui };
    return this;
  }

  /**
   * Obtient les colonnes visibles selon la taille d'écran actuelle.
   *
   * @param {string} [size="auto"] - Taille d'écran
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {Array<Object>} Colonnes visibles
   */
  getVisibleColumns(size = "auto", ctx = {}) {
    const actualSize = size === "auto" ? getCurrentScreenSize() : size;
    const can = ctx?.capabilities || ctx?.meta?.capabilities || {};

    return this.columns
      .map((col) => {
        const config = col instanceof TableColumnConfig ? col.build() : col;
        return { config, column: col instanceof TableColumnConfig ? col : null };
      })
      .filter(({ config, column }) => {
        // Vérifier la permission
        if (config.permissions?.ability) {
          if (!can[config.permissions.ability]) return false;
        }

        // Vérifier la visibilité selon la taille
        if (column) {
          return column.isVisible(actualSize);
        }
        return config.defaultVisible?.[actualSize] ?? true;
      })
      .sort((a, b) => {
        // Trier par ordre (si défini)
        const orderA = a.column?.order ?? a.config.order ?? 999;
        const orderB = b.column?.order ?? b.config.order ?? 999;
        return orderA - orderB;
      })
      .map(({ config }) => config);
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {Object} Configuration du tableau
   */
  build(ctx = {}) {
    const can = ctx?.capabilities || ctx?.meta?.capabilities || {};

    // Vérifier si quickEdit est activé et si la permission est présente
    const quickEditEnabled = this.quickEdit.enabled && (!this.quickEdit.permission || can[this.quickEdit.permission]);

    // Vérifier si actions est activé et si la permission est présente
    const actionsEnabled = this.actions.enabled && (!this.actions.permission || can[this.actions.permission]);

    // Construire les colonnes
    const columns = this.columns.map((col) => (col instanceof TableColumnConfig ? col.build() : col));

    // Trier les colonnes par ordre
    columns.sort((a, b) => {
      const orderA = a.order ?? 999;
      const orderB = b.order ?? 999;
      return orderA - orderB;
    });

    return {
      id: this.id,
      ui: { ...this.ui },
      features: {
        ...this.features,
        selection: {
          ...this.features.selection,
          enabled: this.features.selection.enabled && quickEditEnabled, // Désactiver si pas de permission quickEdit
        },
      },
      columns,
      // Métadonnées pour le système
      _metadata: {
        entityType: this.entityType,
        quickEdit: {
          enabled: quickEditEnabled,
          permission: this.quickEdit.permission,
        },
        actions: {
          enabled: actionsEnabled,
          permission: this.actions.permission,
          available: this.actions.available,
          defaultVisible: this.actions.defaultVisible,
        },
      },
    };
  }
}

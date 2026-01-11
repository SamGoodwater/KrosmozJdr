/**
 * ViewConfig — Classe de base pour configurer une vue d'entité
 *
 * @description
 * Cette classe permet de configurer une vue d'entité (compact, minimal, large, quickEdit) avec :
 * - Liste des champs à afficher
 * - Ordre d'affichage
 * - Actions disponibles
 * - Configuration de layout
 *
 * @example
 * const viewCompact = new ViewConfig({
 *   name: "compact",
 *   label: "Vue compacte"
 * })
 *   .withFields(["rarity", "resource_type", "level"])
 *   .withActions({ available: ["view", "edit"], permission: "view" })
 *   .build();
 */

/**
 * Classe ViewConfig
 */
export class ViewConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.name - Nom de la vue (obligatoire)
   * @param {string} base.label - Libellé de la vue (obligatoire)
   */
  constructor(base = {}) {
    if (!base.name) throw new Error("ViewConfig: 'name' est obligatoire");
    if (!base.label) throw new Error("ViewConfig: 'label' est obligatoire");

    this.name = base.name;
    this.label = base.label;

    // Champs à afficher
    this.fields = [];
    this.order = [];

    // Actions disponibles
    this.actions = {
      available: [],
      permission: null,
      display: "icon-only", // icon-only, icon-text, text-only
    };

    // Configuration de layout (optionnel)
    this.layout = null;
  }

  /**
   * Configure les champs à afficher.
   *
   * @param {string[]} fields - Liste des clés de champs
   * @returns {ViewConfig} Instance pour chaînage
   */
  withFields(fields) {
    this.fields = Array.isArray(fields) ? fields : [];
    this.order = [...this.fields]; // Par défaut, l'ordre suit la liste
    return this;
  }

  /**
   * Configure l'ordre d'affichage des champs.
   *
   * @param {string[]} order - Ordre des champs (peut être différent de fields)
   * @returns {ViewConfig} Instance pour chaînage
   */
  withOrder(order) {
    this.order = Array.isArray(order) ? order : this.fields;
    return this;
  }

  /**
   * Configure les actions disponibles.
   *
   * @param {Object} actions - Configuration des actions
   * @param {string[]} actions.available - Actions disponibles
   * @param {string} [actions.permission] - Permission requise
   * @param {string} [actions.display] - Comment afficher (icon-only, icon-text, text-only)
   * @returns {ViewConfig} Instance pour chaînage
   */
  withActions(actions) {
    this.actions = {
      available: Array.isArray(actions.available) ? actions.available : [],
      permission: actions.permission || null,
      display: actions.display || "icon-only",
    };
    return this;
  }

  /**
   * Configure le layout de la vue.
   *
   * @param {Object} layout - Configuration du layout
   * @param {number} [layout.columns] - Nombre de colonnes
   * @param {string} [layout.spacing] - Espacement (compact, normal, spacious)
   * @returns {ViewConfig} Instance pour chaînage
   */
  withLayout(layout) {
    this.layout = { ...layout };
    return this;
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @param {Object} [ctx] - Contexte (permissions, etc.)
   * @returns {Object} Configuration de la vue
   */
  build(ctx = {}) {
    const can = ctx?.capabilities || ctx?.meta?.capabilities || {};

    // Vérifier si les actions sont disponibles selon les permissions
    let availableActions = this.actions.available;
    if (this.actions.permission && !can[this.actions.permission]) {
      availableActions = [];
    }

    return {
      name: this.name,
      label: this.label,
      fields: [...this.fields],
      order: [...this.order],
      actions: {
        available: availableActions,
        permission: this.actions.permission,
        display: this.actions.display,
      },
      layout: this.layout ? { ...this.layout } : null,
    };
  }
}

/**
 * QuickEditViewConfig — Classe spécialisée pour la vue QuickEdit
 *
 * @description
 * Vue spéciale pour l'édition rapide en masse avec :
 * - Champs éditables
 * - Actions spécifiques (Enregistrer, Annuler, Reset)
 * - Layout spécifique (panneau latéral ou modal)
 *
 * @example
 * const quickEditView = new QuickEditViewConfig({
 *   name: "quickEdit",
 *   label: "Édition rapide"
 * })
 *   .withFields(["rarity", "level", "usable"])
 *   .withLayout({ type: "panel", position: "right" })
 *   .build();
 */
export class QuickEditViewConfig extends ViewConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.name - Nom de la vue (défaut: "quickEdit")
   * @param {string} base.label - Libellé de la vue (défaut: "Édition rapide")
   */
  constructor(base = {}) {
    super({
      name: base.name || "quickEdit",
      label: base.label || "Édition rapide",
    });

    // Actions spécifiques à QuickEdit
    this.actions = {
      available: ["save", "cancel", "reset"],
      permission: "updateAny",
      display: "text-only", // QuickEdit utilise généralement des boutons texte
    };

    // Layout spécifique (panneau latéral par défaut)
    this.layout = {
      type: "panel", // panel, modal
      position: "right", // left, right (pour panel)
      width: "md", // xs, sm, md, lg, xl
    };
  }

  /**
   * Configure le type de layout (panel ou modal).
   *
   * @param {string} type - Type de layout (panel, modal)
   * @param {Object} [options] - Options supplémentaires
   * @returns {QuickEditViewConfig} Instance pour chaînage
   */
  withLayoutType(type, options = {}) {
    this.layout = {
      type,
      ...(type === "panel" ? { position: options.position || "right", width: options.width || "md" } : {}),
      ...(type === "modal" ? { size: options.size || "lg" } : {}),
    };
    return this;
  }
}

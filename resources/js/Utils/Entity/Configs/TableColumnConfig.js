/**
 * TableColumnConfig — Classe pour configurer une colonne de tableau
 *
 * @description
 * Cette classe permet de configurer une colonne de tableau avec :
 * - Permissions
 * - Affichage responsive (xs, sm, md, lg, xl)
 * - Formatage conditionnel
 * - Configuration formulaire (si type = "form")
 *
 * @example
 * const column = new TableColumnConfig({
 *   key: "name",
 *   label: "Nom",
 *   type: "route",
 *   icon: "fa-solid fa-font"
 * })
 *   .withPermission("view")
 *   .withDefaultVisible({ xs: false, sm: false, md: true, lg: true, xl: true })
 *   .withFormat({ xs: { mode: "truncate", maxLength: 20 }, md: { mode: "full" } })
 *   .build();
 */

import { CELL_TYPES, SCREEN_SIZES } from "../Constants.js";
import { getCurrentScreenSize, subtractSize, addSize } from "../Helpers.js";

/**
 * Classe TableColumnConfig
 */
export class TableColumnConfig {
  /**
   * @param {Object} base - Propriétés de base de la colonne
   * @param {string} base.key - Clé unique (obligatoire)
   * @param {string} base.label - Libellé (obligatoire)
   * @param {string} base.type - Type de cellule (obligatoire)
   * @param {string} [base.icon] - Icône FontAwesome
   * @param {string} [base.tooltip] - Tooltip/helper
   */
  constructor(base = {}) {
    // Validation des champs obligatoires
    if (!base.key) throw new Error("TableColumnConfig: 'key' est obligatoire");
    if (!base.label) throw new Error("TableColumnConfig: 'label' est obligatoire");
    if (!base.type) throw new Error("TableColumnConfig: 'type' est obligatoire");

    if (!CELL_TYPES.includes(base.type)) {
      throw new Error(`TableColumnConfig: type invalide '${base.type}'. Types valides: ${CELL_TYPES.join(", ")}`);
    }

    // Propriétés de base
    this.key = base.key;
    this.label = base.label;
    this.type = base.type;
    this.icon = base.icon || null;
    this.tooltip = base.tooltip || null;

    // Permissions
    this.permission = null;

    // Affichage responsive
    this.defaultVisible = {
      xs: true,
      sm: true,
      md: true,
      lg: true,
      xl: true,
    };

    // Ordre et organisation
    this.order = 0;
    this.isMain = false;
    this.hideable = true;
    this.group = null;

    // Formatage responsive
    this.format = {};

    // Tri, recherche, filtres
    this.sort = { enabled: false };
    this.search = { enabled: false };
    this.filter = null;

    // Configuration formulaire (si type = "form")
    this.form = null;
  }

  /**
   * Configure la permission requise pour voir la colonne.
   *
   * @param {string} permission - Permission requise
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withPermission(permission) {
    this.permission = permission;
    return this;
  }

  /**
   * Configure la visibilité par défaut selon la taille d'écran.
   *
   * @param {Object} visible - Visibilité par taille (xs, sm, md, lg, xl)
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withDefaultVisible(visible) {
    this.defaultVisible = { ...this.defaultVisible, ...visible };
    return this;
  }

  /**
   * Configure l'ordre d'affichage dans le header.
   *
   * @param {number} order - Ordre (plus petit = plus à gauche)
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withOrder(order) {
    this.order = order;
    return this;
  }

  /**
   * Marque la colonne comme principale (non masquable).
   *
   * @param {boolean} [isMain=true] - Est-ce la colonne principale ?
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  asMain(isMain = true) {
    this.isMain = isMain;
    this.hideable = !isMain;
    return this;
  }

  /**
   * Configure le groupe de colonnes.
   *
   * @param {string} group - Nom du groupe
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withGroup(group) {
    this.group = group;
    return this;
  }

  /**
   * Configure le formatage responsive.
   *
   * @param {Object} format - Formatage par taille (xs, sm, md, lg, xl)
   * @param {string} format[xs|sm|md|lg|xl].mode - Mode d'affichage (truncate, full, etc.)
   * @param {number} [format[xs|sm|md|lg|xl].maxLength] - Longueur max pour truncate
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withFormat(format) {
    this.format = { ...this.format, ...format };
    return this;
  }

  /**
   * Configure le tri.
   *
   * @param {boolean} [enabled=true] - Activer le tri
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withSort(enabled = true) {
    this.sort = { enabled: Boolean(enabled) };
    return this;
  }

  /**
   * Configure la recherche.
   *
   * @param {boolean} [enabled=true] - Activer la recherche
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withSearch(enabled = true) {
    this.search = { enabled: Boolean(enabled) };
    return this;
  }

  /**
   * Configure le filtre.
   *
   * @param {Object} filter - Configuration du filtre
   * @param {string} filter.id - ID du filtre
   * @param {string} filter.type - Type de filtre (text, select, multi, boolean)
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withFilter(filter) {
    this.filter = filter;
    return this;
  }

  /**
   * Configure le formulaire (si type = "form").
   *
   * @param {Object} form - Configuration du formulaire
   * @returns {TableColumnConfig} Instance pour chaînage
   */
  withForm(form) {
    if (this.type !== "form") {
      throw new Error(`TableColumnConfig.withForm: ne peut être utilisé que si type = "form" (actuel: ${this.type})`);
    }
    this.form = form;
    return this;
  }

  /**
   * Obtient la visibilité actuelle selon la taille d'écran.
   *
   * @param {string} [size="auto"] - Taille d'écran (xs, sm, md, lg, xl, auto)
   * @returns {boolean} Visible ou non
   */
  isVisible(size = "auto") {
    const actualSize = size === "auto" ? getCurrentScreenSize() : size;
    return this.defaultVisible[actualSize] ?? true;
  }

  /**
   * Obtient le formatage pour la taille d'écran actuelle.
   *
   * @param {string} [size="auto"] - Taille d'écran (xs, sm, md, lg, xl, auto)
   * @returns {Object} Configuration de formatage
   */
  getFormat(size = "auto") {
    const actualSize = size === "auto" ? getCurrentScreenSize() : size;
    return this.format[actualSize] || this.format.md || this.format.lg || { mode: "full" };
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @returns {Object} Configuration de colonne
   */
  build() {
    const config = {
      id: this.key,
      label: this.label,
      cell: { type: this.type },
    };

    if (this.icon) config.icon = this.icon;
    if (this.tooltip) config.tooltip = this.tooltip;
    if (this.permission) config.permissions = { ability: this.permission };
    if (this.isMain) config.isMain = true;
    if (this.hideable !== undefined) config.hideable = this.hideable;
    if (this.group) config.group = this.group;

    // Visibilité par défaut (pour la logique responsive)
    config.defaultVisible = { ...this.defaultVisible };

    // Formatage responsive
    if (Object.keys(this.format).length > 0) {
      config.format = { ...this.format };
    }

    // Tri, recherche, filtres
    if (this.sort.enabled) config.sort = { ...this.sort };
    if (this.search.enabled) config.search = { ...this.search };
    if (this.filter) config.filter = { ...this.filter };

    // Configuration formulaire
    if (this.form) {
      config.form = { ...this.form };
    }

    return config;
  }
}

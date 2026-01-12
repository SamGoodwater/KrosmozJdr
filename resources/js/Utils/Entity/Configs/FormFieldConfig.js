/**
 * FormFieldConfig — Classe pour configurer un champ de formulaire
 *
 * @description
 * Cette classe permet de configurer un champ de formulaire avec :
 * - Type de champ
 * - Validation
 * - Options (pour select)
 * - Groupes
 *
 * @example
 * const field = new FormFieldConfig({
 *   key: "name",
 *   type: "text",
 *   label: "Nom"
 * })
 *   .withGroup("Informations générales")
 *   .withRequired(true)
 *   .build();
 */

import { FORM_TYPES } from "../Constants.js";

/**
 * Classe FormFieldConfig
 */
export class FormFieldConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.key - Clé unique (obligatoire)
   * @param {string} base.type - Type de champ (obligatoire)
   * @param {string} [base.label] - Libellé (optionnel, utilise key si non fourni)
   */
  constructor(base = {}) {
    if (!base.key) throw new Error("FormFieldConfig: 'key' est obligatoire");
    if (!base.type) throw new Error("FormFieldConfig: 'type' est obligatoire");

    if (!FORM_TYPES.includes(base.type)) {
      throw new Error(`FormFieldConfig: type invalide '${base.type}'. Types valides: ${FORM_TYPES.join(", ")}`);
    }

    this.key = base.key;
    this.type = base.type;
    this.label = base.label || base.key;

    // Validation
    this.required = false;
    this.showInCompact = true;

    // Organisation
    this.group = null;

    // Aide et informations
    this.help = null;
    this.tooltip = null;
    this.placeholder = null;

    // Valeur par défaut
    this.defaultValue = null;

    // Options (pour select)
    this.options = null;
  }

  /**
   * Configure le libellé du champ.
   *
   * @param {string} label - Libellé
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withLabel(label) {
    this.label = label;
    return this;
  }

  /**
   * Configure le groupe de champs.
   *
   * @param {string} group - Nom du groupe
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withGroup(group) {
    this.group = group;
    return this;
  }

  /**
   * Marque le champ comme obligatoire.
   *
   * @param {boolean} [required=true] - Champ obligatoire ?
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withRequired(required = true) {
    this.required = Boolean(required);
    return this;
  }

  /**
   * Configure l'affichage en vue compacte.
   *
   * @param {boolean} [showInCompact=true] - Afficher en vue compacte ?
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withShowInCompact(showInCompact = true) {
    this.showInCompact = Boolean(showInCompact);
    return this;
  }

  /**
   * Configure le texte d'aide.
   *
   * @param {string} help - Texte d'aide
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withHelp(help) {
    this.help = help;
    return this;
  }

  /**
   * Configure le tooltip.
   *
   * @param {string} tooltip - Tooltip
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withTooltip(tooltip) {
    this.tooltip = tooltip;
    return this;
  }

  /**
   * Configure le placeholder.
   *
   * @param {string} placeholder - Placeholder
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withPlaceholder(placeholder) {
    this.placeholder = placeholder;
    return this;
  }

  /**
   * Configure la valeur par défaut.
   *
   * @param {any} defaultValue - Valeur par défaut
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withDefaultValue(defaultValue) {
    this.defaultValue = defaultValue;
    return this;
  }

  /**
   * Configure les options (pour select).
   *
   * @param {Array|Function} options - Options ou fonction qui retourne les options
   * @returns {FormFieldConfig} Instance pour chaînage
   */
  withOptions(options) {
    this.options = options;
    return this;
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @returns {Object} Configuration du champ de formulaire
   */
  build() {
    const config = {
      type: this.type,
      required: this.required,
      showInCompact: this.showInCompact,
    };

    if (this.label !== this.key) config.label = this.label;
    if (this.group) config.group = this.group;
    if (this.help) config.help = this.help;
    if (this.tooltip) config.tooltip = this.tooltip;
    if (this.placeholder) config.placeholder = this.placeholder;
    if (this.defaultValue !== null) config.defaultValue = this.defaultValue;
    if (this.options) config.options = this.options;

    return config;
  }
}

/**
 * BulkConfig — Classe pour configurer l'édition en masse (bulk)
 *
 * @description
 * Cette classe permet de configurer l'édition en masse avec :
 * - Liste des champs bulk-editables
 * - Fonctions de transformation
 * - Agrégation des valeurs
 *
 * @example
 * const bulkConfig = new BulkConfig({
 *   entityType: "resource"
 * })
 *   .addField("rarity", { enabled: true, nullable: true, build: (v) => Number(v) })
 *   .addField("level", { enabled: true, nullable: true })
 *   .withQuickEditFields(["rarity", "level", "usable"])
 *   .build();
 */

/**
 * Classe BulkConfig
 */
export class BulkConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.entityType - Type d'entité (obligatoire)
   */
  constructor(base = {}) {
    if (!base.entityType) throw new Error("BulkConfig: 'entityType' est obligatoire");

    this.entityType = base.entityType;

    // Champs bulk-editables
    this.fields = {};

    // Champs affichés dans quickEdit
    this.quickEditFields = [];
  }

  /**
   * Ajoute un champ bulk-editable.
   *
   * @param {string} key - Clé du champ
   * @param {Object} config - Configuration bulk
   * @param {boolean} [config.enabled=true] - Activer le bulk
   * @param {boolean} [config.nullable=true] - Permettre null/vide
   * @param {Function} [config.build] - Fonction de transformation
   * @param {string} [config.label] - Libellé (optionnel)
   * @param {string} [config.aggregate] - Comment agréger (common, different, mixed)
   * @returns {BulkConfig} Instance pour chaînage
   */
  addField(key, config = {}) {
    this.fields[key] = {
      enabled: Boolean(config.enabled !== false),
      nullable: Boolean(config.nullable !== false),
      build: config.build || null,
      label: config.label || null,
      aggregate: config.aggregate || "common",
    };
    return this;
  }

  /**
   * Ajoute plusieurs champs bulk-editables.
   *
   * @param {Object} fields - Objet avec les configurations par clé
   * @returns {BulkConfig} Instance pour chaînage
   */
  addFields(fields) {
    for (const [key, config] of Object.entries(fields)) {
      this.addField(key, config);
    }
    return this;
  }

  /**
   * Configure la liste des champs affichés dans quickEdit.
   *
   * @param {string[]} fields - Liste des clés de champs
   * @returns {BulkConfig} Instance pour chaînage
   */
  withQuickEditFields(fields) {
    this.quickEditFields = Array.isArray(fields) ? fields : [];
    return this;
  }

  /**
   * Obtient la configuration d'un champ bulk.
   *
   * @param {string} key - Clé du champ
   * @returns {Object|null} Configuration bulk ou null
   */
  getField(key) {
    return this.fields[key] || null;
  }

  /**
   * Vérifie si un champ est bulk-editable.
   *
   * @param {string} key - Clé du champ
   * @returns {boolean} Est bulk-editable ?
   */
  isBulkEditable(key) {
    return this.fields[key]?.enabled === true;
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @returns {Object} Configuration bulk
   */
  build() {
    return {
      fields: { ...this.fields },
      quickEditFields: [...this.quickEditFields],
    };
  }
}

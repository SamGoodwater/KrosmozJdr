/**
 * FormConfig — Classe pour configurer les formulaires d'une entité
 *
 * @description
 * Cette classe permet de configurer les formulaires avec :
 * - Liste des champs éditables
 * - Groupes de champs
 * - Validation globale
 *
 * @example
 * const formConfig = new FormConfig({
 *   entityType: "resource"
 * })
 *   .addField(new FormFieldConfig({ key: "name", type: "text" }).withRequired(true))
 *   .addField(new FormFieldConfig({ key: "rarity", type: "select" }).withOptions([...]))
 *   .addGroup({ name: "Informations générales", order: 1 })
 *   .build();
 */

import { FormFieldConfig } from "./FormFieldConfig.js";
import { RECOMMENDED_GROUPS } from "./EntityDescriptorConstants.js";

/**
 * Classe FormConfig
 */
export class FormConfig {
  /**
   * @param {Object} base - Propriétés de base
   * @param {string} base.entityType - Type d'entité (obligatoire)
   */
  constructor(base = {}) {
    if (!base.entityType) throw new Error("FormConfig: 'entityType' est obligatoire");

    this.entityType = base.entityType;

    // Champs de formulaire
    this.fields = {};

    // Groupes de champs
    this.groups = {};
  }

  /**
   * Ajoute un champ au formulaire.
   *
   * @param {FormFieldConfig|Object} field - Champ à ajouter
   * @returns {FormConfig} Instance pour chaînage
   */
  addField(field) {
    let fieldConfig;
    if (field instanceof FormFieldConfig) {
      fieldConfig = field;
    } else {
      // Si c'est un objet, créer une instance de FormFieldConfig
      fieldConfig = new FormFieldConfig(field);
    }

    this.fields[fieldConfig.key] = fieldConfig.build();
    return this;
  }

  /**
   * Ajoute plusieurs champs.
   *
   * @param {Array<FormFieldConfig|Object>} fields - Champs à ajouter
   * @returns {FormConfig} Instance pour chaînage
   */
  addFields(fields) {
    fields.forEach((field) => this.addField(field));
    return this;
  }

  /**
   * Ajoute un groupe de champs.
   *
   * @param {Object} group - Configuration du groupe
   * @param {string} group.name - Nom du groupe
   * @param {number} [group.order] - Ordre d'affichage
   * @param {boolean} [group.collapsible] - Peut être replié
   * @returns {FormConfig} Instance pour chaînage
   */
  addGroup(group) {
    this.groups[group.name] = {
      label: group.label || group.name,
      order: group.order || 999,
      collapsible: group.collapsible || false,
    };
    return this;
  }

  /**
   * Ajoute plusieurs groupes.
   *
   * @param {Array<Object>} groups - Groupes à ajouter
   * @returns {FormConfig} Instance pour chaînage
   */
  addGroups(groups) {
    groups.forEach((group) => this.addGroup(group));
    return this;
  }

  /**
   * Obtient la configuration d'un champ.
   *
   * @param {string} key - Clé du champ
   * @returns {Object|null} Configuration du champ ou null
   */
  getField(key) {
    return this.fields[key] || null;
  }

  /**
   * Obtient tous les champs d'un groupe.
   *
   * @param {string} groupName - Nom du groupe
   * @returns {Array<Object>} Champs du groupe
   */
  getFieldsByGroup(groupName) {
    return Object.entries(this.fields)
      .filter(([_, config]) => config.group === groupName)
      .map(([key, config]) => ({ key, ...config }));
  }

  /**
   * Retourne l'objet de configuration final.
   *
   * @param {Object} [ctx] - Contexte (pour les options dynamiques)
   * @returns {Object} Configuration des formulaires
   */
  build(ctx = {}) {
    // Résoudre les options dynamiques si nécessaire
    const fields = {};
    for (const [key, config] of Object.entries(this.fields)) {
      let resolvedConfig = { ...config };

      // Si options est une fonction, l'appeler avec le contexte
      if (typeof resolvedConfig.options === "function") {
        resolvedConfig.options = resolvedConfig.options(ctx);
      }

      fields[key] = resolvedConfig;
    }

    return {
      fields,
      groups: { ...this.groups },
    };
  }
}

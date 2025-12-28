/**
 * Auto-discovery des templates de sections
 * 
 * @description
 * Scanne automatiquement les dossiers de templates et charge leurs configurations.
 * Permet d'ajouter de nouveaux templates sans modifier le code.
 * 
 * @example
 * import { availableTemplates, getTemplateByValue } from './templates';
 * const template = getTemplateByValue('text');
 */
import { computed } from 'vue';

// Import dynamique de toutes les configurations
const templateConfigs = import.meta.glob('./*/config.js', { eager: true });

/**
 * Liste de tous les templates disponibles avec leurs composants
 */
export const availableTemplates = computed(() => {
  return Object.entries(templateConfigs).map(([path, module]) => {
    const config = module.default;
    const templateName = path.split('/').slice(-2, -1)[0]; // Nom du dossier (ex: "text")
    
    // Convertir le nom du template en PascalCase pour les noms de fichiers
    // text -> Text, entity_table -> EntityTable (nom de template historique, pas lié à l'ancien composant EntityTable)
    const capitalizedName = templateName
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join('');
    
    return {
      ...config,
      // Charger les composants read/edit de manière dynamique
      readComponent: () => import(`./${templateName}/Section${capitalizedName}Read.vue`),
      editComponent: () => import(`./${templateName}/Section${capitalizedName}Edit.vue`),
    };
  });
});

/**
 * Retourne un template par sa valeur
 * 
 * @param {String} value - Valeur du template (ex: 'text', 'image')
 * @returns {Object|null} Configuration du template ou null
 */
export function getTemplateByValue(value) {
  return availableTemplates.value.find(t => t.value === value) || null;
}

/**
 * Retourne tous les templates pour les selects
 * 
 * @returns {Array} Liste des templates avec value, label, icon, description
 */
export function getTemplateOptions() {
  return availableTemplates.value
    .filter(template => !template?.hidden)
    .map(template => ({
    value: template.value,
    label: template.name,
    icon: template.icon,
    description: template.description,
    supportsAutoSave: template.supportsAutoSave,
  }));
}

/**
 * Retourne les valeurs par défaut (settings et data) pour un template
 * 
 * @param {String} value - Valeur du template (ex: 'text', 'image')
 * @returns {Object|null} { settings: Object, data: Object } ou null si template non trouvé
 * 
 * @example
 * const defaults = getTemplateDefaults('text');
 * // { settings: {}, data: { content: null } }
 */
export function getTemplateDefaults(value) {
  const template = getTemplateByValue(value);
  if (!template) {
    console.warn(`Template "${value}" non trouvé, retour des valeurs par défaut vides`);
    return {
      settings: {},
      data: {},
    };
  }
  
  return {
    settings: template.defaultSettings || {},
    data: template.defaultData || {},
  };
}

/**
 * Retourne les settings par défaut pour un template
 * 
 * @param {String} value - Valeur du template
 * @returns {Object} Settings par défaut (objet vide si non trouvé)
 */
export function getTemplateDefaultSettings(value) {
  const template = getTemplateByValue(value);
  return template?.defaultSettings || {};
}

/**
 * Retourne les data par défaut pour un template
 * 
 * @param {String} value - Valeur du template
 * @returns {Object} Data par défaut (objet vide si non trouvé)
 */
export function getTemplateDefaultData(value) {
  const template = getTemplateByValue(value);
  return template?.defaultData || {};
}

/**
 * Retourne toutes les propriétés d'un template (config complète)
 * 
 * @param {String} value - Valeur du template
 * @returns {Object|null} Configuration complète du template ou null
 * 
 * @example
 * const config = getTemplateConfig('text');
 * // { name, description, icon, value, supportsAutoSave, defaultSettings, defaultData, ... }
 */
export function getTemplateConfig(value) {
  return getTemplateByValue(value);
}


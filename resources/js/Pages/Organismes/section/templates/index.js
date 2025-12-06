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
    // text -> Text, entity_table -> EntityTable
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
  return availableTemplates.value.map(template => ({
    value: template.value,
    label: template.name,
    icon: template.icon,
    description: template.description,
    supportsAutoSave: template.supportsAutoSave,
  }));
}


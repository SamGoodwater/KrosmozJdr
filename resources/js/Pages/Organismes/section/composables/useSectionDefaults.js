/**
 * Composable pour obtenir les valeurs par défaut des sections selon leur template
 * 
 * @description
 * Fournit les valeurs par défaut pour settings et data selon le type de template.
 * Utilise les fichiers de configuration des templates (config.js) pour récupérer les defaults.
 * Utilisé lors de la création d'une nouvelle section.
 * 
 * **Source des données :**
 * - Les valeurs par défaut sont définies dans les fichiers `config.js` de chaque template
 * - Aucune référence hardcodée aux templates spécifiques dans ce fichier
 * 
 * @example
 * const { getDefaultSettings, getDefaultData, getDefaults } = useSectionDefaults();
 * const settings = getDefaultSettings('text');
 * const data = getDefaultData('text');
 * const allDefaults = getDefaults('text');
 */
import { 
  getTemplateDefaults, 
  getTemplateDefaultSettings, 
  getTemplateDefaultData 
} from '../templates';

export function useSectionDefaults() {
  /**
   * Retourne les settings par défaut selon le template
   * 
   * @param {String} template - Type de template (ex: 'text', 'image', 'gallery')
   * @returns {Object} Settings par défaut (depuis config.js du template)
   */
  const getDefaultSettings = (template) => {
    return getTemplateDefaultSettings(template);
  };

  /**
   * Retourne les data par défaut selon le template
   * 
   * @param {String} template - Type de template (ex: 'text', 'image', 'gallery')
   * @returns {Object} Data par défaut (depuis config.js du template)
   */
  const getDefaultData = (template) => {
    return getTemplateDefaultData(template);
  };

  /**
   * Retourne les settings et data par défaut selon le template
   * 
   * @param {String} template - Type de template
   * @returns {Object} { settings: Object, data: Object }
   */
  const getDefaults = (template) => {
    return getTemplateDefaults(template);
  };

  return {
    getDefaultSettings,
    getDefaultData,
    getDefaults
  };
}


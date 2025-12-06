/**
 * Composable pour gérer les templates de sections
 * 
 * @description
 * Fournit l'accès aux templates disponibles et leurs configurations.
 * Utilise le système d'auto-discovery pour charger les templates.
 * 
 * @example
 * const { availableTemplates, getTemplateConfig, getTemplateComponent } = useSectionTemplates();
 * const template = getTemplateConfig('text');
 * const component = await getTemplateComponent('text', 'read');
 */
import { availableTemplates, getTemplateByValue } from '../templates';

/**
 * Composable pour gérer les templates de sections
 * 
 * @returns {Object} { availableTemplates, getTemplateConfig, getTemplateComponent }
 */
export function useSectionTemplates() {
  /**
   * Retourne la configuration d'un template par sa valeur
   * 
   * @param {String} templateValue - Valeur du template (ex: 'text')
   * @returns {Object|null} Configuration du template
   */
  const getTemplateConfig = (templateValue) => {
    return getTemplateByValue(templateValue);
  };
  
  /**
   * Charge le composant d'un template selon le mode
   * 
   * @param {String} templateValue - Valeur du template
   * @param {String} mode - Mode 'read' ou 'edit'
   * @returns {Promise<Component>} Composant Vue
   */
  const getTemplateComponent = async (templateValue, mode = 'read') => {
    const config = getTemplateByValue(templateValue);
    if (!config) {
      console.warn(`Template "${templateValue}" non trouvé`);
      return null;
    }
    
    try {
      if (mode === 'read') {
        const module = await config.readComponent();
        return module.default || module;
      } else {
        const module = await config.editComponent();
        return module.default || module;
      }
    } catch (error) {
      console.error(`Erreur lors du chargement du template "${templateValue}" en mode "${mode}":`, error);
      return null;
    }
  };
  
  return {
    availableTemplates,
    getTemplateConfig,
    getTemplateComponent,
  };
}


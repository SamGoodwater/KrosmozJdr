/**
 * Composable pour gérer les paramètres des sections
 * 
 * @description
 * Fournit des helpers pour générer les champs de formulaire depuis les paramètres
 * définis dans les configs des templates.
 * 
 * @example
 * const { getParameterFields, getCommonFields } = useSectionParameters();
 * const fields = getParameterFields(templateConfig.parameters);
 */
import { computed } from 'vue';
import { getTemplateConfig } from '../templates';

/**
 * Composable pour gérer les paramètres des sections
 * 
 * @returns {Object} { getParameterFields, getCommonFields, getVisibilityOptions, getStateOptions }
 */
export function useSectionParameters() {
  /**
   * Options pour le champ de visibilité
   */
  const getVisibilityOptions = () => [
    { value: 'guest', label: 'Invité (public)' },
    { value: 'user', label: 'Utilisateur' },
    { value: 'game_master', label: 'Maître de jeu' },
    { value: 'admin', label: 'Administrateur' },
  ];

  /**
   * Options pour le champ d'état
   */
  const getStateOptions = () => [
    { value: 'draft', label: 'Brouillon' },
    { value: 'preview', label: 'Prévisualisation' },
    { value: 'published', label: 'Publié' },
    { value: 'archived', label: 'Archivé' },
  ];

  /**
   * Génère les champs de formulaire depuis les paramètres d'un template
   * 
   * @param {Array} parameters - Tableau de paramètres depuis config.parameters
   * @returns {Array} Tableau d'objets avec les props pour les composants de champs
   */
  const getParameterFields = (parameters = []) => {
    if (!Array.isArray(parameters) || parameters.length === 0) {
      return [];
    }

    return parameters.map(param => {
      const field = {
        key: param.key,
        type: param.type,
        label: param.label || param.key,
        description: param.description || '',
        default: param.default,
        validation: param.validation || {},
      };

      // Propriétés spécifiques selon le type
      switch (param.type) {
        case 'select':
          field.options = param.options || [];
          break;
        case 'number':
          field.min = param.validation?.min;
          field.max = param.validation?.max;
          field.step = param.step || 1;
          field.suffix = param.suffix || '';
          break;
        case 'text':
          field.placeholder = param.placeholder || '';
          field.maxLength = param.validation?.maxLength || param.maxLength;
          break;
        case 'textarea':
          field.rows = param.rows || 4;
          field.maxLength = param.validation?.maxLength || param.maxLength;
          break;
        case 'color':
          // Pas de propriétés spécifiques pour le moment
          break;
        case 'toggle':
          // Pas de propriétés spécifiques
          break;
      }

      return field;
    });
  };

  /**
   * Retourne la configuration des champs communs à toutes les sections
   * 
   * @returns {Array} Tableau d'objets avec les props pour les composants de champs
   */
  const getCommonFields = () => [
    {
      key: 'title',
      type: 'text',
      label: 'Titre',
      description: 'Titre de la section (optionnel)',
      default: null,
      placeholder: 'Titre de la section',
    },
    {
      key: 'slug',
      type: 'text',
      label: 'Slug',
      description: 'Identifiant unique pour l\'ancre de la section (optionnel)',
      default: null,
      placeholder: 'mon-ancre',
    },
    {
      key: 'order',
      type: 'number',
      label: 'Ordre',
      description: 'Position de la section dans la page',
      default: 0,
      min: 0,
      step: 1,
    },
    {
      key: 'is_visible',
      type: 'select',
      label: 'Visibilité',
      description: 'Niveau de visibilité minimum pour voir la section',
      default: 'guest',
      options: getVisibilityOptions(),
    },
    {
      key: 'can_edit_role',
      type: 'select',
      label: 'Rôle d\'édition',
      description: 'Rôle minimum requis pour modifier la section',
      default: 'admin',
      options: getVisibilityOptions(),
    },
    {
      key: 'state',
      type: 'select',
      label: 'État',
      description: 'État de publication de la section',
      default: 'draft',
      options: getStateOptions(),
    },
    {
      key: 'classes',
      type: 'text',
      label: 'Classes CSS',
      description: 'Classes CSS personnalisées à ajouter au conteneur (séparées par des espaces)',
      default: null,
      placeholder: 'ex: my-custom-class another-class',
    },
    {
      key: 'customCss',
      type: 'textarea',
      label: 'CSS personnalisé',
      description: 'CSS personnalisé pour la section (sera injecté dans un tag <style>)',
      default: null,
      placeholder: 'ex: .section-container { background: red; }',
      rows: 4,
    },
  ];

  return {
    getParameterFields,
    getCommonFields,
    getVisibilityOptions,
    getStateOptions,
  };
}

export default useSectionParameters;


/**
 * Service pour gérer les paramètres des sections
 * 
 * @description
 * Service statique pour générer les champs de formulaire depuis les paramètres
 * définis dans les configs des templates.
 * 
 * **Avantages d'un service statique :**
 * - Réutilisable partout (pas seulement dans les composants Vue)
 * - Testable facilement
 * - Pas de dépendance à la réactivité Vue
 * 
 * @example
 * import { SectionParameterService } from '@/Utils/Services';
 * const fields = SectionParameterService.getParameterFields(templateConfig.parameters);
 * const commonFields = SectionParameterService.getCommonFields();
 */
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';
export class SectionParameterService {
    /**
     * Options pour les champs read_level / write_level
     * 
     * @returns {Array} Options de rôles
     */
    static getVisibilityOptions() {
        return getUserRoleOptions();
    }

    /**
     * Options pour le champ d'état
     * 
     * @returns {Array} Options d'état
     */
    static getStateOptions() {
        return getEntityStateOptions();
    }

    /**
     * Génère les champs de formulaire depuis les paramètres d'un template
     * 
     * @param {Array} parameters - Tableau de paramètres depuis config.parameters
     * @returns {Array} Tableau d'objets avec les props pour les composants de champs
     */
    static getParameterFields(parameters = []) {
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
    }

    /**
     * Retourne la configuration des champs communs à toutes les sections
     * 
     * @returns {Array} Tableau d'objets avec les props pour les composants de champs
     */
    static getCommonFields() {
        return [
            {
                key: 'title',
                type: 'text',
                label: 'Titre',
                description: 'Titre de la section (optionnel)',
                default: null,
            },
            {
                key: 'slug',
                type: 'text',
                label: 'Slug',
                description: 'Slug unique pour l\'ancre de la section (généré automatiquement depuis le titre)',
                default: null,
            },
            {
                key: 'order',
                type: 'number',
                label: 'Ordre',
                description: 'Ordre d\'affichage dans la page',
                default: 0,
                validation: {
                    min: 0,
                },
            },
            {
                key: 'read_level',
                type: 'select',
                label: 'Lecture (min.)',
                description: 'Niveau minimum requis pour voir la section',
                default: 0,
                options: this.getVisibilityOptions(),
            },
            {
                key: 'write_level',
                type: 'select',
                label: 'Écriture (min.)',
                description: 'Niveau minimum requis pour modifier cette section',
                default: 4,
                options: this.getVisibilityOptions(),
            },
            {
                key: 'state',
                type: 'select',
                label: 'État',
                description: 'Cycle de vie de la section',
                default: 'draft',
                options: this.getStateOptions(),
            },
            {
                key: 'classes',
                type: 'text',
                label: 'Classes CSS personnalisées',
                description: 'Classes CSS à ajouter au conteneur de la section',
                default: null,
            },
            {
                key: 'customCss',
                type: 'textarea',
                label: 'CSS personnalisé',
                description: 'CSS personnalisé pour la section (sera injecté dans un <style> tag)',
                default: null,
                rows: 4,
            },
        ];
    }

    /**
     * Retourne les champs communs et les champs spécifiques au template
     * 
     * @param {Object} templateConfig - Configuration du template (avec parameters)
     * @returns {Object} { commonFields, templateSpecificFields }
     */
    static getAllFields(templateConfig = {}) {
        return {
            commonFields: this.getCommonFields(),
            templateSpecificFields: this.getParameterFields(templateConfig?.parameters || []),
        };
    }
}

export default SectionParameterService;


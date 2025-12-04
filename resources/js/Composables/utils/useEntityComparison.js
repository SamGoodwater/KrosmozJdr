/**
 * useEntityComparison Composable
 * 
 * @description
 * Compare plusieurs entités et identifie les valeurs communes et différentes.
 * Utilisé pour l'édition multiple d'entités.
 * 
 * @example
 * const { commonValues, differentFields, hasDifferences } = useEntityComparison(selectedEntities, fieldsConfig);
 */
import { computed } from 'vue';

/**
 * Compare plusieurs entités et retourne les valeurs communes et les champs différents
 * @param {Array} entities - Tableau d'entités à comparer
 * @param {Object} fieldsConfig - Configuration des champs à comparer
 * @returns {Object} { commonValues, differentFields, hasDifferences }
 */
export function useEntityComparison(entities, fieldsConfig) {
    if (!entities || entities.length === 0) {
        return {
            commonValues: {},
            differentFields: [],
            hasDifferences: false
        };
    }

    // Si une seule entité, retourner ses valeurs comme valeurs communes
    if (entities.length === 1) {
        const entity = entities[0];
        const commonValues = {};
        Object.keys(fieldsConfig).forEach(key => {
            // Gérer les instances de modèles et les objets bruts
            if (entity && typeof entity._data !== 'undefined') {
                // Instance de modèle
                commonValues[key] = entity[key] ?? entity._data?.[key] ?? getDefaultValue(fieldsConfig[key].type);
            } else {
                // Objet brut
                commonValues[key] = entity[key] ?? getDefaultValue(fieldsConfig[key].type);
            }
        });
        return {
            commonValues,
            differentFields: [],
            hasDifferences: false
        };
    }

    // Comparer toutes les entités
    const commonValues = {};
    const differentFields = [];
    const fieldKeys = Object.keys(fieldsConfig);

    fieldKeys.forEach(key => {
        const values = entities.map(entity => {
            // Gérer les instances de modèles et les objets bruts
            if (entity && typeof entity._data !== 'undefined') {
                // Instance de modèle
                return entity[key] ?? entity._data?.[key] ?? getDefaultValue(fieldsConfig[key].type);
            } else {
                // Objet brut
                return entity[key] ?? getDefaultValue(fieldsConfig[key].type);
            }
        });

        // Vérifier si toutes les valeurs sont identiques
        const firstValue = values[0];
        const allSame = values.every(val => {
            // Comparaison profonde pour les objets et tableaux
            if (typeof val === 'object' && val !== null) {
                return JSON.stringify(val) === JSON.stringify(firstValue);
            }
            return val === firstValue;
        });

        if (allSame) {
            commonValues[key] = firstValue;
        } else {
            differentFields.push(key);
            // Pour les champs différents, on laisse la valeur vide ou null
            commonValues[key] = getDefaultValue(fieldsConfig[key].type);
        }
    });

    return {
        commonValues,
        differentFields,
        hasDifferences: differentFields.length > 0
    };
}

/**
 * Retourne une valeur par défaut selon le type de champ
 * @param {string} type - Type de champ
 * @returns {*} Valeur par défaut
 */
function getDefaultValue(type) {
    switch (type) {
        case 'number':
            return null;
        case 'checkbox':
            return false;
        case 'select':
            return null;
        case 'textarea':
            return '';
        default:
            return '';
    }
}

export default useEntityComparison;


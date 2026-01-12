/**
 * form-helpers — Fonctions utilitaires pour les formulaires d'entités
 * 
 * @description
 * Fonctions utilitaires pour initialiser et gérer les formulaires d'entités.
 */

/**
 * Initialise un objet de formulaire à partir d'une entité et d'une configuration de champs
 * 
 * @param {Object} entity - Instance de l'entité (peut avoir _data pour BaseModel)
 * @param {Object} fieldsConfig - Configuration des champs depuis les descriptors
 * @param {string[]} [fieldKeys] - Liste optionnelle de clés de champs à inclure (si null, utilise toutes les clés de fieldsConfig)
 * @returns {Object} Objet de formulaire initialisé
 * 
 * @example
 * const formData = initializeFormFromEntity(resource, fieldsConfig);
 * const form = useForm(formData);
 * 
 * @example
 * // Avec une liste spécifique de champs
 * const compactFields = ['name', 'level', 'rarity'];
 * const formData = initializeFormFromEntity(resource, fieldsConfig, compactFields);
 */
export function initializeFormFromEntity(entity, fieldsConfig, fieldKeys = null) {
    const keys = fieldKeys || Object.keys(fieldsConfig || {});
    
    return keys.reduce((acc, key) => {
        // Priorité : entity[key] > entity._data[key] > defaultValue > ''
        acc[key] = entity?.[key] ?? 
                   entity?._data?.[key] ?? 
                   fieldsConfig[key]?.defaultValue ?? 
                   '';
        return acc;
    }, {});
}

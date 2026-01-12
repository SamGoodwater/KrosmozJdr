/**
 * useEntityFieldHelpers — Composable pour les helpers de champs d'entité
 * 
 * @description
 * Fournit des fonctions utilitaires communes pour travailler avec les descriptors d'entités :
 * - getFieldIcon : Récupère l'icône d'un champ depuis les descriptors
 * - getFieldGroup : Récupère le groupe d'un champ depuis les descriptors
 * 
 * @param {Function} getDescriptorsFn - Fonction pour obtenir les descriptors (ex: getResourceFieldDescriptors)
 * @param {Object} ctx - Contexte pour les descriptors
 * @returns {Object} Helpers { getFieldIcon, getFieldGroup }
 * 
 * @example
 * import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
 * const { getFieldIcon, getFieldGroup } = useEntityFieldHelpers(getResourceFieldDescriptors, ctx);
 */
import { computed } from 'vue';

export function useEntityFieldHelpers(getDescriptorsFn, ctx = {}) {
    // Mémoriser les descriptors pour éviter les recalculs
    const descriptors = computed(() => {
        if (typeof getDescriptorsFn !== 'function') return {};
        return getDescriptorsFn(ctx);
    });

    /**
     * Récupère l'icône d'un champ depuis les descriptors
     * 
     * @param {string} fieldKey - Clé du champ
     * @returns {string} Icône FontAwesome ou icône par défaut
     */
    const getFieldIcon = (fieldKey) => {
        return descriptors.value[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
    };

    /**
     * Récupère le groupe d'un champ depuis les descriptors
     * 
     * @param {string} fieldKey - Clé du champ
     * @param {string} defaultGroup - Groupe par défaut si non spécifié
     * @returns {string} Nom du groupe
     */
    const getFieldGroup = (fieldKey, defaultGroup = 'Informations générales') => {
        const group = descriptors.value[fieldKey]?.edition?.form?.group;
        return group ? String(group) : defaultGroup;
    };

    /**
     * Groupe une liste de clés de champs par leur groupe
     * 
     * @param {string[]} fieldKeys - Liste des clés de champs
     * @param {string} defaultGroup - Groupe par défaut si non spécifié
     * @returns {Array<{title: string, keys: string[]}>} Liste des groupes avec leurs champs
     * 
     * @example
     * const groups = groupFieldsByGroup(['name', 'level', 'rarity']);
     * // Retourne: [{ title: 'Informations générales', keys: ['name', 'level', 'rarity'] }]
     */
    const groupFieldsByGroup = (fieldKeys, defaultGroup = 'Informations générales') => {
        if (!Array.isArray(fieldKeys)) return [];
        
        const groups = new Map();
        
        fieldKeys.forEach((key) => {
            const group = getFieldGroup(key, defaultGroup);
            if (!groups.has(group)) {
                groups.set(group, []);
            }
            groups.get(group).push(key);
        });
        
        return Array.from(groups.entries()).map(([title, keys]) => ({ title, keys }));
    };

    return {
        getFieldIcon,
        getFieldGroup,
        groupFieldsByGroup,
        descriptors, // Exposer pour usage avancé si nécessaire
    };
}

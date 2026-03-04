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
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';

export function useEntityFieldHelpers(getDescriptorsFn, ctx = {}, options = {}) {
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

    /**
     * Résout les métadonnées UI d'un champ (BDD -> descriptors -> fallback)
     *
     * @param {string} fieldKey
     * @returns {{label:string, shortLabel:string, icon:string, tooltip:string, color:string, characteristic:any}}
     */
    const getFieldUi = (fieldKey) => {
        return resolveEntityFieldUi({
            fieldKey,
            descriptors: descriptors.value,
            tableMeta: options?.tableMeta || {},
            entityType: options?.entityType || '',
        });
    };

    /**
     * Résout le style de badge d'un champ.
     *
     * @param {string} fieldKey
     * @param {Object} cell
     * @param {Record<string, string>} localColorMap
     * @returns {{color:string, autoLabel?:string, autoScheme?:string, autoTone?:string}}
     */
    const getBadgeUi = (fieldKey, cell, localColorMap = {}) => {
        return resolveEntityBadgeUi({
            fieldKey,
            cell,
            fieldUi: getFieldUi(fieldKey),
            localColorMap,
        });
    };

    return {
        getFieldIcon,
        getFieldGroup,
        groupFieldsByGroup,
        getFieldUi,
        getBadgeUi,
        descriptors, // Exposer pour usage avancé si nécessaire
    };
}

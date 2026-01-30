/**
 * Composable pour les options des formulaires de sections
 * 
 * @description
 * Fournit les options pour les selects des formulaires de sections :
 * - Lecture (read_level)
 * - Écriture (write_level)
 * - État (state)
 * 
 * Centralise la logique pour éviter la duplication entre CreateSectionModal et SectionParamsModal.
 * 
 * @example
 * const { visibilityOptions, stateOptions } = useSectionFormOptions();
 */
import { computed } from 'vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';

/**
 * Composable pour les options de formulaire de section
 * 
 * @returns {Object} { visibilityOptions, stateOptions }
 */
export function useSectionFormOptions() {
    /**
     * Options pour les champs read_level / write_level
     */
    const roleOptions = computed(() => getUserRoleOptions());

    /**
     * Options pour le champ state
     */
    const stateOptions = computed(() => getEntityStateOptions());

    return {
        roleOptions,
        stateOptions,
    };
}


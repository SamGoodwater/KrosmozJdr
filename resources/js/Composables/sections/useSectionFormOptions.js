/**
 * Composable pour les options des formulaires de sections
 * 
 * @description
 * Fournit les options pour les selects des formulaires de sections :
 * - Visibilité (is_visible)
 * - Rôle requis pour éditer (can_edit_role)
 * - État (state)
 * 
 * Centralise la logique pour éviter la duplication entre CreateSectionModal et SectionParamsModal.
 * 
 * @example
 * const { visibilityOptions, stateOptions } = useSectionFormOptions();
 */
import { computed } from 'vue';
import { Visibility } from '@/Utils/enums/Visibility';
import { PageState } from '@/Utils/enums/PageState';

/**
 * Composable pour les options de formulaire de section
 * 
 * @returns {Object} { visibilityOptions, stateOptions }
 */
export function useSectionFormOptions() {
    /**
     * Options pour le champ is_visible
     */
    const visibilityOptions = computed(() => [
        { value: Visibility.GUEST.value, label: 'Public (Invité)' },
        { value: Visibility.USER.value, label: 'Utilisateur' },
        { value: Visibility.GAME_MASTER.value, label: 'Maître du Jeu' },
        { value: Visibility.ADMIN.value, label: 'Administrateur' },
    ]);

    /**
     * Options pour le champ state
     */
    const stateOptions = computed(() => [
        { value: PageState.DRAFT.value, label: 'Brouillon' },
        { value: PageState.PREVIEW.value, label: 'Prévisualisation' },
        { value: PageState.PUBLISHED.value, label: 'Publié' },
        { value: PageState.ARCHIVED.value, label: 'Archivé' },
    ]);

    return {
        visibilityOptions,
        stateOptions,
    };
}


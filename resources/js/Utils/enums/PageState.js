/**
 * PageState Enum Utility
 * 
 * @description
 * Utilitaire JavaScript pour les états de page, correspondant à l'enum PHP PageState.
 * 
 * @example
 * import { PageState } from '@/Utils/enums/PageState';
 * const state = PageState.DRAFT;
 * console.log(state.label); // "Brouillon"
 */

export const PageState = {
    DRAFT: {
        value: 'draft',
        label: 'Brouillon',
        color: 'neutral'
    },
    PREVIEW: {
        value: 'preview',
        label: 'Prévisualisation',
        color: 'warning'
    },
    PUBLISHED: {
        value: 'published',
        label: 'Publié',
        color: 'success'
    },
    ARCHIVED: {
        value: 'archived',
        label: 'Archivé',
        color: 'error'
    }
};

/**
 * Retourne tous les états avec leurs labels pour les selects
 */
export function getPageStateOptions() {
    return Object.values(PageState).map(state => ({
        value: state.value,
        label: state.label
    }));
}

/**
 * Retourne un état par sa valeur
 */
export function getPageStateByValue(value) {
    return Object.values(PageState).find(state => state.value === value) || null;
}


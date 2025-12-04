/**
 * Visibility Enum Utility
 * 
 * @description
 * Utilitaire JavaScript pour les niveaux de visibilité, correspondant à l'enum PHP Visibility.
 * 
 * @example
 * import { Visibility } from '@/Utils/enums/Visibility';
 * const visibility = Visibility.GUEST;
 * console.log(visibility.label); // "Invité"
 */

export const Visibility = {
    GUEST: {
        value: 'guest',
        label: 'Invité'
    },
    USER: {
        value: 'user',
        label: 'Utilisateur'
    },
    GAME_MASTER: {
        value: 'game_master',
        label: 'Maître de jeu'
    },
    ADMIN: {
        value: 'admin',
        label: 'Administrateur'
    }
};

/**
 * Retourne tous les niveaux de visibilité avec leurs labels pour les selects
 */
export function getVisibilityOptions() {
    return Object.values(Visibility).map(visibility => ({
        value: visibility.value,
        label: visibility.label
    }));
}

/**
 * Retourne un niveau de visibilité par sa valeur
 */
export function getVisibilityByValue(value) {
    return Object.values(Visibility).find(visibility => visibility.value === value) || null;
}


/**
 * SectionType Enum Utility
 * 
 * @description
 * Utilitaire JavaScript pour les types de sections, correspondant à l'enum PHP SectionType.
 * 
 * @example
 * import { SectionType } from '@/Utils/enums/SectionType';
 * const type = SectionType.TEXT;
 * console.log(type.label); // "Texte"
 */

export const SectionType = {
    TEXT: {
        value: 'text',
        label: 'Texte',
        icon: 'fa-file-lines'
    },
    IMAGE: {
        value: 'image',
        label: 'Image',
        icon: 'fa-image'
    },
    GALLERY: {
        value: 'gallery',
        label: 'Galerie',
        icon: 'fa-images'
    },
    VIDEO: {
        value: 'video',
        label: 'Vidéo',
        icon: 'fa-video'
    },
    ENTITY_TABLE: {
        value: 'entity_table',
        label: 'Tableau d\'entités',
        icon: 'fa-table'
    }
};

/**
 * Retourne tous les types de sections avec leurs labels pour les selects
 */
export function getSectionTypeOptions() {
    return Object.values(SectionType).map(type => ({
        value: type.value,
        label: type.label,
        icon: type.icon
    }));
}

/**
 * Retourne un type de section par sa valeur
 */
export function getSectionTypeByValue(value) {
    return Object.values(SectionType).find(type => type.value === value) || null;
}


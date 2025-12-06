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
        icon: 'fa-file-lines',
        description: 'Section de texte riche avec éditeur WYSIWYG. Permet d\'ajouter du contenu formaté, des listes, des liens, etc.'
    },
    IMAGE: {
        value: 'image',
        label: 'Image',
        icon: 'fa-image',
        description: 'Affiche une image unique avec légende optionnelle. Permet d\'uploader et de configurer l\'affichage d\'une image.'
    },
    GALLERY: {
        value: 'gallery',
        label: 'Galerie',
        icon: 'fa-images',
        description: 'Galerie d\'images avec éditeur intégré. Permet d\'ajouter plusieurs images dans une grille personnalisable.'
    },
    VIDEO: {
        value: 'video',
        label: 'Vidéo',
        icon: 'fa-video',
        description: 'Affiche une vidéo (YouTube, Vimeo ou fichier direct). Permet d\'intégrer des vidéos avec contrôles personnalisables.'
    },
    ENTITY_TABLE: {
        value: 'entity_table',
        label: 'Tableau d\'entités',
        icon: 'fa-table',
        description: 'Affiche un tableau d\'entités avec filtres et options de tri. Permet de lister et filtrer des entités du jeu.'
    }
};

/**
 * Retourne tous les types de sections avec leurs labels pour les selects
 */
export function getSectionTypeOptions() {
    return Object.values(SectionType).map(type => ({
        value: type.value,
        label: type.label,
        icon: type.icon,
        description: type.description || ''
    }));
}

/**
 * Retourne un type de section par sa valeur
 */
export function getSectionTypeByValue(value) {
    return Object.values(SectionType).find(type => type.value === value) || null;
}


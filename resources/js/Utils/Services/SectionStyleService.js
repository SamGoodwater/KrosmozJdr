/**
 * Service pour générer les classes CSS des sections selon leurs settings
 * 
 * @description
 * Service statique pour générer les classes CSS dynamiques selon les settings d'une section.
 * Utilisé par les templates pour appliquer les styles (alignement, taille, etc.).
 * 
 * **Avantages d'un service statique :**
 * - Réutilisable partout (pas seulement dans les composants Vue)
 * - Testable facilement
 * - Pas de dépendance à la réactivité Vue
 * 
 * @example
 * import { SectionStyleService } from '@/Utils/Services';
 * const classes = SectionStyleService.getContainerClasses(settings);
 * const imageClasses = SectionStyleService.getImageClasses(settings);
 */
export class SectionStyleService {
    /**
     * Génère les classes d'alignement
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS d'alignement
     */
    static getAlignClasses(settings = {}) {
        const align = settings?.align || 'left';
        const alignMap = {
            'left': 'text-left',
            'center': 'text-center',
            'right': 'text-right',
            'justify': 'text-justify'
        };
        return alignMap[align] || 'text-left';
    }

    /**
     * Génère les classes de taille de texte
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS de taille
     */
    static getTextSizeClasses(settings = {}) {
        const size = settings?.size || 'md';
        const sizeMap = {
            'xs': 'text-xs',
            'sm': 'text-sm',
            'md': 'text-base',
            'lg': 'text-lg',
            'xl': 'text-xl',
            '2xl': 'text-2xl',
            '3xl': 'text-3xl'
        };
        return sizeMap[size] || 'text-base';
    }

    /**
     * Génère les classes de taille d'image
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS de taille d'image
     */
    static getImageSizeClasses(settings = {}) {
        const size = settings?.size || 'md';
        const sizeMap = {
            'xs': 'w-32',
            'sm': 'w-48',
            'md': 'w-64',
            'lg': 'w-96',
            'xl': 'w-[32rem]',
            'full': 'w-full'
        };
        return sizeMap[size] || 'w-64';
    }

    /**
     * Génère les classes pour les colonnes de galerie
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS de colonnes
     */
    static getGalleryColumnsClasses(settings = {}) {
        const columns = settings?.columns || 3;
        const columnMap = {
            2: 'grid-cols-2',
            3: 'grid-cols-3',
            4: 'grid-cols-4',
            5: 'grid-cols-5',
            6: 'grid-cols-6'
        };
        return columnMap[columns] || 'grid-cols-3';
    }

    /**
     * Génère les classes pour l'espacement de galerie
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS d'espacement
     */
    static getGalleryGapClasses(settings = {}) {
        const gap = settings?.gap || 'md';
        const gapMap = {
            'xs': 'gap-1',
            'sm': 'gap-2',
            'md': 'gap-4',
            'lg': 'gap-6',
            'xl': 'gap-8'
        };
        return gapMap[gap] || 'gap-4';
    }

    /**
     * Récupère les classes CSS personnalisées depuis settings.classes
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS personnalisées
     */
    static getCustomClasses(settings = {}) {
        return settings?.classes || '';
    }

    /**
     * Génère les classes combinées pour un conteneur de texte
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS combinées
     */
    static getContainerClasses(settings = {}) {
        return [
            this.getAlignClasses(settings),
            this.getTextSizeClasses(settings),
            this.getCustomClasses(settings)
        ].filter(Boolean).join(' ');
    }

    /**
     * Génère les classes combinées pour une galerie
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS combinées pour galerie
     */
    static getGalleryClasses(settings = {}) {
        return [
            'grid',
            this.getGalleryColumnsClasses(settings),
            this.getGalleryGapClasses(settings),
            this.getCustomClasses(settings)
        ].filter(Boolean).join(' ');
    }

    /**
     * Génère les classes combinées pour une image
     * 
     * @param {Object} settings - Settings de la section
     * @returns {String} Classes CSS combinées pour image
     */
    static getImageClasses(settings = {}) {
        return [
            this.getAlignClasses(settings),
            this.getImageSizeClasses(settings),
            this.getCustomClasses(settings)
        ].filter(Boolean).join(' ');
    }

    /**
     * Génère toutes les classes disponibles (pour compatibilité avec l'ancien useSectionStyles)
     * 
     * @param {Object} settings - Settings de la section
     * @returns {Object} Objet avec toutes les classes
     */
    static getAllClasses(settings = {}) {
        return {
            alignClasses: this.getAlignClasses(settings),
            sizeClasses: this.getTextSizeClasses(settings),
            imageSizeClasses: this.getImageSizeClasses(settings),
            galleryColumnsClasses: this.getGalleryColumnsClasses(settings),
            galleryGapClasses: this.getGalleryGapClasses(settings),
            customClasses: this.getCustomClasses(settings),
            containerClasses: this.getContainerClasses(settings),
            galleryClasses: this.getGalleryClasses(settings),
            imageClasses: this.getImageClasses(settings),
        };
    }
}

export default SectionStyleService;


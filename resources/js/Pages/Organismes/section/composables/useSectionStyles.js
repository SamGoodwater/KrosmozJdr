/**
 * Composable pour gérer les styles CSS des sections selon leurs settings
 * 
 * @description
 * Génère les classes CSS dynamiques selon les settings d'une section.
 * Utilisé par les templates pour appliquer les styles (alignement, taille, etc.)
 * 
 * @example
 * const { alignClasses, sizeClasses, containerClasses } = useSectionStyles(settings);
 */
import { computed } from 'vue';

/**
 * Composable pour générer les classes CSS selon les settings
 * 
 * @param {Object|ComputedRef} settings - Settings de la section (peut être réactif)
 * @returns {Object} Classes CSS calculées
 */
export function useSectionStyles(settings) {
  // Extraire la valeur réactive si nécessaire
  const settingsValue = computed(() => {
    if (typeof settings === 'object' && 'value' in settings) {
      return settings.value || {};
    }
    return settings || {};
  });

  /**
   * Classes d'alignement
   */
  const alignClasses = computed(() => {
    const align = settingsValue.value?.align || 'left';
    const alignMap = {
      'left': 'text-left',
      'center': 'text-center',
      'right': 'text-right',
      'justify': 'text-justify'
    };
    return alignMap[align] || 'text-left';
  });

  /**
   * Classes de taille de texte
   */
  const sizeClasses = computed(() => {
    const size = settingsValue.value?.size || 'md';
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
  });

  /**
   * Classes de taille d'image
   */
  const imageSizeClasses = computed(() => {
    const size = settingsValue.value?.size || 'md';
    const sizeMap = {
      'xs': 'w-32',
      'sm': 'w-48',
      'md': 'w-64',
      'lg': 'w-96',
      'xl': 'w-[32rem]',
      'full': 'w-full'
    };
    return sizeMap[size] || 'w-64';
  });

  /**
   * Classes pour les colonnes de galerie
   */
  const galleryColumnsClasses = computed(() => {
    const columns = settingsValue.value?.columns || 3;
    const columnMap = {
      2: 'grid-cols-2',
      3: 'grid-cols-3',
      4: 'grid-cols-4',
      5: 'grid-cols-5',
      6: 'grid-cols-6'
    };
    return columnMap[columns] || 'grid-cols-3';
  });

  /**
   * Classes pour l'espacement de galerie
   */
  const galleryGapClasses = computed(() => {
    const gap = settingsValue.value?.gap || 'md';
    const gapMap = {
      'xs': 'gap-1',
      'sm': 'gap-2',
      'md': 'gap-4',
      'lg': 'gap-6',
      'xl': 'gap-8'
    };
    return gapMap[gap] || 'gap-4';
  });

  /**
   * Classes CSS personnalisées depuis settings.classes
   */
  const customClasses = computed(() => {
    return settingsValue.value?.classes || '';
  });

  /**
   * Classes combinées pour le conteneur
   */
  const containerClasses = computed(() => {
    return [
      alignClasses.value,
      sizeClasses.value,
      customClasses.value
    ].filter(Boolean).join(' ');
  });

  /**
   * Classes combinées pour une galerie
   */
  const galleryClasses = computed(() => {
    return [
      'grid',
      galleryColumnsClasses.value,
      galleryGapClasses.value,
      customClasses.value
    ].filter(Boolean).join(' ');
  });

  /**
   * Classes combinées pour une image
   */
  const imageClasses = computed(() => {
    return [
      alignClasses.value,
      imageSizeClasses.value,
      customClasses.value
    ].filter(Boolean).join(' ');
  });

  return {
    // Classes individuelles
    alignClasses,
    sizeClasses,
    imageSizeClasses,
    galleryColumnsClasses,
    galleryGapClasses,
    customClasses,
    
    // Classes combinées
    containerClasses,
    galleryClasses,
    imageClasses,
  };
}

export default useSectionStyles;


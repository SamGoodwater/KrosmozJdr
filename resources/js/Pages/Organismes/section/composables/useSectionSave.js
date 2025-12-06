/**
 * Composable pour gérer la sauvegarde des sections
 * 
 * @description
 * Gère l'auto-save des sections avec debounce.
 * Les templates peuvent utiliser ce composable pour sauvegarder leurs données.
 * 
 * @example
 * const { saveSection, saveSectionImmediate } = useSectionSave();
 * saveSection(sectionId, { data: { content: '...' } });
 */
import { router } from '@inertiajs/vue3';

/**
 * Fonction debounce simple avec cancel
 */
function debounce(func, wait) {
  let timeout;
  const debouncedFn = function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
  
  debouncedFn.cancel = () => {
    clearTimeout(timeout);
  };
  
  return debouncedFn;
}

// Map pour stocker les fonctions debounced par section ID
const debouncedSaves = new Map();

/**
 * Sauvegarde une section avec debounce (auto-save)
 * 
 * @param {Number|String} sectionId - ID de la section
 * @param {Object} updates - Données à mettre à jour { data, settings, title, etc. }
 * @param {Number} delay - Délai en ms (défaut: 500)
 */
const saveSection = (sectionId, updates, delay = 500) => {
  // Si une sauvegarde debounced existe déjà, l'annuler
  if (debouncedSaves.has(sectionId)) {
    debouncedSaves.get(sectionId).cancel();
  }
  
  // Créer une nouvelle fonction debounced
  const debouncedFn = debounce(() => {
    router.patch(route('sections.update', sectionId), updates, {
      preserveScroll: true,
      only: ['page'], // Recharger uniquement la page
      onSuccess: () => {
        // Nettoyer après sauvegarde réussie
        debouncedSaves.delete(sectionId);
      },
      onError: (errors) => {
        console.error('Erreur lors de la sauvegarde de la section:', errors);
        debouncedSaves.delete(sectionId);
      }
    });
  }, delay);
  
  // Stocker la fonction debounced
  debouncedSaves.set(sectionId, debouncedFn);
  
  // Appeler la fonction debounced
  debouncedFn();
};

/**
 * Sauvegarde immédiate d'une section (sans debounce)
 * 
 * @param {Number|String} sectionId - ID de la section
 * @param {Object} updates - Données à mettre à jour
 */
const saveSectionImmediate = (sectionId, updates) => {
  // Annuler toute sauvegarde debounced en cours
  if (debouncedSaves.has(sectionId)) {
    debouncedSaves.get(sectionId).cancel();
    debouncedSaves.delete(sectionId);
  }
  
  router.patch(route('sections.update', sectionId), updates, {
    preserveScroll: true,
    only: ['page'],
  });
};

/**
 * Composable pour gérer la sauvegarde des sections
 * 
 * @returns {Object} { saveSection, saveSectionImmediate }
 */
export function useSectionSave() {
  return {
    saveSection,
    saveSectionImmediate,
  };
}


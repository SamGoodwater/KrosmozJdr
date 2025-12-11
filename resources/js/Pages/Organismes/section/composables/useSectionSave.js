/**
 * Composable pour gérer la sauvegarde des sections
 * 
 * @description
 * Gère l'auto-save des sections avec debounce pour éviter les appels API trop fréquents.
 * Utilise `useSectionAPI` en interne pour centraliser les appels backend.
 * 
 * **Fonctionnalités :**
 * - `saveSection()` : Sauvegarde avec debounce (500ms par défaut) - idéal pour l'auto-save
 * - `saveSectionImmediate()` : Sauvegarde immédiate sans debounce - pour les actions utilisateur
 * 
 * **Gestion du debounce :**
 * - Chaque section a sa propre fonction debounced
 * - Si une nouvelle sauvegarde est déclenchée avant la fin du délai, la précédente est annulée
 * - Les fonctions debounced sont nettoyées après succès ou erreur
 * 
 * @example
 * // Auto-save avec debounce (recommandé pour les modifications en temps réel)
 * const { saveSection } = useSectionSave();
 * watch(content, (newContent) => {
 *   saveSection(sectionId, { data: { content: newContent } });
 * });
 * 
 * // Sauvegarde immédiate (pour les actions utilisateur comme "Enregistrer")
 * const { saveSectionImmediate } = useSectionSave();
 * saveSectionImmediate(sectionId, { title: 'Nouveau titre' });
 */
import { router } from '@inertiajs/vue3';
import { useSectionAPI } from './useSectionAPI';

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

// Instance partagée de useSectionAPI
const sectionAPI = useSectionAPI();

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
    sectionAPI.updateSection(sectionId, updates, {
      onSuccess: () => {
        // Nettoyer après sauvegarde réussie
        debouncedSaves.delete(sectionId);
      },
      onError: (errors) => {
        console.error('Erreur lors de la sauvegarde de la section:', errors);
        debouncedSaves.delete(sectionId);
      }
    }).catch(() => {
      // Erreur déjà gérée dans onError
      debouncedSaves.delete(sectionId);
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
  
  sectionAPI.updateSection(sectionId, updates);
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


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
 * Normalise les options de sauvegarde.
 *
 * @param {Number|Object} delayOrOptions - Délai legacy (number) ou options.
 * @returns {{delay:number,onQueued:function|null,onSuccess:function|null,onError:function|null}}
 */
const normalizeSaveOptions = (delayOrOptions) => {
  if (typeof delayOrOptions === 'number') {
    return {
      delay: delayOrOptions,
      onQueued: null,
      onSuccess: null,
      onError: null,
    };
  }

  const opts = (delayOrOptions && typeof delayOrOptions === 'object') ? delayOrOptions : {};
  return {
    delay: Number(opts.delay) > 0 ? Number(opts.delay) : 500,
    onQueued: typeof opts.onQueued === 'function' ? opts.onQueued : null,
    onSuccess: typeof opts.onSuccess === 'function' ? opts.onSuccess : null,
    onError: typeof opts.onError === 'function' ? opts.onError : null,
  };
};

/**
 * Sauvegarde une section avec debounce (auto-save)
 * 
 * @param {Number|String} sectionId - ID de la section
 * @param {Object} updates - Données à mettre à jour { data, settings, title, etc. }
 * @param {Number|Object} delayOrOptions - Délai legacy (number) ou options { delay, onQueued, onSuccess, onError }
 */
const saveSection = (sectionId, updates, delayOrOptions = 500) => {
  const options = normalizeSaveOptions(delayOrOptions);

  // Si une sauvegarde debounced existe déjà, l'annuler
  if (debouncedSaves.has(sectionId)) {
    debouncedSaves.get(sectionId).cancel();
  }
  
  // Créer une nouvelle fonction debounced
  const debouncedFn = debounce(() => {
    sectionAPI.updateSection(sectionId, updates, {
      silent: true,
      onSuccess: () => {
        options.onSuccess?.();
        // Nettoyer après sauvegarde réussie
        debouncedSaves.delete(sectionId);
      },
      onError: (errors) => {
        console.error('Erreur lors de la sauvegarde de la section:', errors);
        options.onError?.(errors);
        debouncedSaves.delete(sectionId);
      }
    }).catch(() => {
      // Erreur déjà gérée dans onError
      debouncedSaves.delete(sectionId);
    });
  }, options.delay);
  
  // Stocker la fonction debounced
  debouncedSaves.set(sectionId, debouncedFn);
  
  // Appeler la fonction debounced
  debouncedFn();
  options.onQueued?.();
};

/**
 * Sauvegarde immédiate d'une section (sans debounce)
 * 
 * @param {Number|String} sectionId - ID de la section
 * @param {Object} updates - Données à mettre à jour
 * @param {Object} options - Options { onSuccess, onError }
 */
const saveSectionImmediate = (sectionId, updates, options = {}) => {
  // Annuler toute sauvegarde debounced en cours
  if (debouncedSaves.has(sectionId)) {
    debouncedSaves.get(sectionId).cancel();
    debouncedSaves.delete(sectionId);
  }
  
  sectionAPI.updateSection(sectionId, updates, {
    silent: true,
    onSuccess: () => options?.onSuccess?.(),
    onError: (errors) => {
      console.error('Erreur lors de la sauvegarde immédiate de la section:', errors);
      options?.onError?.(errors);
    },
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


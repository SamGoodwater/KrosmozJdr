import { onMounted, onUnmounted } from 'vue';

/**
 * Composable pour gérer les clics extérieurs et la navigation clavier
 * Réutilisable pour dropdowns, modals, tooltips, etc.
 * 
 * @param {Function} callback - Fonction à appeler lors du clic extérieur
 * @param {Object} options - Options de configuration
 * @param {boolean} options.enabled - Activer/désactiver la détection
 * @param {boolean} options.escapeKey - Gérer la touche Escape
 * @param {boolean} options.focusTrap - Piéger le focus dans l'élément
 * @param {Array} options.excludeSelectors - Sélecteurs à exclure
 * @returns {Object} - Méthodes de contrôle
 */
export function useClickOutside(callback, options = {}) {
  const {
    enabled = true,
    escapeKey = true,
    focusTrap = false,
    excludeSelectors = [],
    closeOnContentClick = true
  } = options;

  let isActive = false;

  // Gestion du clic extérieur
  const handleClickOutside = (event) => {
    if (!enabled || !isActive) return;

    // Vérifier si le clic est sur un élément exclu
    const isExcluded = excludeSelectors.some(selector => {
      return event.target.closest(selector);
    });

    if (isExcluded) return;

    // Si closeOnContentClick est false, ne pas fermer si le clic est dans le contenu
    if (!closeOnContentClick) {
      const contentElement = document.querySelector('.dropdown-content');
      if (contentElement && contentElement.contains(event.target)) {
        return;
      }
    }

    // Appeler le callback
    callback(event);
  };

  // Gestion de la touche Escape
  const handleEscape = (event) => {
    if (!enabled || !isActive || !escapeKey) return;
    
    if (event.key === 'Escape') {
      event.preventDefault();
      callback(event);
    }
  };

  // Gestion du focus pour le focus trap
  const handleFocus = (event) => {
    if (!enabled || !isActive || !focusTrap) return;

    // Logique de focus trap si nécessaire
    // (à implémenter selon les besoins)
  };

  // Activer la détection
  const enable = () => {
    isActive = true;
  };

  // Désactiver la détection
  const disable = () => {
    isActive = false;
  };

  // Gestion des événements
  const addEventListeners = () => {
    if (!enabled) return;

    // Utiliser capture pour intercepter les événements tôt
    document.addEventListener('mousedown', handleClickOutside, true);
    document.addEventListener('touchstart', handleClickOutside, true);
    
    if (escapeKey) {
      document.addEventListener('keydown', handleEscape, true);
    }
    
    if (focusTrap) {
      document.addEventListener('focusin', handleFocus, true);
    }
  };

  const removeEventListeners = () => {
    document.removeEventListener('mousedown', handleClickOutside, true);
    document.removeEventListener('touchstart', handleClickOutside, true);
    document.removeEventListener('keydown', handleEscape, true);
    document.removeEventListener('focusin', handleFocus, true);
  };

  // Lifecycle
  onMounted(() => {
    addEventListeners();
  });

  onUnmounted(() => {
    removeEventListeners();
  });

  return {
    enable,
    disable,
    isActive: () => isActive
  };
}

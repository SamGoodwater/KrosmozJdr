import { ref, provide, inject, onMounted, onUnmounted, readonly } from 'vue';

const DROPDOWN_PROVIDER_KEY = Symbol('dropdown-provider');

/**
 * Provider pour gérer les instances multiples de dropdowns
 * Gère la fermeture automatique, les z-index, etc.
 */
export function useDropdownProvider() {
  const openDropdowns = ref(new Set());
  const zIndexCounter = ref(1000);

  // Enregistrer un dropdown ouvert
  const registerDropdown = (id) => {
    openDropdowns.value.add(id);
    return zIndexCounter.value++;
  };

  // Désenregistrer un dropdown
  const unregisterDropdown = (id) => {
    openDropdowns.value.delete(id);
  };

  // Fermer tous les autres dropdowns
  const closeOtherDropdowns = (currentId) => {
    openDropdowns.value.forEach(id => {
      if (id !== currentId) {
        // Émettre un événement pour fermer le dropdown
        window.dispatchEvent(new CustomEvent('close-dropdown', { 
          detail: { id } 
        }));
      }
    });
  };

  // Fermer tous les dropdowns
  const closeAllDropdowns = () => {
    openDropdowns.value.forEach(id => {
      window.dispatchEvent(new CustomEvent('close-dropdown', { 
        detail: { id } 
      }));
    });
    openDropdowns.value.clear();
  };

  // Vérifier si un dropdown est ouvert
  const isDropdownOpen = (id) => {
    return openDropdowns.value.has(id);
  };

  // Obtenir le nombre de dropdowns ouverts
  const getOpenDropdownsCount = () => {
    return openDropdowns.value.size;
  };

  const providerValue = {
    openDropdowns,
    registerDropdown,
    unregisterDropdown,
    closeOtherDropdowns,
    closeAllDropdowns,
    isDropdownOpen,
    getOpenDropdownsCount
  };

  provide(DROPDOWN_PROVIDER_KEY, providerValue);

  return providerValue;
}

/**
 * Hook pour utiliser le provider dropdown
 */
export function useDropdown() {
  const provider = inject(DROPDOWN_PROVIDER_KEY);
  
  if (!provider) {
    throw new Error('useDropdown must be used within a component that provides dropdown context');
  }
  
  return provider;
}

/**
 * Hook pour créer un dropdown avec gestion automatique
 */
export function useDropdownInstance(id) {
  const provider = useDropdown();
  const isOpen = ref(false);
  const zIndex = ref(1000);

  // Ouvrir le dropdown
  const open = () => {
    if (isOpen.value) return;
    
    // Fermer les autres dropdowns
    provider.closeOtherDropdowns(id);
    
    // Enregistrer ce dropdown
    zIndex.value = provider.registerDropdown(id);
    isOpen.value = true;
  };

  // Fermer le dropdown
  const close = () => {
    if (!isOpen.value) return;
    
    provider.unregisterDropdown(id);
    isOpen.value = false;
  };

  // Toggle du dropdown
  const toggle = () => {
    if (isOpen.value) {
      close();
    } else {
      open();
    }
  };

  // Écouter les événements de fermeture
  const handleCloseEvent = (event) => {
    if (event.detail.id === id) {
      close();
    }
  };

  // Lifecycle
  onMounted(() => {
    window.addEventListener('close-dropdown', handleCloseEvent);
  });

  onUnmounted(() => {
    window.removeEventListener('close-dropdown', handleCloseEvent);
    // Nettoyer si le dropdown était ouvert
    if (isOpen.value) {
      provider.unregisterDropdown(id);
    }
  });

  return {
    isOpen: readonly(isOpen),
    zIndex: readonly(zIndex),
    open,
    close,
    toggle
  };
}

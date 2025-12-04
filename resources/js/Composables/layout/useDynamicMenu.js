/**
 * useDynamicMenu Composable
 * 
 * @description
 * Composable pour gérer le menu dynamique des pages.
 * - Récupère les pages du menu depuis l'API
 * - Gère le cache côté client
 * - Construit l'arborescence du menu
 * 
 * @example
 * const { menuItems, loading, error, refresh } = useDynamicMenu();
 */
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const menuItems = ref([]);
const loading = ref(false);
const error = ref(null);
const cacheTTL = 3600000; // 1 heure en millisecondes

/**
 * Génère la clé de cache selon l'utilisateur
 * @param {number|null} userId - ID de l'utilisateur (null pour invité)
 * @returns {string}
 */
const getCacheKey = (userId) => {
    return `dynamic_menu_cache_${userId || 'guest'}`;
};

/**
 * Récupère les pages du menu depuis l'API
 */
const fetchMenuPages = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        // Récupérer l'ID utilisateur depuis les props Inertia
        // On doit utiliser usePage() mais on ne peut pas l'utiliser ici car c'est en dehors du composable
        // On va donc récupérer depuis window.__inertia ou faire l'appel sans cache côté client
        // Le backend gère déjà le cache par utilisateur
        
        // Récupérer depuis l'API (le backend gère le cache par utilisateur)
        const response = await axios.get(route('pages.menu'));
        
        if (response.data && response.data.menu) {
            menuItems.value = response.data.menu;
        }
    } catch (err) {
        console.error('[useDynamicMenu] Erreur lors de la récupération du menu:', err);
        error.value = err.message || 'Erreur lors du chargement du menu';
    } finally {
        loading.value = false;
    }
};

/**
 * Invalide le cache du menu pour tous les utilisateurs
 */
const clearCache = () => {
    // Supprimer tous les caches de menu (le backend gère le cache)
    // On peut aussi appeler une route backend pour invalider le cache si nécessaire
    const keys = Object.keys(localStorage);
    keys.forEach(key => {
        if (key.startsWith('dynamic_menu_cache_')) {
            localStorage.removeItem(key);
        }
    });
};

/**
 * Rafraîchit le menu
 */
const refresh = async () => {
    clearCache();
    await fetchMenuPages();
};

/**
 * Vérifie si une page est active
 */
const isPageActive = (page, currentRoute) => {
    if (!currentRoute) return false;
    
    // Vérifier si la route actuelle correspond à la page
    if (currentRoute.includes(`/pages/${page.slug}`)) {
        return true;
    }
    
    // Vérifier récursivement les enfants
    if (page.children && page.children.length > 0) {
        return page.children.some(child => isPageActive(child, currentRoute));
    }
    
    return false;
};

/**
 * Vérifie si un menu parent doit être ouvert
 */
const shouldMenuBeOpen = (page, currentRoute) => {
    if (isPageActive(page, currentRoute)) {
        return true;
    }
    
    if (page.children && page.children.length > 0) {
        return page.children.some(child => isPageActive(child, currentRoute));
    }
    
    return false;
};

export function useDynamicMenu() {
    // Charger le menu au montage
    onMounted(() => {
        fetchMenuPages();
    });
    
    return {
        menuItems: computed(() => menuItems.value),
        loading: computed(() => loading.value),
        error: computed(() => error.value),
        refresh,
        clearCache,
        isPageActive,
        shouldMenuBeOpen
    };
}


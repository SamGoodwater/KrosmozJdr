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
const cacheKey = 'dynamic_menu_cache';
const cacheTTL = 3600000; // 1 heure en millisecondes

/**
 * Récupère les pages du menu depuis l'API
 */
const fetchMenuPages = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        // Vérifier le cache
        const cached = localStorage.getItem(cacheKey);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            const now = Date.now();
            
            if (now - timestamp < cacheTTL) {
                menuItems.value = data;
                loading.value = false;
                return;
            }
        }
        
        // Récupérer depuis l'API
        const response = await axios.get(route('pages.menu'));
        
        if (response.data && response.data.menu) {
            menuItems.value = response.data.menu;
            
            // Mettre en cache
            localStorage.setItem(cacheKey, JSON.stringify({
                data: response.data.menu,
                timestamp: Date.now()
            }));
        }
    } catch (err) {
        console.error('[useDynamicMenu] Erreur lors de la récupération du menu:', err);
        error.value = err.message || 'Erreur lors du chargement du menu';
    } finally {
        loading.value = false;
    }
};

/**
 * Invalide le cache du menu
 */
const clearCache = () => {
    localStorage.removeItem(cacheKey);
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


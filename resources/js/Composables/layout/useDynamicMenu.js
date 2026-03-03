/**
 * useDynamicMenu Composable
 *
 * @description
 * Composable pour gérer le menu dynamique des pages.
 * - Récupère les pages du menu depuis l'API GET /pages/menu
 * - Affiche uniquement les pages « à afficher » : state=playable, in_menu=true, visibles pour l'utilisateur
 * - Rafraîchit le menu à chaque navigation vers une URL /pages/* (après création/édition/suppression)
 *
 * @example
 * const { menuItems, loading, error, refresh } = useDynamicMenu();
 */
import { ref, computed, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const menuItems = ref([]);
const loading = ref(false);
const error = ref(null);

/**
 * Récupère les pages du menu depuis l'API.
 * Le backend ne retourne que les pages playable + in_menu + visibles pour l'utilisateur.
 */
const fetchMenuPages = async () => {
    loading.value = true;
    error.value = null;

    try {
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
    const page = usePage();

    // Charger le menu au montage
    onMounted(() => {
        fetchMenuPages();
    });

    // Rafraîchir le menu à chaque navigation vers une page /pages/* (ex. après création/édition)
    // pour que la liste de gauche reflète bien les pages à afficher (in_menu, playable).
    watch(
        () => page.url,
        (newUrl) => {
            if (newUrl && newUrl.startsWith('/pages')) {
                fetchMenuPages();
            }
        },
        { immediate: false }
    );

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


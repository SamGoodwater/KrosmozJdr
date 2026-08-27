/**
 * useDynamicMenu Composable
 *
 * @description
 * Composable pour gérer le menu dynamique des pages.
 * - Récupère les pages du menu depuis l'API GET /pages/menu
 * - Affiche uniquement les pages « à afficher » : state=playable, in_menu=true, visibles pour l'utilisateur
 * - Recalcule le menu à la connexion / déconnexion (le layout Aside survit au login Inertia)
 * - Rafraîchit aussi à chaque navigation (création/édition de pages, etc.)
 *
 * @example
 * const { menuItems, loading, error, refresh } = useDynamicMenu();
 */
import { ref, computed, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Clé d’auth pour détecter login / logout / changement de rôle.
 *
 * @param {import('@inertiajs/vue3').Page} page
 * @returns {string}
 */
function authMenuKey(page) {
    const user = page.props?.auth?.user;
    if (!user?.id) {
        return 'guest';
    }
    return `${user.id}:${user.role ?? ''}`;
}

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
 * Extrait le chemin (/path) d'une URL menu ou Inertia (chemin seul ou absolue).
 *
 * @param {string} url
 * @returns {string}
 */
const menuItemPath = (url) => {
    if (!url || typeof url !== 'string') return '';
    const trimmed = url.split('?')[0];
    if (trimmed.startsWith('/')) return trimmed;
    try {
        return new URL(trimmed, window.location.origin).pathname || '';
    } catch {
        return trimmed;
    }
};

/**
 * Vérifie si un item de menu est actif (page ou lien bibliothèque)
 */
const isPageActive = (page, currentRoute) => {
    if (!currentRoute) return false;

    const cur = menuItemPath(currentRoute);
    const itemPath = menuItemPath(page.url);
    if (itemPath && (cur === itemPath || cur.startsWith(`${itemPath}/`))) return true;
    if (page.slug && cur.includes(`/pages/${page.slug}`)) return true;

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

    onMounted(() => {
        fetchMenuPages();
    });

    watch(
        () => authMenuKey(page),
        (key, previous) => {
            if (previous !== undefined && key !== previous) {
                fetchMenuPages();
            }
        },
    );

    watch(
        () => page.url,
        (newUrl, oldUrl) => {
            if (newUrl && newUrl !== oldUrl) {
                fetchMenuPages();
            }
        },
        { immediate: false },
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


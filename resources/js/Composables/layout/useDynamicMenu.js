/**
 * useDynamicMenu Composable
 *
 * @description
 * Arbre du menu Aside, chargé une fois via GET /pages/menu.
 * La page courante est marquée côté client (`isPageActive`) : changer d’URL
 * ne relance pas la requête. Un nouveau fetch a lieu au login / logout / changement
 * de rôle, ou quand la prop Inertia `menu_revision` change (CRUD de page).
 *
 * @example
 * const { menuItems, loading, error, refresh, isPageActive } = useDynamicMenu();
 */
import { ref, computed, watch, effectScope } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

/**
 * Clé d’auth pour détecter login / logout / changement de rôle.
 *
 * @param {import('@inertiajs/vue3').Page} page
 * @returns {string}
 * @example authMenuKey(page); // "guest" ou "12:3"
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

/** @type {Promise<void>|null} */
let inflight = null;

/** @type {import('vue').EffectScope|null} */
let scope = null;

/**
 * Récupère les pages du menu depuis l'API.
 * Le backend ne retourne que les pages playable + in_menu + visibles pour l'utilisateur.
 *
 * @param {{ silent?: boolean }} [options] - `silent` garde l’arbre affiché (pas de spinner).
 * @returns {Promise<void>}
 * @example await fetchMenuPages({ silent: true });
 */
const fetchMenuPages = async ({ silent = false } = {}) => {
    if (inflight) {
        return inflight;
    }

    const hasMenu = menuItems.value.length > 0;
    if (!silent && !hasMenu) {
        loading.value = true;
    }
    if (!hasMenu) {
        error.value = null;
    }

    inflight = (async () => {
        try {
            const response = await axios.get(route('pages.menu'));
            const next = response.data?.menu;
            if (Array.isArray(next)) {
                menuItems.value = next;
                error.value = null;
            }
        } catch (err) {
            console.error('[useDynamicMenu] Erreur lors de la récupération du menu:', err);
            if (menuItems.value.length === 0) {
                error.value = err.message || 'Erreur lors du chargement du menu';
            }
        } finally {
            loading.value = false;
            inflight = null;
        }
    })();

    return inflight;
};

/**
 * Rafraîchit le menu sans démonter l’arbre s’il est déjà affiché.
 *
 * @returns {Promise<void>}
 * @example await refresh();
 */
const refresh = async () => {
    await fetchMenuPages({ silent: menuItems.value.length > 0 });
};

/**
 * Extrait le chemin (/path) d'une URL menu ou Inertia (chemin seul ou absolue).
 *
 * @param {string} url
 * @returns {string}
 * @example menuItemPath('http://localhost/pages/regles?x=1'); // "/pages/regles"
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
 *
 * @param {{ url?: string, slug?: string, children?: Array }} page
 * @param {string} currentRoute
 * @returns {boolean}
 * @example isPageActive({ url: '/pages/regles' }, '/pages/regles'); // true
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
 *
 * @param {{ url?: string, slug?: string, children?: Array }} page
 * @param {string} currentRoute
 * @returns {boolean}
 * @example shouldMenuBeOpen({ url: '/pages/regles', children: [] }, '/pages/regles'); // true
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

/**
 * Branche les watchers une seule fois pour toute l’app (Aside + PageRenderer).
 *
 * @param {import('@inertiajs/vue3').Page} page
 */
function boot(page) {
    if (scope || typeof window === 'undefined') {
        return;
    }

    scope = effectScope(true);
    scope.run(() => {
        watch(
            () => authMenuKey(page),
            (key, previous) => {
                if (previous !== undefined && key !== previous) {
                    fetchMenuPages({ silent: menuItems.value.length > 0 });
                }
            },
        );

        watch(
            () => page.props?.menu_revision,
            (revision, previous) => {
                if (previous !== undefined && revision !== previous) {
                    fetchMenuPages({ silent: true });
                }
            },
        );
    });

    fetchMenuPages();
}

/**
 * Remet l’état module (tests). Sans effet en production.
 */
export function resetDynamicMenuForTests() {
    scope?.stop();
    scope = null;
    inflight = null;
    menuItems.value = [];
    loading.value = false;
    error.value = null;
}

/**
 * @returns {{
 *   menuItems: import('vue').ComputedRef<Array>,
 *   loading: import('vue').ComputedRef<boolean>,
 *   error: import('vue').ComputedRef<string|null>,
 *   refresh: () => Promise<void>,
 *   isPageActive: typeof isPageActive,
 *   shouldMenuBeOpen: typeof shouldMenuBeOpen,
 * }}
 */
export function useDynamicMenu() {
    const page = usePage();
    boot(page);

    return {
        menuItems: computed(() => menuItems.value),
        loading: computed(() => loading.value),
        error: computed(() => error.value),
        refresh,
        isPageActive,
        shouldMenuBeOpen,
    };
}

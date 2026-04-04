/**
 * Breakpoints alignés sur Tailwind v4 (thème par défaut) : **md = 768px**, **lg = 1024px**.
 * Toute logique JS « mobile / tablette / desktop » pour les layouts doit réutiliser ces constantes
 * ou les `MEDIA_QUERY_*` associées pour rester cohérent avec les utilitaires `md:` / `lg:`.
 *
 * **Attributs `data` navigation shell** (préfixe `kz-nav`, kebab-case dans le DOM) :
 * - `data-kz-nav-app-sidebar` : racine du panneau latéral principal (`Aside`)
 * - `data-kz-nav-toggle-sidebar` : contrôle d’ouverture / fermeture (hamburger, dock, etc.)
 *
 * @example
 * import { BREAKPOINT_MD_PX, NAV_SELECTORS } from '@/Composables/layout/viewport-breakpoints';
 */

/** @type {number} Correspond à `md` Tailwind */
export const BREAKPOINT_MD_PX = 768;

/** @type {number} Correspond à `lg` Tailwind */
export const BREAKPOINT_LG_PX = 1024;

/** Mobile strict : largeur &lt; md (équivalent `max-md` en sémantique « phone ») */
export const MEDIA_QUERY_MOBILE_MAX = `(max-width: ${BREAKPOINT_MD_PX - 1}px)`;

/** Tablette seule : md inclus, &lt; lg */
export const MEDIA_QUERY_TABLET_ONLY = `(min-width: ${BREAKPOINT_MD_PX}px) and (max-width: ${BREAKPOINT_LG_PX - 1}px)`;

/** Desktop : lg et plus */
export const MEDIA_QUERY_DESKTOP_MIN = `(min-width: ${BREAKPOINT_LG_PX}px)`;

/**
 * Sélecteurs CSS pour la fermeture au clic extérieur et l’accessibilité ciblée.
 *
 * @type {{ appSidebar: string, toggleSidebar: string }}
 */
export const NAV_SELECTORS = {
    appSidebar: 'aside[data-kz-nav-app-sidebar]',
    toggleSidebar: '[data-kz-nav-toggle-sidebar]',
};

/**
 * Largeur du panneau latéral principal (menu app). Aligné avec `Main` (`left-64`) et `lg:w-64` en classes Tailwind statiques.
 *
 * @type {string}
 */
export const LAYOUT_APP_SIDEBAR_WIDTH_CLASS = 'w-64';

/**
 * Décalage du `main` / header quand la sidebar est ouverte en desktop.
 *
 * @type {string}
 */
export const LAYOUT_APP_SIDEBAR_OFFSET_LEFT_CLASS = 'left-64';

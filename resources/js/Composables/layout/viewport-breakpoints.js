/**
 * Breakpoints alignés sur Tailwind v4 (thème par défaut) : **md = 768px**, **lg = 1024px**.
 * Toute logique JS « mobile / tablette / desktop » pour les layouts doit réutiliser ces constantes
 * ou les `MEDIA_QUERY_*` associées pour rester cohérent avec les utilitaires `md:` / `lg:`.
 *
 * ## Matrice chrome (shell app)
 *
 * | Mode | Largeur | Header | Dock bas | Toggle flottant | Sidebar |
 * | --- | --- | --- | --- | --- | --- |
 * | Mobile (`isMobile`) | &lt; md (768) | masqué | visible | masqué (menu dans le dock) | drawer overlay |
 * | Tablette (`isTablet`) | md → &lt; lg | visible | masqué | visible | drawer overlay |
 * | Desktop (`isDesktop`) | ≥ lg | visible | masqué | visible | panneau fixe |
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

/**
 * Classes Tailwind littérales — chrome mobile (&lt; md) vs tablette/desktop.
 * Pas de construction dynamique (Tailwind purge).
 */

/** Affiche le dock bas uniquement en mobile (`max-md`) */
export const LAYOUT_MOBILE_DOCK_VISIBLE_CLASS = 'fixed bottom-0 left-0 right-0 z-50 hidden max-md:block';

/** Masque le footer desktop en mobile */
export const LAYOUT_DESKTOP_FOOTER_HIDDEN_ON_MOBILE_CLASS = 'max-md:hidden';

/** Masque les toggles flottants quand le dock mobile est visible */
export const LAYOUT_FLOATING_TOGGLE_HIDDEN_ON_MOBILE_CLASS = 'max-md:hidden';

/**
 * Padding bas du contenu scrollable pour ne pas passer sous le dock
 * (hauteur dock ~4.5rem + safe-area).
 */
export const LAYOUT_MAIN_CONTENT_MOBILE_BOTTOM_PAD_CLASS =
    'max-md:pb-[calc(5.5rem+env(safe-area-inset-bottom,0px))]';

/**
 * Offset bas pour FAB / overlays au-dessus du dock mobile.
 * Desktop : `md:bottom-5` ; mobile : au-dessus du dock.
 */
export const LAYOUT_FAB_BOTTOM_OFFSET_CLASS =
    'bottom-[calc(5.25rem+env(safe-area-inset-bottom,0px))] md:bottom-5';

/**
 * Offset bas pour bandeaux fixes (cookies, sélection tableau) au-dessus du dock.
 */
export const LAYOUT_FIXED_BANNER_BOTTOM_OFFSET_CLASS =
    'bottom-[calc(5.25rem+env(safe-area-inset-bottom,0px))] md:bottom-4';

/**
 * Pied sticky (formulaires) au-dessus du dock mobile ; collé au bas dès md.
 */
export const LAYOUT_STICKY_ABOVE_MOBILE_DOCK_CLASS =
    'bottom-[calc(5.25rem+env(safe-area-inset-bottom,0px))] md:bottom-0';

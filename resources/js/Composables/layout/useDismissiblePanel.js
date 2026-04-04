import { watch, nextTick, onUnmounted } from 'vue';
import { NAV_SELECTORS } from '@/Composables/layout/viewport-breakpoints';

/**
 * Écoute les clics document pour fermer un panneau (drawer, popover plein écran, etc.)
 * lorsque le clic est **en dehors** du panneau et des zones exclues (toggle, etc.).
 *
 * @param {object} options
 * @param {import('vue').Ref<boolean>} options.isOpen - Panneau ouvert
 * @param {import('vue').Ref<boolean> | import('vue').ComputedRef<boolean>} options.isEnabled - Activer l’écoute (ex. mode overlay seulement)
 * @param {() => void} options.onDismiss - Fermeture (ex. `closeSidebar`)
 * @param {string} [options.panelSelector] - Sélecteur racine du panneau (défaut : `NAV_SELECTORS.appSidebar`)
 * @param {string} [options.toggleSelector] - Sélecteur des contrôles (défaut : `NAV_SELECTORS.toggleSidebar`)
 * @param {string[]} [options.extraExcludeSelectors] - Autres racines à ignorer
 *
 * @example
 * useDismissiblePanel({
 *   isOpen: isSidebarOpen,
 *   isEnabled: isMobileMode,
 *   onDismiss: closeSidebar,
 * });
 */
export function useDismissiblePanel(options) {
    const {
        isOpen,
        isEnabled,
        onDismiss,
        panelSelector = NAV_SELECTORS.appSidebar,
        toggleSelector = NAV_SELECTORS.toggleSidebar,
        extraExcludeSelectors = [],
    } = options;

    function handleDocumentClick(event) {
        if (!isOpen.value || !isEnabled.value) {
            return;
        }
        const target = event.target;
        if (!target || typeof target.closest !== 'function') {
            return;
        }
        if (target.closest(panelSelector)) {
            return;
        }
        if (target.closest(toggleSelector)) {
            return;
        }
        for (const sel of extraExcludeSelectors) {
            if (sel && target.closest(sel)) {
                return;
            }
        }
        onDismiss();
    }

    function bind() {
        document.addEventListener('click', handleDocumentClick);
    }

    function unbind() {
        document.removeEventListener('click', handleDocumentClick);
    }

    watch(
        [isOpen, isEnabled],
        () => {
            nextTick(() => {
                unbind();
                if (isOpen.value && isEnabled.value) {
                    bind();
                }
            });
        },
        { immediate: true }
    );

    onUnmounted(() => {
        unbind();
    });
}

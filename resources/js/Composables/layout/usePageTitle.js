import { computed, ref, watch } from "vue";

const pageTitle = ref(import.meta.env.VITE_APP_NAME);
const appName = import.meta.env.VITE_APP_NAME;
const pageHeaderCount = ref(0);

/**
 * Titre de la page courante (onglet du navigateur + header global).
 *
 * @description
 * `PageHeader` appelle `setPageTitle` et signale sa présence via `registerPageHeader()` :
 * le header global masque alors son propre titre pour éviter le doublon.
 *
 * @example
 * const { setPageTitle } = usePageTitle();
 * setPageTitle('Langues');
 */
export function usePageTitle() {
    const setPageTitle = (newTitle) => {
        pageTitle.value = newTitle;
    };

    /** @returns {() => void} Désenregistrement */
    const registerPageHeader = () => {
        pageHeaderCount.value += 1;
        let done = false;
        return () => {
            if (done) return;
            done = true;
            pageHeaderCount.value = Math.max(0, pageHeaderCount.value - 1);
        };
    };

    watch(pageTitle, (newTitle) => {
        // Met à jour le titre du document
        document.title = newTitle ? `${newTitle} - ${appName}` : appName;
    });

    return {
        pageTitle,
        setPageTitle,
        hasPageHeader: computed(() => pageHeaderCount.value > 0),
        registerPageHeader,
    };
}

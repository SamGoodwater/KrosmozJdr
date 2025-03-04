import { ref, watch } from "vue";

const pageTitle = ref(import.meta.env.VITE_APP_NAME);
const appName = import.meta.env.VITE_APP_NAME;

export function usePageTitle() {
    const setPageTitle = (newTitle) => {
        pageTitle.value = newTitle;
    };

    watch(pageTitle, (newTitle) => {
        // Met à jour le titre du document
        document.title = newTitle ? `${newTitle} - ${appName}` : appName;
    });

    return {
        pageTitle,
        setPageTitle,
    };
}

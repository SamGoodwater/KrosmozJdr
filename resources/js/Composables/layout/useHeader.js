import { ref, onMounted, onUnmounted } from "vue";

const isHeaderOpen = ref(true);

export function useHeader() {
    // Détection desktop (lg+)
    const isDesktop = () => {
        return window.matchMedia('(min-width: 1024px)').matches;
    };
    // Affichage auto selon breakpoint
    const updateHeaderVisibility = () => {
        isHeaderOpen.value = isDesktop();
    };
    // Toggle manuel (ex : dock)
    const toggleHeader = () => {
        isHeaderOpen.value = !isHeaderOpen.value;
    };
    // Raccourci clavier ALT+H (optionnel)
    const handleKeydown = (event) => {
        if (event.altKey && event.key === "h") {
            toggleHeader();
        }
    };
    onMounted(() => {
        updateHeaderVisibility();
        window.addEventListener("resize", updateHeaderVisibility);
        window.addEventListener("keydown", handleKeydown);
    });
    onUnmounted(() => {
        window.removeEventListener("resize", updateHeaderVisibility);
        window.removeEventListener("keydown", handleKeydown);
    });
    return {
        isHeaderOpen,
        toggleHeader,
        isDesktop,
    };
}

import { ref, onMounted, onUnmounted } from "vue";
import { useDevice } from "@/Composables/layout/useDevice";

const isSidebarOpen = ref(true); // Par défaut fermée sur mobile/tablette

export function useSidebar() {
    const { isMobile, isTablet, isDesktop } = useDevice();
    // Ouvre la sidebar (drawer DaisyUI)
    const openSidebar = () => {
        isSidebarOpen.value = true;
    };
    // Ferme la sidebar (drawer DaisyUI)
    const closeSidebar = () => {
        isSidebarOpen.value = false;
    };
    // Toggle (mobile/tablette)
    const toggleSidebar = () => {
        isSidebarOpen.value = !isSidebarOpen.value;
    };
    // Raccourci clavier ALT+G (optionnel)
    const handleKeydown = (event) => {
        if (event.altKey && event.key === "g") {
            toggleSidebar();
        }
    };
    onMounted(() => {
        window.addEventListener("keydown", handleKeydown);
    });
    onUnmounted(() => {
        window.removeEventListener("keydown", handleKeydown);
    });
    return {
        isSidebarOpen,
        openSidebar,
        closeSidebar,
        toggleSidebar,
        isDesktop,
        isTablet,
        isMobile,
    };
}

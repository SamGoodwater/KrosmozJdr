import { ref, onMounted, onUnmounted } from "vue";
import { useDevice } from "@/Composables/layout/useDevice";

const isSidebarOpen = ref(false); // Par défaut fermée, sera ajusté selon l'appareil

export function useSidebar() {
    const { isMobile, isTablet, isDesktop } = useDevice();
    
    // Initialisation responsive de l'état de la sidebar
    const initializeSidebarState = () => {
        if (isDesktop.value) {
            isSidebarOpen.value = true; // Ouverte par défaut sur desktop
        } else {
            isSidebarOpen.value = false; // Fermée par défaut sur mobile/tablette
        }
    };
    
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
    
    onMounted(() => {
        // Initialisation après le montage pour s'assurer que useDevice est prêt
        initializeSidebarState();
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

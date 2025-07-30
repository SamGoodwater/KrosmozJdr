import { ref, onMounted, onUnmounted } from "vue";

const isMobile = ref(window.innerWidth < 768);
const isTablet = ref(window.innerWidth >= 768 && window.innerWidth < 1024);
const isDesktop = ref(window.innerWidth >= 1024);

function updateDeviceFlags() {
    const width = window.innerWidth;
    isMobile.value = width < 768;
    isTablet.value = width >= 768 && width < 1024;
    isDesktop.value = width >= 1024;
}

export function useDevice() {
    onMounted(() => {
        window.addEventListener("resize", updateDeviceFlags);
        // Initialisation immédiate
        updateDeviceFlags();
    });
    
    onUnmounted(() => {
        window.removeEventListener("resize", updateDeviceFlags);
    });
    
    return {
        isMobile,
        isTablet,
        isDesktop,
        updateDeviceFlags, // Exposer la fonction pour forcer la mise à jour si nécessaire
    };
}

import { ref, onMounted, onUnmounted } from "vue";

const isMobile = ref(window.innerWidth < 768);
const isTablet = ref(window.innerWidth >= 768 && window.innerWidth < 1024);
const isDesktop = ref(window.innerWidth >= 1024);

function updateDeviceFlags() {
    isMobile.value = window.innerWidth < 768;
    isTablet.value = window.innerWidth >= 768 && window.innerWidth < 1024;
    isDesktop.value = window.innerWidth >= 1024;
}

export function useDevice() {
    onMounted(() => {
        window.addEventListener("resize", updateDeviceFlags);
        updateDeviceFlags();
    });
    onUnmounted(() => {
        window.removeEventListener("resize", updateDeviceFlags);
    });
    return {
        isMobile,
        isTablet,
        isDesktop,
    };
}

<script setup>
/**
* Main Layout (Atomic Design, DaisyUI)
*
* @description
* Layout principal du projet KrosmozJDR, refactorisé pour respecter l'atomic design.
* - Utilise l'atom Container pour le contenu principal (main)
* - Structure : background, Header, Aside, Container (main), Footer, notifications
* - Responsive, glassmorphism, accessibilité
* - Notifications centralisées via l'organism NotificationContainer (plus de slots de notifications)
*
* @see Container, Header, Aside, Footer, NotificationContainer
*
* @slot default - Contenu principal de la page
*/
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Composants
import Header from "@/Pages/Layouts/Header.vue";
import Aside from "@/Pages/Layouts/Aside.vue";
import Footer from "@/Pages/Layouts/Footer.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import NotificationContainer from "@/Pages/Organismes/feedback/NotificationContainer.vue";
import ToggleSidebar from "@/Pages/Molecules/layout/ToggleSidebar.vue";
import { useHeader } from "@/Composables/layout/useHeader";
import { useSidebar } from "@/Composables/layout/useSidebar";
import { useDevice } from "@/Composables/layout/useDevice";

// Centralisation des classes Tailwind pour le layout
const ASIDE_WIDTH_CLASS = 'w-64'      // 16rem = 256px
const HEADER_HEIGHT_CLASS = 'h-18'    // 
const OFFSET_LEFT_CLASS = 'left-64'
const OFFSET_TOP_CLASS = 'top-18'

const { isHeaderOpen } = useHeader();
const { isMobile } = useDevice();
const { isSidebarOpen, toggleSidebar } = useSidebar();

function handleResize() {
    if (isMobile.value && isSidebarOpen.value) {
        toggleSidebar();
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize)
})
onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})
</script>

<template>
    <div class="relative min-h-screen max-w-screen overflow-x-hidden flex flex-col">
        <!-- Background (image + fallback dégradé) -->
        <div class="fixed inset-0 z-[-1] brightness-50 blur-2xl">
            <div class="z-1 bg-cover bg-center bg-no-repeat absolute inset-0"
                style="background-image: url('storage/images/backgrounds/background.jpg');" aria-hidden="true">
            </div>
            <div
                class="z-2 bg-gradient-to-br from-primary/30 via-base-200/30 to-base-100/30 bg-cover bg-center bg-no-repeat absolute inset-0">
            </div>
        </div>

        <!-- Header -->
        <Header
            :class="['fixed top-0 right-0 z-30', HEADER_HEIGHT_CLASS, isSidebarOpen ? OFFSET_LEFT_CLASS : 'left-0']" />

        <!-- Toggle Aside -->
        <ToggleSidebar
            :class="['fixed top-6 z-50', isMobile.value ? 'hidden' : '', isSidebarOpen ? OFFSET_LEFT_CLASS + ' ml-[-3rem]' : 'left-2']" />

        <!-- Sidebar (Drawer DaisyUI gère tout) -->
        <Aside v-show="isSidebarOpen" :class="['z-40 top-0 left-0 bottom-0', ASIDE_WIDTH_CLASS]"
            style="position: fixed;" />

        <!-- Main content -->
        <main :class="[
            isSidebarOpen ? OFFSET_LEFT_CLASS : 'left-0',
            isHeaderOpen ? OFFSET_TOP_CLASS : 'top-0',
            'relative min-h-screen max-w-screen-xl transition-all flex-1 w-full px-2 sm:px-4 pt-14 pb-14 mx-auto'
        ]">
            <Container fluid>
                <div class="flex justify-center mx-auto">
                    <slot />
                </div>
            </Container>
        </main>

        <!-- Footer -->
        <Footer class="w-full z-30" />

        <!-- Notifications -->
        <NotificationContainer />
    </div>
</template>

<style scoped>
/* Plus de .background custom, tout est géré par Tailwind dans le template */
</style>

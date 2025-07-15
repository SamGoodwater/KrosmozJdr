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
    <div class="relative min-h-screen w-full overflow-x-hidden">
        <!-- Background (image + fallback dégradé) -->
        <div class="background">
            <div class="background-image" aria-hidden="true"></div>
            <div class="background-filter--1" aria-hidden="true"></div>
            <div class="background-filter--2" aria-hidden="true"></div>
        </div>

        <!-- Header -->
        <Header
            :class="['!fixed top-0 right-0 z-30 max-sm:hidden', HEADER_HEIGHT_CLASS, isSidebarOpen ? OFFSET_LEFT_CLASS : 'left-0']" />

        <!-- Toggle Aside -->
        <ToggleSidebar
            :class="['fixed top-6 z-50 max-sm:hidden', isMobile.value ? 'hidden' : '', isSidebarOpen ? OFFSET_LEFT_CLASS + ' ml-[-3rem]' : 'left-2']" />

        <!-- Sidebar (Drawer DaisyUI gère tout) -->
        <Aside v-show="isSidebarOpen" :class="['z-40 top-0 left-0 bottom-0', ASIDE_WIDTH_CLASS]"
            style="position: fixed;" />

        <!-- Main content -->
        <main :class="[
            'fixed',
            isSidebarOpen ? OFFSET_LEFT_CLASS : 'left-0',
            isHeaderOpen ? OFFSET_TOP_CLASS : 'top-0',
            'right-0 bottom-0 overflow-y-auto'
        ]">
            <div class="min-h-full flex flex-col">
                <!-- Contenu principal centré -->
                <div class="flex-1 flex items-center justify-center p-4">
                    <div class="w-full max-w-4xl">
                        <Container fluid>
                            <slot />
                        </Container>
                    </div>
                </div>
                
                <!-- Footer -->
                <Footer :class="['relative z-30']" />
            </div>
        </main>

        <!-- Notifications -->
        <NotificationContainer />
    </div>
</template>

<style scoped lang="scss">
.background {
    position: fixed;
    z-index: -1;
    inset: 0;
    filter: blur(24px) brightness(0.5);
    background-color: var(--color-stone-900);

    &-image {
        position: absolute;
        z-index: 1;
        inset: 0;
        background-image: url('storage/images/backgrounds/background.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.8;
    }

    &-filter--1 {
        position: absolute;
        z-index: 2;
        inset: 0;
        background-image: linear-gradient(
            195deg,
            var(--color-primary-400) 0%,
            var(--color-primary-500) 3%,
            var(--color-primary-600) 10%,
            var(--color-primary-700) 25%,
            var(--color-primary-800) 40%,
            var(--color-primary-900) 62%,
            var(--color-primary-950) 100%
        );
        opacity: 0.2;
    }

    &-filter--2 {
        position: absolute;
        z-index: 3;
        inset: 0;
        opacity: 0.2;
        background: radial-gradient(circle, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 60%, mix-color(in oklch var(--color-primary-900) 100%));
    }
}
</style>

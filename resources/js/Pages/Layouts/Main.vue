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
import { useSidebar } from "@/Composables/layout/useSidebar";

// Composants
import Header from "@/Pages/Layouts/Header.vue";
import Aside from "@/Pages/Layouts/Aside.vue";
import Footer from "@/Pages/Layouts/Footer.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import NotificationContainer from "@/Pages/Organismes/feedback/NotificationContainer.vue";

const { isSidebarOpen } = useSidebar();
</script>

<template>
    <div class="relative min-h-screen max-w-screen">
        <!-- Background avec effet de flou -->
        <div class="background fixed w-screen h-screen overflow-hidden">
            <div class="background-square-1"></div>
            <div class="background-square-2"></div>
            <div class="background-square-3"></div>
            <div class="background-square-4"></div>
        </div>

        <!-- Header -->
        <Header :class="[isSidebarOpen ? 'ml-64' : 'ml-0']" class="z-10 fixed max-sm:ml-0 top-0 w-fit-available" />

        <!-- Sidebar -->
        <Aside class="z-20" />

        <!-- Contenu principal -->
        <main :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="relative max-sm:ml-0 flex flex-col items-center z-0 w-full h-full overflow-x-hidden">
            <Container fluid class="w-full h-full">
                <div
                    class="mt-20 max-md:mt-18 max-sm:mt-16 mb-26 max-md:mb-24 max-sm:mb-20 mx-16 max-xl:mx-30 max-lg:mx-20 max-md:mx-4 max-sm:mx-1 w-full h-full flex justify-center">
                    <slot />
                </div>
            </Container>
        </main>

        <!-- Footer -->
        <Footer :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 absolute max-sm:fixed max-sm:ml-0 bottom-0 w-full h-fit" />

        <!-- Notifications centralisées (organism) -->
        <NotificationContainer />
    </div>
</template>

<style scoped>
.background {
    filter: blur(100px);
    z-index: -1;
    background-image: linear-gradient(
        195deg,
        #1e40af 0%,
        #1e3a8a 3%,
        #172554 10%,
        #1e293b 25%,
        #1e293b 40%,
        #0f172a 62%,
        #020617 81%,
        #020617 100%
    );
}

.background-square-1 {
    position: absolute;
    background-color: var(--color-stone-900);
    top: 0%;
    left: 0%;
    width: 20rem;
    height: 100vh;
    opacity: 0.1;
}

.background-square-2 {
    position: absolute;
    background-color: var(--color-cyan-950);
    border-radius: 50%;
    top: 40%;
    left: 40%;
    width: 30rem;
    height: 30rem;
    opacity: 0.8;
}

.background-square-3 {
    position: absolute;
    background-color: var(--color-stone-950);
    top: 0vh;
    left: 0;
    width: 100vw;
    height: 70px;
    opacity: 0.4;
}

.background-square-4 {
    position: absolute;
    background-color: var(--color-stone-950);
    top: 90vh;
    left: 0;
    width: 100vw;
    height: 70px;
    opacity: 0.4;
}
</style>

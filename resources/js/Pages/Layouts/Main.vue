<script setup>
import Header from "@/Pages/Layouts/Header.vue";
import Aside from "@/Pages/Layouts/Aside.vue";
import Footer from "@/Pages/Layouts/Footer.vue";
import { useSidebar } from "@/Composables/useSidebar";

const { isSidebarOpen } = useSidebar();
</script>

<template>
    <div class="relative min-h-[100vh] max-w-[100vw]">
        <div class="background fixed w-screen h-screen overflow-hidden">
            <div class="background-square-1"></div>
            <div class="background-square-2"></div>
            <div class="background-square-3"></div>
            <div class="background-square-4"></div>
        </div>

        <Header
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 fixed max-sm:ml-0 top-0 w-fit-available"
        />

        <Aside class="z-20" />

        <main
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="relative max-sm:ml-0 flex flex-col align-items-center z-0 w-fit-available h-fit-available overflow-x-hidden"
        >
            <div
                class="mt-20 max-md:mt-18 max-sm:mt-16 mb-26 max-md:mb-24 max-sm:mb-20 mx-16 max-xl:mx-30 max-lg:mx-20 max-md:mx-4 max-sm:mx-1 w-fit-available h-fit-available flex justify-center"
            >
                <slot />
            </div>
        </main>

        <Footer
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 absolute max-sm:fixed max-sm:ml-0 bottom-0 w-fit-available h-fit-available"
        />

        <!-- Conteneurs de notifications -->
        <div id="notifications-top-start" class="fixed top-4 left-4 flex flex-col gap-2 z-[9999] pointer-events-none min-w-[200px] max-w-[400px]">
            <TransitionGroup name="notification-list">
                <slot name="notifications-top-start" />
            </TransitionGroup>
        </div>
        <div id="notifications-top-end" class="fixed top-4 right-4 flex flex-col gap-2 z-[9999] pointer-events-none min-w-[200px] max-w-[400px]">
            <TransitionGroup name="notification-list">
                <slot name="notifications-top-end" />
            </TransitionGroup>
        </div>
        <div id="notifications-bottom-start" class="fixed bottom-4 left-4 flex flex-col gap-2 z-[9999] pointer-events-none min-w-[200px] max-w-[400px]">
            <TransitionGroup name="notification-list">
                <slot name="notifications-bottom-start" />
            </TransitionGroup>
        </div>
        <div id="notifications-bottom-end" class="fixed bottom-4 right-4 flex flex-col gap-2 z-[9999] pointer-events-none min-w-[200px] max-w-[400px]">
            <TransitionGroup name="notification-list">
                <slot name="notifications-bottom-end" />
            </TransitionGroup>
        </div>
    </div>

</template>

<style scoped lang="scss">
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
}

.notification-list-move, /* s'applique aux éléments en cours de déplacement */
.notification-list-enter-active,
.notification-list-leave-active {
    transition: all 0.3s ease;
}

.notification-list-enter-from,
.notification-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

/* s'assure que les éléments sortants sont retirés du flux du document pour que les animations fonctionnent correctement */
.notification-list-leave-active {
    position: absolute;
}
</style>

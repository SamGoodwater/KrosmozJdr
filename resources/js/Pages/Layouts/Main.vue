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
                class="mt-20 max-md:mt-18 max-sm:mt-16 mb-26 max-md:mb-24 max-sm:mb-20 mx-16 max-xl:mx-30 max-lg:mx-24 max-md:mx-4 max-sm:mx-2 w-fit-available h-fit-available flex justify-center"
            >
                <slot />
            </div>
        </main>

        <Footer
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 absolute max-sm:fixed max-sm:ml-0 bottom-0 w-fit-available h-fit-available"
        />
    </div>
    <!--
------------------------------------ SAFELIST CSS ------------------------------------
- à supprimer lorsque j'aurai trouvé une solution pour gérer les variables css dynamiques
-->
    <div
        class="bg-secondary-300/20 bg-secondary-300/30 bg-secondary-300/40 bg-secondary-300/50 bg-secondary-300/60 bg-secondary-300/70 bg-secondary-300/80 bg-secondary-700/80 hidden badge-xs badge-sm badge-md badge-lg badge-xl badge-2xl bg-fuchsia-700 border-fuchsia-700 text-fuchsia-700 bg-blue-500 border-blue-500 text-blue-500 bg-fuchsia-500 border-fuchsia-500 text-fuchsia-500 bg-blue-700 border-blue-700 text-blue-700 bg-base-900/80 bg-base-900/40 bg-primary border-primary text-primary bg-secondary border-secondary text-secondary bg-success border-success text-success bg-error border-error text-error bg-neutral border-neutral text-neutral bg-info border-info text-info bg-warning border-warning text-warning bg-secondary-300/10 border-secondary-100/10 rounded-full rounded-none rounded-sm rounded-md rounded-lg rounded-xl rounded-2xl rounded-3xl shadow-sm shadow-md shadow-lg shadow-xl shadow-2xl shadow-3xl shadow-4xl shadow-5xl backdrop-blur-sm backdrop-blur-md backdrop-blur-lg backdrop-blur-xl backdrop-blur-2xl backdrop-blur-3xl backdrop-blur-4xl backdrop-blur-5xl w-6 h-6 w-8 h-8 w-10 h-10 w-12 h-12 w-14 h-14 w-16 h-16 w-18 h-18 w-20 h-20 w-22 h-22 w-24 h-24 w-26 h-26 w-28 h-28 w-30 h-30 w-32 h-32 w-34 h-34 w-36 h-36 w-38 h-38 w-40 h-40 w-42 h-42 w-44 h-44 w-46 h-46 w-48 h-48 w-50 h-50 w-52 h-52 w-54 h-54 w-56 h-56 w-58 h-58"
    ></div>
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
</style>

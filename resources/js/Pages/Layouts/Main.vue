<script setup>
import Header from "@/Pages/Layouts/Header.vue";
import Aside from "@/Pages/Layouts/Aside.vue";
import Footer from "@/Pages/Layouts/Footer.vue";
import { useSidebar } from "@/Composables/useSidebar";

const { isSidebarOpen } = useSidebar();
</script>

<template>
    <div class="relative min-h-[100vh] max-w-[100vw]">
        <Header
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 fixed max-sm:ml-0 top-0 w-fit-available"
        />

        <Aside class="z-20" />

        <main
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="relative max-sm:ml-0 flex flex-col align-items-center z-0 w-fit-available h-fit-available overflow-x-hidden"
        >
            <div class="
                mt-20
                max-md:mt-18
                max-sm:mt-16
                mb-26
                max-md:mb-24
                max-sm:mb-20
                mx-16
                max-xl:mx-30
                max-lg:mx-24
                max-md:mx-4
                max-sm:mx-2
                w-fit-available h-fit-available flex justify-center">
                <slot />
            </div>
            <div
                class="background fixed w-screen h-screen overflow-hidden"
            ></div>
        </main>

        <Footer
            :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 absolute max-sm:fixed max-sm:ml-0 bottom-0 w-fit-available h-fit-available"
        />
    </div>
</template>

<style scoped>
.background {
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
</style>

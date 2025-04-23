<script setup>
import SearchInput from "@/Pages/Layouts/Molecules/SearchInput.vue";
import ToggleSidebar from "@/Pages/Layouts/Molecules/ToggleSidebar.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import { ref, onMounted } from "vue";
import { useSidebar } from "@/Composables/useSidebar";
import { MediaManager } from "@/Utils/MediaManager";

const { isSidebarOpen } = useSidebar();
const appSlogan = ref(import.meta.env.VITE_APP_SLOGAN);
const logo = ref("");

onMounted(async () => {
    try {
        logo.value = await MediaManager.get('logos/logo', 'image');
    } catch (error) {
        console.error("Erreur lors du chargement du logo:", error);
    }
});

const navItems = [
    {
        route: "home",
        label: "Accueil",
        icon: "fa-solid fa-house",
        active: (page) => page.component.includes('Home')
    },
    {
        route: "",
        label: "Pages",
        icon: "fa-solid fa-file-lines",
        active: (page) => page.component.includes('/page')
    },
    {
        route: "",
        label: "Créer une page",
        icon: "fa-solid fa-plus",
        active: (page) => page.component.includes('/page/create')
    }
];

const footerItems = [
    {
        route: "contribute",
        label: "Contribuer",
        icon: "fa-solid fa-handshake-angle",
        tooltip: "Tous les liens pour contribuer au projet KrosmozJDR"
    },
    {
        route: "tools",
        label: "Outils",
        icon: "fa-solid fa-dice",
        tooltip: "En cours de développement"
    },
    {
        route: "campaigns",
        label: "Campagnes",
        icon: "fa-solid fa-map",
        tooltip: "En cours de développement"
    }
];
</script>

<template>
    <div>
        <aside
            id="menuSidebar"
            :class="[
                isSidebarOpen ? 'sidebar-on' : 'sidebar-off',
                'px-2',
                'pt-4',
                'fixed',
                'top-0',
                'left-0',
                'bottom-0',
                'z-40',
                'w-64',
                'transition-transform',
                '-translate-x-full',
                'sm:translate-x-0',
                'bg-base-200',
                'flex',
                'flex-col',
                'justify-between',
                'backdrop-blur-xl',
            ]"
            aria-label="Sidenav"
        >
            <ToggleSidebar size="xs" class="absolute right-2" />

            <div>
                <BaseTooltip :tooltip="{ custom: true }" tooltip-position="bottom-center">
                    <Route class="hover:scale-105 focus:scale-95" route="home">
                        <figure>
                            <img
                                class="w-auto px-14"
                                :src="logo"
                                :alt="`Logo de ${appSlogan}`"
                            />
                        </figure>
                    </Route>

                    <template #tooltip>
                        Aller à la page d'accueil
                    </template>
                </BaseTooltip>

                <nav id="nav" class="my-10">
                    <ul>
                        <li v-for="item in navItems" :key="item.label" class="my-2">
                            <Route
                                :route="item.route"
                                class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                                :class="[
                                    item.active($page) ? 'bg-primary/10' : ''
                                ]"
                            >
                                <i :class="item.icon" class="mr-2"></i>
                                {{ item.label }}
                            </Route>
                        </li>
                    </ul>
                </nav>
            </div>

            <div id="footer">
                <SearchInput class="max-sm:block hidden" />

                <div class="flex gap-1 flex-nowrap justify-around btm-nav bg-transparent px-1 py-2">
                    <BaseTooltip
                        v-for="item in footerItems"
                        :key="item.label"
                        :tooltip="{ custom: true }"
                        tooltip-position="top"
                    >
                        <Route
                            class="pb-1 text-secondary-400 hover:text-primary-200 relative cursor-pointer before:bg-primary-300 before:absolute before:-bottom-0 before:-left-0 before:block before:h-[1px] before:w-full before:origin-bottom-right before:scale-x-0 before:transition before:duration-300 before:ease-in-out hover:before:origin-bottom-left hover:before:scale-x-100"
                            :route="item.route"
                        >
                            <button>
                                <i :class="item.icon"></i><br />
                                <span class="btm-nav-label">{{ item.label }}</span>
                            </button>
                        </Route>

                        <template #tooltip>
                            <span>{{ item.tooltip }}</span>
                        </template>
                    </BaseTooltip>
                </div>
            </div>
        </aside>
    </div>
</template>

<style scoped>
aside {
    /* background-image: linear-gradient(
        195deg,
        #1e40af 0%,
        #1e3a8a 3%,
        #172554 10%,
        #1e293b 25%,
        #1e293b 40%,
        #0f172a 62%,
        #020617 81%,
        #020617 100%
    ); */
}

#menuSidebar {
    transition: transform 0.3s ease-in-out;
}

.sidebar-on {
    transform: translateX(0);
}

.sidebar-off {
    transform: translateX(-100%);
}
</style>

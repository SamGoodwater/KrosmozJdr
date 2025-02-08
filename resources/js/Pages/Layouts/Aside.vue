<script setup>
import searchInput from "@/Pages/Layouts/Molecules/searchInput.vue";
import toggleSidebar from "@/Pages/Layouts/Molecules/ToggleSidebar.vue";
import Tooltips from "@/Pages/Atoms/feedback/tooltips.vue";
import { ref } from "vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import { useSidebar } from "@/Composables/useSidebar";

const { isSidebarOpen } = useSidebar();
const appSlogan = ref(import.meta.env.VITE_APP_SLOGAN);
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
            ]"
            aria-label="Sidenav"
        >
            <toggleSidebar size="xs" class="absolute right-2" />

            <div>
                <Tooltips placement="bottom-center">
                    <template #reference>
                        <Route
                            class="hover:scale-105 focus:scale-95"
                            route="home"
                        >
                            <figure>
                                <img
                                    class="w-auto px-14"
                                    src="storage/logos/logo.webp"
                                    alt="Logo de {{ appName }}"
                                />
                            </figure>
                        </Route>
                    </template>
                    <template #content> Aller à la page d'accueil </template>
                </Tooltips>

                <div id="nav" class="my-10">
                    <ul>
                        <li class="my-2">
                            <Route
                                route="home"
                                class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                                :class="[
                                    $page.component.includes('Home')
                                        ? 'bg-primary/10'
                                        : '',
                                ]"
                            >
                                Accueil
                            </Route>
                        </li>
                        <li class="my-2">
                            <Route
                                route=""
                                class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                                :class="[
                                    $page.component.includes('/page')
                                        ? 'bg-primary/10'
                                        : '',
                                ]"
                                >Pages
                            </Route>
                        </li>
                        <li class="my-2">
                            <Route
                                route=""
                                class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                                :class="[
                                    $page.component.includes('/page/create')
                                        ? 'bg-primary/10'
                                        : '',
                                ]"
                                >Créer une page</Route
                            >
                        </li>
                    </ul>
                </div>
            </div>

            <div id="footer">
                <searchInput class="max-sm:block hidden" />

                <div
                    class="flex gap-1 flex-nowrap justify-around btm-nav bg-transparent px-1 py-2"
                >
                    <Tooltips placement="top">
                        <template #reference>
                            <Route
                                class="pb-1 text-secondary-400 hover:text-primary-200 relative cursor-pointer before:bg-primary-300 before:absolute before:-bottom-0 before:-left-0 before:block before:h-[1px] before:w-full before:origin-bottom-right before:scale-x-0 before:transition before:duration-300 before:ease-in-out hover:before:origin-bottom-left hover:before:scale-x-100"
                                route="contribute"
                            >
                                <button>
                                    <i class="fa-solid fa-handshake-angle"></i
                                    ><br />
                                    <span class="btm-nav-label"
                                        >Contribuer</span
                                    >
                                </button>
                            </Route>
                        </template>
                        <template #label>
                            <span
                                >Tout les liens pour<br /><b
                                    >contribuer au projet KrosmozJDR</b
                                ></span
                            >
                        </template>
                    </Tooltips>
                    <Tooltips placement="top">
                        <template #reference>
                            <Route
                                class="pb-1 text-secondary-400 hover:text-primary-200 relative cursor-pointer before:bg-primary-300 before:absolute before:-bottom-0 before:-left-0 before:block before:h-[1px] before:w-full before:origin-bottom-right before:scale-x-0 before:transition before:duration-300 before:ease-in-out hover:before:origin-bottom-left hover:before:scale-x-100"
                                route="contribute"
                            >
                                <button>
                                    <i class="fa-solid fa-dice"></i><br />
                                    <span class="btm-nav-label">Outils</span>
                                </button>
                            </Route>
                        </template>
                        <template #label>
                            <span>En cours de développement</span>
                        </template>
                    </Tooltips>
                    <Tooltips placement="top">
                        <template #reference>
                            <Route
                                class="pb-1 text-secondary-400 hover:text-primary-200 relative cursor-pointer before:bg-primary-300 before:absolute before:-bottom-0 before:-left-0 before:block before:h-[1px] before:w-full before:origin-bottom-right before:scale-x-0 before:transition before:duration-300 before:ease-in-out hover:before:origin-bottom-left hover:before:scale-x-100"
                                route="contribute"
                            >
                                <button>
                                    <i class="fa-solid fa-map"></i><br />
                                    <span class="btm-nav-label">Campagnes</span>
                                </button>
                            </Route>
                        </template>
                        <template #label>
                            <span>En cours de développement</span>
                        </template>
                    </Tooltips>
                </div>
            </div>
        </aside>
    </div>
</template>

<style scoped>
aside {
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

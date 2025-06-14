<script setup>
/**
* Aside Layout (Atomic Design, DaisyUI)
*
* @description
* Layout Aside du projet KrosmozJDR, refactorisé pour n'utiliser que des atoms et molecules du design system.
* - Utilise :
* - Atom Route pour les liens
* - Atom Image pour le logo
* - Molecule Menu + atom MenuItem pour la navigation principale
* - Molecule Dock + atom DockItem pour le footer
* - Atom Icon pour toutes les icônes
* - Molecules ToggleSidebar et SearchInput
* - Structure layout dans
<aside>, tout le contenu est atomique/moleculaire
 * - Accessibilité et props transmises via les helpers du design system
 *
 * @see Menu, MenuItem, Dock, DockItem, Route, Image, Icon, ToggleSidebar, SearchInput
 */
import SearchInput from "@/Pages/Molecules/data-input/SearchInput.vue";
import ToggleSidebar from "@/Pages/Molecules/layout/ToggleSidebar.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Menu from "@/Pages/Molecules/navigation/Menu.vue";
import MenuItem from "@/Pages/Atoms/navigation/MenuItem.vue";
import Dock from "@/Pages/Molecules/navigation/Dock.vue";
import DockItem from "@/Pages/Atoms/navigation/DockItem.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { ref } from "vue";
import { useSidebar } from "@/Composables/layout/useSidebar";

const { isSidebarOpen } = useSidebar();
const appSlogan = ref(import.meta.env.VITE_APP_SLOGAN);
const logoError = ref(false);

const navItems = [
    {
        route: "home",
        label: "Accueil",
        icon: "fa-house",
        pack: "solid",
        active: (page) => page.component.includes("Home"),
    },
    {
        route: "",
        label: "Pages",
        icon: "fa-file-lines",
        pack: "solid",
        active: (page) => page.component.includes("/page"),
    },
    {
        route: "",
        label: "Créer une page",
        icon: "fa-plus",
        pack: "solid",
        active: (page) => page.component.includes("/page/create"),
    },
];

const footerItems = [
    {
        route: "contribute",
        label: "Contribuer",
        icon: "fa-handshake-angle",
        pack: "solid",
        tooltip: "Tous les liens pour contribuer au projet KrosmozJDR",
    },
    {
        route: "",
        label: "Outils",
        icon: "fa-dice",
        pack: "solid",
        tooltip: "En cours de développement",
    },
    {
        route: "",
        label: "Campagnes",
        icon: "fa-map",
        pack: "solid",
        tooltip: "En cours de développement",
    },
];
</script>

<template>
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
            <Route
                route="home"
                target="_self"
                class="hover:scale-105 focus:scale-95"
            >
                <template v-if="!logoError">
                    <Image
                        source="logos/logo.webp"
                        :alt="`Logo de ${appSlogan}`"
                        size="lg"
                        class="mx-auto"
                        @error="logoError = true"
                    />
                </template>
                <template v-else>
                    <div class="flex items-center justify-center h-16">
                        <span class="text-xl font-bold">{{ appSlogan }}</span>
                    </div>
                </template>
            </Route>

            <Menu direction="vertical" size="md" class="my-10">
                <MenuItem
                    v-for="item in navItems"
                    :key="item.label"
                    :route="item.route"
                    :icon="item.icon"
                    :pack="item.pack"
                    :active="item.active($page)"
                >
                    {{ item.label }}
                </MenuItem>
            </Menu>
        </div>

        <div id="footer">
            <SearchInput class="max-sm:block hidden" />
            <Dock size="md" class="px-1 py-2">
                <DockItem
                    v-for="item in footerItems"
                    :key="item.label"
                    :route="item.route"
                    :icon="item.icon"
                    :pack="item.pack"
                    :label="item.label"
                    :tooltip="item.tooltip"
                />
            </Dock>
        </div>
    </aside>
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

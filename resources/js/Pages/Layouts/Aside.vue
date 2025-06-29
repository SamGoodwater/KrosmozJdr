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
import Route from "@/Pages/Atoms/action/Route.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Menu from "@/Pages/Molecules/navigation/Menu.vue";
import MenuItem from "@/Pages/Atoms/navigation/MenuItem.vue";
import Dock from "@/Pages/Molecules/navigation/Dock.vue";
import DockItem from "@/Pages/Atoms/navigation/DockItem.vue";
import { ref } from "vue";

const appSlogan = ref(import.meta.env.VITE_APP_SLOGAN);
const appName = ref(import.meta.env.VITE_APP_NAME);
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
    <aside class="h-full min-h-full flex flex-col justify-between flex-nowrap glass-box-md">
        <div class="flex flex-col justify-start flex-nowrap items-center">
            <Route route="home" target="_self" class="hover:scale-105 focus:scale-95 my-5">
                <template v-if="!logoError">
                    <Image source="logos/logo.webp" :alt="`Logo de ${appName}`" size="md" class="mx-auto"
                        @error="logoError = true" />
                </template>
                <template v-else>
                    <div class="flex items-center justify-center h-16">
                        <span class="text-subtitle/80 text-sm">{{ appName }}</span>
                    </div>
                </template>
            </Route>
            <p class="m-2 text-subtitle/80 text-sm">{{ appSlogan }}</p>
            <Menu direction="vertical" size="md" class="my-5">
                <MenuItem v-for="item in navItems" :key="item.label" :route="item.route" :icon="item.icon"
                    :pack="item.pack" :active="item.active($page)">
                {{ item.label }}
                </MenuItem>
            </Menu>
        </div>
        <div id="footer">
            <Dock size="md" class="px-1 py-2 relative box-glass-t-xs">
                <DockItem v-for="item in footerItems" :key="item.label" :route="item.route" :icon="item.icon"
                    :pack="item.pack" :label="item.label" :tooltip="item.tooltip" />
            </Dock>
        </div>
    </aside>
</template>

<style scoped>
/* aside { */
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
/* } */
    .drawer-side {
        /* Pour éviter le scroll horizontal sur mobile */
        overflow-y: auto;
        overflow-x: hidden;
}
</style>

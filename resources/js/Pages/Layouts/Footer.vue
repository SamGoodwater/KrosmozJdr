<script setup>
/**
 * Footer Layout (Atomic Design, DaisyUI)
 *
 * @description
 * Layout Footer du projet KrosmozJDR, refactorisé pour n'utiliser que la molecule Footer et les atoms du design system.
 * - Utilise :
 * - Molecule Footer comme conteneur principal (slots logo, section, copyright)
 * - Atom Icon pour les icônes
 * - Atom Route pour les liens
 * - Atom Tooltip pour les tooltips
 * - Responsive : version desktop (footer classique), version mobile (dock/btm-nav)
 * - Accessibilité et props transmises via les helpers du design system
 *
 * @see Footer, Icon, Route, Tooltip
 */
import { ref } from "vue";
import FooterMolecule from "@/Pages/Molecules/navigation/Footer.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Dock from "@/Pages/Molecules/navigation/Dock.vue";
import DockItem from "@/Pages/Atoms/navigation/DockItem.vue";
import { useSidebar } from "@/Composables/layout/useSidebar";
import Image from "@/Pages/Atoms/data-display/Image.vue";

const { toggleSidebar } = useSidebar();

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appStability = ref(convertStability[import.meta.env.VITE_APP_STABILITY]);

const footerItems = [
    {
        icon: "fa-envelope",
        pack: "solid",
        href: "mailto:contact@krosmoz-jdr.fr",
        label: "contact@krosmoz-jdr.fr",
        tooltip: "Envoyer un email",
    },
    {
        icon: "fa-discord",
        pack: "brands",
        href: "https://discord.gg/XVu4VWFskj",
        label: "#XVu4VWFskj",
        tooltip: "Rejoindre notre serveur Discord",
        target: "_blank",
    },
];
</script>

<template>
    <FooterMolecule direction="vertical" center textColor="text-content" class="box-glass-t-xs" v-bind="$attrs">
        <template #logo>
            <Image source="logos/logo.webp" :alt="`Logo de ${appName}`" height="24px" class="mx-auto" @error="logoError = true" />
        </template>
        <template #section>
            <span>
                {{ appName }}
                | version {{ appVersion + " " + appStability }}
                | {{ new Date().getFullYear() }}
            </span>
            <span v-for="item in footerItems" :key="item.label" class="flex items-center gap-2">
                <Tooltip :content="item.tooltip" placement="top">
                    <Route :href="item.href" :target="item.target" class="flex items-center gap-2">
                        <Icon :source="item.icon" :pack="item.pack" :alt="item.tooltip" class="w-4 h-4" />
                        <span>{{ item.label }}</span>
                    </Route>
                </Tooltip>
            </span>
        </template>
        <template #copyright>
            {{ appDescription }}
        </template>
    </FooterMolecule>
    <!-- Mobile Footer (Dock) -->
    <div class="fixed bottom-0 left-0 z-50 max-sm:block hidden">
        <Dock size="md" class="px-1 py-2 flex justify-between box-glass-md">
            <!-- Bouton sidebar -->
            <DockItem icon="fa-bars" pack="solid" label="Menu" @click="toggleSidebar" />
            <!-- Bouton recherche (placeholder) -->
            <DockItem icon="fa-magnifying-glass" pack="solid" label="Recherche" />
            <!-- Bouton compte/utilisateur (placeholder dropdown) -->
            <DockItem icon="fa-user" pack="solid" label="Compte" />
            <!-- Items de contact -->
            <DockItem v-for="item in footerItems" :key="item.label" :icon="item.icon" :pack="item.pack"
                :label="item.label" :route="item.href" :tooltip="item.tooltip" :target="item.target" />
        </Dock>
    </div>
</template>

<style scoped>
</style>
